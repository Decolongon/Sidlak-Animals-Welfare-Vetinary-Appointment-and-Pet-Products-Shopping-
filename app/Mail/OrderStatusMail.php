<?php

namespace App\Mail;

use App\Models\Ecommerce\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $order_status;
    public User $user;
    public $shipping_method;

    //public $orders = []; // all orders
    public $total;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, $status, Order $order)
    {
        $this->user = $user;
        $this->order_status = $status;
        $this->order = $order;

        $this->order->load([
            'orderItems.product.images',
            'user',
        ]);

        //get primary image
        $this->order->orderItems->each(function ($orderItem) {
            if ($orderItem->product && $orderItem->product->relationLoaded('images')) {
                $orderItem->product->primary_image = $orderItem->product->images
                    ->where('is_primary', true)
                    ->first()
                    ?? $orderItem->product->images->first();
            }
        });

       // $this->shipping_method = ($order->shipping_method == 'COD') ? 'Cash on Delivery' : 'E-Wallet'. '- '.$order->shipping_method;


        $this->total = $order->total;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Order #{$this->order->order_num}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order.orderStatus',
            with: [
                'user' => $this->user,
                'order_status' => $this->order_status,
                'order_status_label' => $this->getStatusLabel($this->order_status),
                'total' => $this->total,
                //'shipping_method' => $this->shipping_method,
                'order' => $this->order, // single order
                //'orders' => $this->orders, // all orders
            ]
        );
    }


    private function getStatusLabel($status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => ucfirst($status),
        };
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
