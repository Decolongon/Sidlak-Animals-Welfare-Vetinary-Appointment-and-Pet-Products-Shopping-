<?php

namespace App\Livewire\Ecommerce;


use Livewire\Component;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use App\Models\Ecommerce\Order;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Luigel\Paymongo\Facades\Paymongo;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Checkout extends Component
{
    use LivewireAlert;

    public $checkoutItems = [];
    public $subtotal = 0;
    public $totalShipping=0;

    public $shipping_street, $shipping_city, $shipping_state, $shipping_zip;
    public $same_as_billing = false;
    public $shipping_method;
    public $notes;
    public $payment_method;

    public $paymentIntent;
    public $paymentIntent_id;
    public $createPaymentIntent;

    public function mount()
    {
      $this->getCheckoutItems();
     
      $this->calculateTotalShipping();
      $this->calculateSubtotal();
      //$this->placeOrder();
        
    }

    public function getCheckoutItems(){
        if(!Auth::user()){
            return redirect()->route('login');
        }
        // $selectedItemIds = session()->pull('selected_checkout_items', []);
        $selectedItemIds = session('selected_checkout_items',[]);
        $this->checkoutItems = Cart::with('product')
                                ->where('user_id', Auth::id())
                                ->whereIn('id', $selectedItemIds)
                                ->get();

     
        // return $this->checkoutItems;
       
    }

    //calculate subtotal
    public function calculateSubtotal(){
       
        $itemsTotal = $this->checkoutItems->sum(function($item) {
            return $item->product->prod_price * $item->quantity;
        });
      $this->subtotal = $itemsTotal + $this->totalShipping;
    }

    //calculate total shipping
    public function calculateTotalShipping()
    {
       
        foreach ($this->checkoutItems as $item) {
            $requiresShipping = $item->product->prod_requires_shipping;
            $cost = $item->product->shipping_cost;

            if ($requiresShipping) {
                $this->totalShipping += $cost;
            }
           
           
        }
    }

    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    //validate data 
    protected $rules = [
        // 'shipping_street' => 'required',
        // 'shipping_city' => 'required',
        // 'shipping_state' => 'required',
        // 'shipping_zip' => 'required',
        'shipping_method' => 'required',
        'notes' => 'nullable|max:1000',
    ];

    //place order
    public function placeOrder()
    {
        // dd($this->payment_method); 
        ($this->validate());

        $sanitizedData = $this->sanitizeInput([
            // 'user_id' => Auth::id(),
            // 'shipping_street' => $this->shipping_street,
            // 'shipping_city' => $this->shipping_city,
            // 'shipping_state' => $this->shipping_state,
            // 'shipping_zip' => $this->shipping_zip,
            'shipping_method' => $this->shipping_method,
            'notes' => $this->notes,
            'total' => $this->subtotal,
        ]);

        // $this->paymentCreateIntent($this->subtotal);

        // $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();
        // $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('page.shop'));

        // if($this->shipping_method == 'gcash'){
        //     $
        // }


        $orders = Order::create([
                    'user_id' => Auth::id(),
                    // 'shipping_street' => $sanitizedData['shipping_street'],
                    // 'shipping_city' => $sanitizedData['shipping_city'],
                    // 'shipping_zip' => $sanitizedData['shipping_zip'],
                    'shipping_method' => $sanitizedData['shipping_method'],
                    'notes' => $sanitizedData['notes'],
                    'shipping_price' => $this->totalShipping,
                    'total' => $this->subtotal
                ]);

        foreach ($this->checkoutItems as $item) {
             $orders->orderItems()->create([
                    'product_id' => $item->product->id,
                    'quantity' => $item->quantity,
                    // 'total' => $this->subtotal,
                    'price' => $item->product->prod_price,
                ]);

                Cart::where('id', $item->id)
                ->where('user_id', Auth::id())
                ->delete();
        }
       

        // $redirectUrl = $attachedPaymentIntent->next_action['redirect']['url'];
        $this->reset(['payment_method', 'shipping_method', 'notes', 'checkoutItems','totalShipping','subtotal']);
        // return redirect($redirectUrl);
        $this->alert('success','', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Product ordered!',
        ]);
        return redirect()->route('page.shop');
      
    }



    public function paymentCreateIntent($amount){
        $this->createPaymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card','paymaya','grab_pay', 'gcash',
            ],
            'payment_method_options' => [
                'card' => [
                    'request_three_d_secure' => 'automatic',
                ],
            ],
            'description' => 'Sidlak Animal Welfare Donation',
            'statement_descriptor' => 'SIDLAK ANIMAL WELFARE',
            'currency' => 'PHP',
        ]);
      
        $this->paymentIntent_id = $this->createPaymentIntent->id;
    }



   


    #[Layout('layouts.app')]
    #[Title('Checkout')]
    public function render()
    {
        return view('livewire.ecommerce.checkout',[
           'checkoutItems' => $this->checkoutItems,
           'subtotal' => $this->subtotal,
           'totalShipping' => $this->totalShipping
        ]);
    }
}
