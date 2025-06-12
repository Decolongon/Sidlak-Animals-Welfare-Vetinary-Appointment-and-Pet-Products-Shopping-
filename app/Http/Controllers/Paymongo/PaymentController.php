<?php

namespace App\Http\Controllers\Paymongo;

use Illuminate\Http\Request;
use App\Models\Ecommerce\Order;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Luigel\Paymongo\Facades\Paymongo;

class PaymentController extends Controller
{
    public function handleRedirect(Request $request)
    {
        $redirectUrl = $request->query('url');
        if (!$redirectUrl) {
            return redirect()->back()->with('error', 'No redirect URL provided');
        }
        return redirect()->away($redirectUrl);
    }

    public function paymentCallback(Request $request)
{
    $paymentIntentId = $request->query('payment_intent_id');

    if ($paymentIntentId) {
        try {
            $paymentIntent = Paymongo::paymentIntent()->find($paymentIntentId)->getAttributes();
            $order = Order::where('payment_intent_id', $paymentIntentId)->first();

            if ($order) {
                $status = $paymentIntent['status'];
                $newStatus = 'failed'; // default

                if ($status === 'succeeded') {
                    $newStatus = 'completed';
                } elseif (in_array($status, ['awaiting_payment', 'processing'])) {
                    $newStatus = 'pending';
                } elseif($status === 'expired'){
                    $newStatus = 'failed';
                } 

                $order->update(['payment_status' => $newStatus]);
                 return redirect()->route('page.shop');
                // // Show appropriate message to user
                // if ($newStatus === 'completed') {
                //     $this->showSuccessAlert('Payment successful!');
                // } elseif ($newStatus === 'failed') {
                //     $this->showErrorAlert('Payment failed. Please try again.');
                // }
            }
        } catch (\Exception $e) {
            // Log error
            Log::error("Payment callback error: " . $e->getMessage());
        }
    }

    return redirect()->route('page.shop');
}
}
