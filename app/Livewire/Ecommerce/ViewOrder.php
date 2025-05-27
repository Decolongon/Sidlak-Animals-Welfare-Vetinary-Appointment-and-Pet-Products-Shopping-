<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use App\Models\Ecommerce\Order;
use Livewire\Attributes\Layout;

class ViewOrder extends Component
{
    public $orders;
    public function mount()
    {
        $this->getOrders();
    }

    public function getOrders()
    {
       $this->orders = Order::with(['product','orderItems'])
       ->where('user_id',auth()->user()->id)
       ->where('order_status', '!=', 'cancelled')
       ->where('order_status', '!=', 'delivered')
       ->orderBy('created_at','desc')
       ->get();
    }

    public function cancelOrder($id)
    {
        $order = Order::find($id);
        $order->order_status = 'cancelled';

        foreach ($order->orderItems as $item) {
            $product = $item->product;

            if ($product) {
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
        return view('livewire.ecommerce.view-order',[
            'orders' => $this->orders,
        ]);
    }
}
