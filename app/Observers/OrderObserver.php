<?php

namespace App\Observers;

use App\Mail\OrderStatusMail;
use App\Models\Ecommerce\Order;
use Illuminate\Support\Facades\Mail;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {

        // if ($order->user) {
        //     Mail::to($order->user->email)
        //         ->send(new OrderStatusMail(
        //             $order->user,
        //             $order->order_status,
        //             $order
        //         ));
        // }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // if ($order->wasChanged('order_status')) {
        //     if ($order->user) {
        //         Mail::to($order->user->email)
        //             ->send(new OrderStatusMail(
        //                 $order->user,
        //                 $order->order_status,
        //                 $order
        //             ));
        //     }
        // }

     
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
