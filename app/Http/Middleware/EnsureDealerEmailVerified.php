<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Dealer;
use App\Models\DealerSource;
use App\Models\AssignDealer;
use Illuminate\Support\Facades\View;

class EnsureDealerEmailVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('dealer')->user(); 
        if ($user) {
            if (!$user->parent_id) {
                $mainDealer = $user;
                $storeList = $user->storeList()->get();
                $parentId = $user->id;

                if (!$mainDealer->email_verified_at) {
                    return redirect(route('dealer.profile'))->with('error', 'Your email address is not verified.');
                }
                if (!$mainDealer->dealership_group) {
                    return redirect(route('dealer.profile'))->with('error', 'Please Enter your Dealership Group');
                }
            } else {
                $parentId = $user->parent_id;
                $assignedDealerSources = AssignDealer::where('dealer_id', $user->id)->pluck('dealer_source_id');
                $storeList = DealerSource::whereIn('id', $assignedDealerSources)->get();
                $mainDealer = Dealer::find($user->parent_id);

                if (!$mainDealer->email_verified_at) {
                    return redirect(route('dealer.profile'))->with('error', 'Your email address is not verified.');
                }

                if (!$mainDealer->dealership_group) {
                    return redirect(route('dealer.profile'))->with('error', 'Please Enter your Dealership Group');
                }
            }

            // Bind the data to the application container
            app()->instance('mainDealer', $mainDealer);
            app()->instance('storeList', $storeList);
            app()->instance('parentId', $parentId);

            // Separate subscribed and unsubscribed store lists
            $unsubscribedStoreList = $storeList->where('subscribed', 0)->pluck('id')->toArray();
            $subscribedStoreList = $storeList->where('subscribed', 1)->where('is_subscribed', 0)->pluck('id')->toArray();

            //$allstoreid = $storeList->where('is_subscribed', 0)->get()->pluck('id')->toArray();
            // Share the data with all views
            View::share('mainDealer', $mainDealer);
            View::share('totalstorelist', $storeList->count());
            View::share('storeList', $storeList);
            //View::share('allstoreid', $allstoreid);
            View::share('parentId', $parentId);
            View::share('unsubscribedStoreList', $unsubscribedStoreList);
            View::share('subscribedStoreList', $subscribedStoreList);
        }

        return $next($request);
    }
}
