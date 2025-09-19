<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Ecommerce\Order;
use Livewire\Attributes\Layout;
use App\Helpers\ProductDiscountHelper;
use Livewire\WithPagination;

class ViewOrder extends Component
{
    public $orders;
    public $statusFilter = 'all'; // default filter
    public function mount()
    {
        $this->getOrders();
    }

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
            $query->where('order_status', '=' ,"{$this->statusFilter}");

        }

        $this->orders = $query->take(10)->get();
        // Process variants for each order item
        $this->orders->each(function ($order) {
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

        $this->getPrimaryImage($this->orders);
      
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





    public function updatedStatusFilter()
    {
        $this->getOrders();
    }

    public function cancelOrder($id)
    {
        $order = Order::find($id);
        $order->order_status = 'cancelled';

        foreach ($order->orderItems as $item) {
            $product = $item->product;

            if ($product) {
                if($product->prod_unit === 'diff_size'){
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
        $this->getOrders();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.ecommerce.view-order', [
            'orders' => $this->orders,
        ]);
    }
}
