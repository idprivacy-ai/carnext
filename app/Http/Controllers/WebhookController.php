<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Laravel\Cashier\Subscription;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Get the raw payload and the signature header
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        
        Log::info('Webhook received', ['payload' => $payload]);
        Log::info('Webhook header', ['sigHeader' => $sigHeader]);

        $endpointSecret = env('WEBHOOK_SIGNATURE'); // Ensure this is correctly set in your .env file

        try {
            // Verify the event by constructing it using the raw payload and signature header
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Invalid payload', ['exception' => $e]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Invalid signature', ['exception' => $e]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'invoice.created':
                $this->handleInvoiceCreated($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handleInvoiceCreated($invoice)
    {
        // Retrieve the subscription ID from the invoice
        $subscriptionId = $invoice->subscription;

        // Find the corresponding subscription in your database
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();

        if ($subscription) {
            // Update your subscription model accordingly
            $subscription->update([
                'last_invoice_id' => $invoice->id,
                'last_invoice_date' => \Carbon\Carbon::createFromTimestamp($invoice->created),
                // Optionally update other fields here
            ]);

            // Send the invoice to the customer
           
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        // Retrieve the subscription ID from the invoice
        $subscriptionId = $invoice->subscription;

        // Find the corresponding subscription in your database
        $subscription = Subscription::where('stripe_id', $subscriptionId)->first();

        if ($subscription) {
            // Update your subscription model accordingly
            $stripeSubscription = \Stripe\Subscription::retrieve($subscriptionId);
            $subscription->update([
                'last_invoice_id' => $invoice->id,
                'last_invoice_date' => \Carbon\Carbon::createFromTimestamp($invoice->created),
                'ends_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                // Optionally update other fields here
            ]);

            $customerEmail = $subscription->user->email; // Assuming your Subscription model is related to a User model
            $stripeInvoice = \Stripe\Invoice::retrieve($invoice->id);
            Mail::to($customerEmail)->send(new InvoiceMail($stripeInvoice));

            // Additional logic for payment succeeded, e.g., granting access to a service
        }
    }
}
