<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Models\Ecommerce\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Computed;
use App\Helpers\ProductDiscountHelper;

class ViewOrder extends Component
{
    public $orders;
    public $statusFilter = 'all'; // default filter
    public $counter = 0;

    #[Locked]
    public $orderId;

    public function mount() {
        $this->checkOrderStatusChanges();
    }

    #[Computed()]
    public function getOrders()
    {
        $query = Order::with(['product.productDiscounts' => function ($query) {
            $query->ActiveProdDiscount();
        }, 'orderItems.product.images'])
            ->where('user_id', auth()->user()->id)
            // ->where('order_status', '!=', 'cancelled')
            // ->where('order_status', '!=', 'delivered')
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('order_status', '=', "{$this->statusFilter}");
        }

        $orders = $query->take(10)->get();
        // Process variants for each order item
        $orders->each(function ($order) {
            $order->orderItems->each(function ($item) {
                if (
                    $item->product &&
                    $item->product->prod_unit === 'diff_size'
                ) {
                    $item->variant = $item->product->images
                        ->where('sizes', $item->size)
                        ->first();
                    //dd($item->size);
                }
            });
        });
        // // Calculate discounted prices for all products in orders
        // foreach ($this->orders as $order) {
        //     $products = $order->orderItems->map(function ($item) {
        //         return $item->product;
        //     })->filter();

        //     if ($products->isNotEmpty()) {
        //         app(ProductDiscountHelper::class)->calculateDiscountedPrice($products);
        //     }
        // }

        $this->getPrimaryImage($orders);
        return $orders;
    }

    // public function filter($filter)
    // {
    //     $this->$this->statusFilter = $filter;
    //     $this->getOrders();
    // }


    protected function getPrimaryImage($orders)
    {
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                if ($item->product && $item->product->images->isNotEmpty()) {
                    // Set the primary image dynamically
                    $item->product->primary_image = $item->product->images
                        ->where('is_primary', true)
                        ->first() ?? $item->product->images->first();
                }
            }
        }
    }


    public function checkOrderStatusChanges()
    {
        $currentOrders = $this->getOrders;

        $previousOrderStatuses = session('order_statuses', []);

        $currentOrderStatuses = [];
        $hasStatusChanged = false;

        foreach ($currentOrders as $order) {
            $currentOrderStatuses[$order->id] = $order->order_status;

            // Check if status has changed from previous session
            if (
                isset($previousOrderStatuses[$order->id]) &&
                $previousOrderStatuses[$order->id] !== $order->order_status
            ) {
                $hasStatusChanged = true;

                // Store the updated status message
                session()->flash(
                    'order_status_updated',
                    "Tracking #{$order->order_num} status has been updated to: " .
                        ucfirst($order->order_status)
                );
            }
        }

        // If any status changed, flush relevant sessions
        if ($hasStatusChanged) {
           session()->forget([
                'order_statuses',
           ]);
        }

        // Update session with current order statuses
        session(['order_statuses' => $currentOrderStatuses]);
    }

    // You can call this method when component mounts or when orders are updated
    public function updated($property)
    {
        if ($property === 'statusFilter') {
            $this->checkOrderStatusChanges();
        }
    }


    public function updatedStatusFilter()
    {
        $this->getOrders();
    }

    public function toDelivered($id)
    {
        $order = $order = Order::find($id);
        $order->update(['order_status' => 'delivered']);
    }

    public function cancelOrder($id)
    {
        $this->orderId = $id;
        //$this->reset(['orderId']);
        $this->getOrders();
        // $this->counter = 5;
        // while ($this->counter > 0) {

        // $this->stream(
        //     to: 'count',
        //     content: $this->counter,
        //     replace: true,
        // );

        //sleep(1);
        //     $this->counter--;

        //     if ($this->counter == 0) {
        //         $this->processCancelOrder($id);
        //         $this->getOrders();
        //         break;
        //     }
        // }
    }

    public function processCancelOrder($id)
    {

        $order = Order::findOrFail($id);
        $order->order_status = 'cancelled';

        foreach ($order->orderItems as $item) {
            $product = $item->product;

            if ($product) {
                if ($product->prod_unit === 'diff_size') {
                    $variant = $product->images()
                        ->where('sizes', $item->size)
                        ->first();
                    if ($variant) {
                        $variant->quantity += $item->quantity;
                        $variant->save();
                    }
                }
                $product->prod_quantity += $item->quantity; // Add back the ordered quantity
                $product->save();
            }
        }
        $order->save();
        $this->resetOrder();
    }

    public function resetOrder()
    {
        $this->reset(['orderId']);
        $this->getOrders();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.ecommerce.view-order', [
            //'orders' => $this->orders,
        ]);
    }
}
