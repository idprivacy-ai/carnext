<?php
 
namespace App\Http\Controllers\Dealer;
 
use App\Models\dealer;
use App\Models\Plan;
use Illuminate\View\View;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use App\Services\SnsService;
use App\Services\AuthService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Response;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Services\MarketcheckApiClient;
use Carbon\Carbon;
use App\Jobs\SendSubscriptionEmailJob;

use Stripe\Stripe;
use Stripe\InvoiceItem;
use Stripe\Invoice;
use Stripe\SubscriptionSchedule;
use Stripe\Subscription;


class SubscriptionController extends Controller
{
    use  ApiResponseTrait;
    public function index()
    {
        $plans = Plan::orderBy('price','asc')->get();
        $dealer = auth('dealer')->user();
        $subscription = $dealer->subscription('default');
        $paymentMethod = $dealer->defaultPaymentMethod();
        return view('template.dealers.subscription', compact('plans','subscription','paymentMethod'));
        
    }

    public function checkPlan(Request $request) {
        try {
            $dealer = auth('dealer')->user();
            $token = $request->token;
            $planId = $request->plan_id;
    
            // Assume you have a Plan model to fetch plan details
            $newPlan = Plan::find($planId); // Fetch the new plan using plan_id from the request
            if (!$newPlan) {
                return $this->respondWithError('Invalid plan selected.', ['status' => 0], Response::HTTP_OK);
            }
    
            if ($dealer->subscribed('default')) {
                $subscription = $dealer->subscription('default');
                $currentPlan = Plan::where('stripe_price_id', $subscription->stripe_price)->first(); // Fetch the current plan
    
                // Check if the user is trying to subscribe to the same plan
                if ($subscription->stripe_price == $newPlan->stripe_price_id) {
                    if (!$subscription->canceled()) {
                        return $this->respondWithError('You are already subscribed to this plan.', ['status' => 0], Response::HTTP_OK);
                    } else if ($subscription->onGracePeriod()) {
                        return $this->respondWithSuccess('You are about to switch to a new plan. Your current plan will be terminated immediately. Would you like to confirm this change?', ['status' => 1], Response::HTTP_OK);
                    }
                } else {
                    if (!$subscription->ended()) {
                        $message = "You are about to switch to a new plan. Your current plan will be terminated immediately. Would you like to confirm this change?";
                        return $this->respondWithError($message, ['status' => 1], Response::HTTP_OK);
                    }
                }
            }
    
            return $this->respondWithSuccess('Subscription has been renewed successfully!', ['status' => 0], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), [], Response::HTTP_OK);
        }
    }
    

    public function processSubscriptionAjax(Request $request)
    {
        $dealer = auth('dealer')->user();
        $token = $request->token;
        $planId = $request->plan_id;
        $promoCode = $request->promo_code; // Get the promo code from the request
        $plan = Plan::find($planId);
    
        try {
            // Validate and retrieve the promo code from Stripe
            $promotionCodeId = null;
            if ($promoCode) {
                $promotionCode = \Stripe\PromotionCode::all([
                    'code' => $promoCode,
                    'active' => true,
                ])->data[0] ?? null;
    
                if (!$promotionCode) {
                    return $this->respondWithError('Invalid promo code.', [], Response::HTTP_OK);
                }
    
                $promotionCodeId = $promotionCode->id;
            }
    
            if ($dealer->subscribed('default')) {
                $subscription = $dealer->subscription('default');
    
                // Check if the user is trying to subscribe to the same plan
                if ($subscription->stripe_price == $plan->stripe_price_id) {
                    if (!$subscription->canceled()) {
                        return $this->respondWithError('You are already subscribed to this plan.', [], Response::HTTP_OK);
                    } else if ($subscription->onGracePeriod()) {
                        // Renew the subscription if it's cancelled but still in grace period
                        $subscription->resume();
                        return $this->respondWithSuccess('Subscription has been renewed successfully!', [], Response::HTTP_OK);
                    }
                } else {
                    // If it's a different plan, proceed with upgrading
                    if (!$subscription->ended()) {
                        $currentEndDate = Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end);
                        $remainingDays = Carbon::now()->diffInDays($currentEndDate);
    
                        // Swap the subscription to the new plan without proration
                        $subscription->noProrate()->swap($plan->stripe_price_id);
    
                        // Retrieve the updated subscription to get the new period end date
                        $updatedSubscription = $dealer->subscription('default')->asStripeSubscription();
    
                        // Calculate the new billing date by adding remaining days to the new plan's period end date
                        $newBillingDate = Carbon::createFromTimestamp($updatedSubscription->current_period_end)->addDays($remainingDays);
    
                        // Update the subscription with the new billing cycle anchor
                        $newBillingDate = Carbon::createFromTimestamp($updatedSubscription->current_period_end)->addDays($remainingDays);

                        // Update the subscription with the new billing cycle anchor
                        Stripe::setApiKey(env('STRIPE_SECRET'));
                       /* Subscription::update(
                            $updatedSubscription->id,
                            [
                                'billing_cycle_anchor' => $newBillingDate->timestamp,
                                'proration_behavior' => 'none'
                            ]
                        );*/
    
                        // Optionally, handle the promotion code if provided
                        if ($promotionCodeId) {
                            $subscription->updateStripeSubscription([
                                'promotion_code' => $promotionCodeId,
                            ]);
                        }
                        $invoice = \Stripe\Invoice::all([
                            'subscription' => $subscription->asStripeSubscription()->id,
                            'limit' => 1,
                        ])->data[0];
                        $subscriptionDetails = [
                            'email' => $dealer->email,
                            'start_date' => $subscription->created_at->format('Y-m-d'),
                            'plan' => $plan->name, // assuming you have a plan relationship
                            'amount_paid' => $subscription->latestPayment()->amount, // method to get the latest payment
                            'transaction_id' => $subscription->latestPayment()->transaction_id // method to get transaction ID
                        ];
            
                        // Dispatch the job
                        SendSubscriptionEmailJob::dispatch($subscriptionDetails, $dealer,$invoice->id);
                        return $this->respondWithSuccess('Subscription has been upgraded with remaining days added to the new plan.', [], Response::HTTP_OK);
                    }
                }
            } else {
                // Handling new subscriptions
                $subscription = $dealer->newSubscription('default', $plan->stripe_price_id)
                    ->create($token);
                if ($promotionCodeId) {
                    $subscription->updateStripeSubscription([
                        'promotion_code' => $promotionCodeId,
                    ]);
                }
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $invoice = \Stripe\Invoice::all([
                    'subscription' => $subscription->asStripeSubscription()->id,
                    'limit' => 1,
                ])->data[0];
                $subscriptionDetails = [
                    'email' => $dealer->email,
                    'start_date' => $subscription->created_at->format('Y-m-d'),
                    'plan' => $plan->name, // assuming you have a plan relationship
                    'amount_paid' => $subscription->latestPayment()->amount, // method to get the latest payment
                    'transaction_id' => $subscription->latestPayment()->transaction_id // method to get transaction ID
                ];
    
                // Dispatch the job
                SendSubscriptionEmailJob::dispatch($subscriptionDetails, $dealer,$invoice->id);
    
                return $this->respondWithSuccess('Subscription added successfully!', [], Response::HTTP_OK);
            }
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(), [], Response::HTTP_OK);
        }
    }
    

    public function processSubscriptionAjaxOld(Request $request)
    {
        $dealer = auth('dealer')->user();
        $token = $request->token;
        $planId = $request->plan_id;
        $plan = Plan::find($planId);

        if (!$plan) {
            return $this->respondWithError('The plan does not exist.', [], Response::HTTP_OK);
        }

        // Check if the user has any subscription at all (active or cancelled)
        if ($dealer->subscribed('default')) {
            $subscription = $dealer->subscription('default');

            // Check if the user is trying to subscribe to the same plan
            if ($subscription->stripe_price == $plan->stripe_price_id) {
                if (!$subscription->canceled()) {
                    return $this->respondWithError('You are already subscribed to this plan.', [], Response::HTTP_OK);
                } else if ($subscription->onGracePeriod()) {
                    // Renew the subscription if it's cancelled but still in grace period
                    $subscription->resume();
                    return $this->respondWithSuccess('Subscription has been renewed successfully!', [], Response::HTTP_OK);
                }
            } else {

                if (!$subscription->ended()) {
                    $remainingDays = Carbon::now()->diffInDays(Carbon::createFromTimestamp($subscription->asStripeSubscription()->current_period_end));

                    try {
                        
                        $subscription = $subscription->noProrate()->swap($plan->stripe_price_id);

                        $subscription->trial_ends_at = Carbon::now()->addDays($remainingDays);
                        $subscription->save();

                      
                    } catch (IncompletePayment $exception) {
                       
                        return $this->respondWithError('Payment was not completed.', [], Response::HTTP_OK);
                    } catch (Exception $e) {
                       
                        return $this->respondWithError($e->getMessage(), [], Response::HTTP_OK);
                    }
                }
              
                // If it's a different plan, proceed with swapping
               // $subscription->swap($plan->stripe_price_id);
                $latestPayment = $subscription->latestPayment();
                $transactionId = $latestPayment ? $latestPayment->transaction_id : 'N/A';
                $subscriptionDetails = [
                    'email' => $dealer->email,
                    'start_date' => $subscription->created_at->format('Y-m-d'),
                    'plan' => $plan->name, // assuming you have a plan relationship
                    'amount_paid' => $plan->price, // method to get the latest payment
                    'transaction_id' => $transactionId
                ];
                $subscriptionDetails = [
                    'email' => $dealer->email,
                    'start_date' => $subscription->created_at->format('Y-m-d'),
                    'plan' => $plan->name, // assuming you have a plan relationship
                    'amount_paid' => $plan->price, // method to get the latest payment
                    'transaction_id' => $transactionId // method to get transaction ID
                ];
               
                App\Jobs\SendSubscriptionEmailJob::dispatch($subscriptionDetails, $dealer);
                return $this->respondWithSuccess('Subscription has been upgraded with remaining days added to the new plan.', [], Response::HTTP_OK);
            }
        } else {
            // Handling new subscriptions
            try {
                $subscription = $dealer->newSubscription('default', $plan->stripe_price_id)->create($token);

                $subscriptionDetails = [
                    'email' => $dealer->email,
                    'start_date' => $subscription->created_at->format('Y-m-d'),
                    'plan' =>  $plan->name, // assuming you have a plan relationship
                    'amount_paid' => $plan->price, // method to get the latest payment
                    'transaction_id' => $subscription->latestPayment()->transaction_id // method to get transaction ID
                ];
            
                // Retrieve dealer details (adjust according to your schema)
                
            
                // Dispatch the job
                App\Jobs\SendSubscriptionEmailJob::dispatch($subscriptionDetails, $dealer);
            
                return $this->respondWithSuccess('Subscription added successfully!', [], Response::HTTP_OK);
            } catch (Exception $e) {
                return $this->respondWithError($e->getMessage(), [], Response::HTTP_OK);
            }
        }
    }


    public function myPaymentMethod(Request $request)
    {
        $dealer = auth('dealer')->user();
        $paymentMethod = $dealer->defaultPaymentMethod();
        #dd($paymentMethod);
        return view('template.dealers.paymentMethod', compact('paymentMethod'));

    }

    public function addPaymentMethod(Request $request)
    {
        $planId = $request->plan_id;
        $plans =Plan::where('id',$planId)->first();
        $dealer = auth('dealer')->user();
        if($planId){
            $paymentMethod = $dealer->defaultPaymentMethod();
            if($request->paymentmethod && $paymentMethod){
                return view('template.dealers.makePayment', compact('plans','paymentMethod'));
            }
            return view('template.dealers.makePayment', compact('plans'));
        }else{

        }
        return view('template.dealers.addPaymentMethod', compact('plans'));
    }

    public function updateCard(Request $request)
    {
      
        $dealer = auth('dealer')->user();
        $stripeToken = $request->input('token');
        try {
            if (!$dealer->stripe_id) {
                // Create a new Stripe customer
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $stripeCustomer = \Stripe\Customer::create([
                    'email' => $dealer->email,
                    'name' => $dealer->name,
                ]);
    
                // Save the Stripe customer ID to the dealer
                $dealer->stripe_id = $stripeCustomer->id;
                $dealer->save();
            }
            $paymentMethod = $dealer->addPaymentMethod($stripeToken);

            // Then, set it as default
            $dealer->updateDefaultPaymentMethod($stripeToken);
            return $this->respondWithSuccess('Card updated successfully!',$paymentMethod,Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(),[],Response::HTTP_OK);
        }
        
    }

    public function changePlan(Request $request){
        $dealer = auth('dealer')->user();
        $stripeToken = $request->input('stripeToken');
        try {
            $paymentMethod = $dealer->addPaymentMethod($stripeToken);
            $dealer->updateDefaultPaymentMethod($stripeToken);
            return $this->respondWithSuccess('Profile updated successfully!',$data,Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->respondWithError($e->getMessage(),[],Response::HTTP_OK);
        }
        // First, add the new payment method to Stripe
       
    }

    public function cancelSubscription(Request $request)
    {
        $dealer = auth('dealer')->user();

        $subscription = $dealer->subscription('default'); // Assuming 'default' is the name of the subscription

        if ($subscription && !$subscription->canceled()) {
            if($request->cancel =='now')
                $subscription->cancelNow();
            else 
                $subscription->cancel(); // Cancel the subscription at the end of the billing period
           // return $this->respondWithSuccess('Your subscription has been canceled and will end at the conclusion of your current billing period.',$data,Response::HTTP_OK);
            return redirect()->back()->with('success', 'Your subscription has been canceled and will end at the conclusion of your current billing period.');
        }
        return redirect()->back()->with('error', 'No active or cancellable subscription found.');
    }
    
}
