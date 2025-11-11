<?php

namespace App\Http\Controllers\Paymongo;

use Illuminate\Http\Request;
use App\Helpers\CheckOutHelper;
use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Address;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Luigel\Paymongo\Facades\Paymongo;
use App\Models\Appointment\Appointment;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class PaymentController extends Controller
{
    private CheckOutHelper $helper;
    public function __construct(private Checkouthelper $helpers)
    {
        $this->helper = $helpers;
    }
    public function handleRedirect(Request $request)
    {
        $redirectUrl = $request->query('url');
        if (!$redirectUrl) {
            return redirect()->back()->with('error', 'No redirect URL provided');
        }
        return redirect()->away($redirectUrl);
    }

    //shop webhook
    public function paymentCallback(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id'); //query payment_intent_id from paymongo

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
                    } elseif ($status === 'expired') {
                        $newStatus = 'failed';
                    }

                    $order->update(['payment_status' => $newStatus]);
                    session()->forget([
                        'buy_now_product',
                        'buy_now_mode',
                        'buy_now_quantity',
                        'cart_quantities',
                        'selected_checkout_items'
                    ]);
                
                    return redirect()->route('view-order')->with('success', 'Product ordered Successfully');
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


   

    //vetinary appointment webhook
    public function vetWebHook(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent_id'); //query payment_intent_id from paymongo
        // dd($paymentIntentId);
        if ($paymentIntentId) {
            try {
                $paymentIntent = Paymongo::paymentIntent()->find($paymentIntentId)->getAttributes();
                $appoint = Appointment::where('paymentIntent_id', $paymentIntentId)->first();

                if ($appoint) {
                    $status = $paymentIntent['status'];
                    $newStatus = 'failed'; // default

                    if ($status === 'succeeded') {
                        $newStatus = 'completed';
                    } elseif (in_array($status, ['awaiting_payment', 'processing'])) {
                        $newStatus = 'pending';
                    } elseif ($status === 'expired') {
                        $newStatus = 'failed';
                    }

                    $appoint->update(['payment_status' => $newStatus]);
                    return redirect()->route('appointment')->with('message', 'Successfully Booked Appointment!');
                }
            } catch (\Exception $e) {
                // Log error
                Log::error("Payment callback error: " . $e->getMessage());
            }
        }

        return redirect()->route('appointment');
    }
}
