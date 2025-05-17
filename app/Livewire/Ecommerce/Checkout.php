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
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Woenel\Prpcmblmts\Models\PhilippineRegion;
use Woenel\Prpcmblmts\Models\PhilippineBarangay;
use Woenel\Prpcmblmts\Models\PhilippineProvince;

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

    //paymonggo
    protected $paymentMethod;
    protected $paymentIntent;
    protected $paymentIntent_id;
    protected $createPaymentIntent;

    public $province_id = 37; //negros occidental
    public $region_id = 7; //region VI (western visayas)
    public $cities = [];
    public $barangays = [];
    public $selectedRegion, $selectedProvince, $selectedCity, $selectedBarangay;

    public function mount()
    {
      $this->getCheckoutItems();
     
      $this->calculateTotalShipping();
      $this->calculateSubtotal();
      //$this->placeOrder();

       $this->selectedRegion = $this->region_id;
       $this->selectedProvince = '0645'; //provincce_code of negroos occidental

         // Load cities for the province
      $this->cities = PhilippineCity::where('province_code', $this->selectedProvince)->get();

        // // Load barangays if a city is already selected
        if ($this->selectedCity) {
            $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)->get();
        }
        
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

    //sanitization
    protected function sanitizeInput(array $data): array
    {
        return array_map(function ($value) {
            return is_array($value) ? $this->sanitizeInput($value) : strip_tags($value);
        }, $data);
    }

    
    //validation of data
    protected function rules(){
       $rules =[
        //    'payment_method' => 'required|in:gcash,card,paymaya,grab_pay'
        // 'shipping_street' => 'required',
        // 'shipping_city' => 'required',
        // 'shipping_state' => 'required',
        // 'shipping_zip' => 'required',
        'shipping_method' => 'required',
        'notes' => 'nullable|max:1000',
       ];

       if ($this->shipping_method === 'card') {
            $rules['card_name'] = 'required|string|min:3|max:100';
            $rules['card_number'] = 'required|numeric|digits:16';
            $rules['expiration_month'] = 'required|numeric|min:1|max:12';
            $rules['expiration_year'] = 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 15);
            $rules['cvv'] = 'required|numeric|digits:3';
        }

       return $rules;
    }
   
    //place order
    public function placeOrder()
    {
        
        $validatedData = $this->validate();
        if ($this->shipping_method === 'e-wallet' && !$this->payment_method) {
            $this->addError('payment_method', 'Please select a specific e-wallet.');
            return;
        }

        $sanitizedData = $this->sanitizeInput($validatedData);
       
        $orders = Order::create([
            'user_id' => Auth::id(),
            // 'shipping_street' => $sanitizedData['shipping_street'],
            // 'shipping_city' => $sanitizedData['shipping_city'],
            // 'shipping_zip' => $sanitizedData['shipping_zip'],
            'shipping_method' => $sanitizedData['shipping_method'],
            'notes' => $sanitizedData['notes'],
            'shipping_price' => $this->totalShipping,
            'total' => $this->subtotal,
           
        ]);

       
        if (in_array($this->shipping_method, ['gcash', 'paymaya', 'grab_pay', 'card'])) {
            $this->paymentCreateIntent($this->subtotal);
       

            $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();
            $this->paymentCreateMethod($sanitizedData);

            //dd($this->paymentIntent);
            $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('page.shop'));
           
            $this->processOrderItems($orders);
            //   if($this->paymentIntent->status === 'succeeded'){
            //     $orders->update([
            //         'payment_status' => $this->paymentIntent
            //     ]);
            // }
            
              $orders->update([
                    'payment_status' => 'completed'
                ]);
          
          
            if (
                isset($attachedPaymentIntent->next_action['redirect']['url']) &&
                $attachedPaymentIntent->status === 'awaiting_next_action'
            ) {
                // Redirect user to complete payment
                return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
              
            }

          
          
           

        }        
       
       
        $this->processOrderItems($orders);
       

     
        $this->reset();
      
        $this->alert('success','', [
            'position' => 'top-end',
            'timer' => 3000,
            'toast' => true,
            'text' => 'Product ordered!',
        ]);
        return redirect()->route('page.shop');
      
    }



    public function paymentCreateIntent($amount){
        
        $paymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card','paymaya','grab_pay', 'gcash',
            ],
            'currency' => 'PHP',
            'description' => 'Sidlak Animal Welfare Ecommerce payment',
            'statement_descriptor' => 'SIDLAK ANIMAL WELFARE',
        ]);
    
        $this->paymentIntent_id = $paymentIntent->id;
        $this->createPaymentIntent = $paymentIntent;
        // dd($this->createPaymentIntent);

    }

    public function paymentCreateMethod($sanitizedData)
    {
      
        if (in_array($this->shipping_method, ['gcash', 'paymaya', 'grab_pay'])) {
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => $this->shipping_method,
                'amount' => $this->subtotal * 100,
                'currency' => 'PHP',
                
            ]);
           
        }
    
        // Card logic
        if ($this->shipping_method === 'card') {
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => 'card',
                'details' => [
                    'email' => Auth::user()->email,
                    'details' => [
                        'card_number' => $sanitizedData['card_number'],
                        'exp_month' => (int)$sanitizedData['expiration_month'],
                        'exp_year' => (int)$sanitizedData['expiration_year'],
                        'cvc' => $sanitizedData['cvv'],
                    ],
                ],
            ]);
        }
    }


    public function updatedSelectedCity($value)
    {
         $this->barangays = PhilippineBarangay::where('city_code', $value)->get();
    }

    public function updatedSelectedRegion($value)
    {
         // PhilippineRegion::where('id', $this->region_id)->first();
    }

    public function updateSelectedProvince($value)
    {

    }


    protected function processOrderItems(Order $orders)
    {
        foreach ($this->checkoutItems as $item) {
            $product = $item->product;

            // Deduct quantity from product stock
            $product->prod_quantity -= $item->quantity;
            $product->save();

            $orders->orderItems()->create([
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price' => $item->product->prod_price,
            ]);

            Cart::where('id', $item->id)
                ->where('user_id', Auth::id())
                ->delete();
        }
    }


    


   


    #[Layout('layouts.app')]
    #[Title('Checkout')]
    public function render()
    {
        return view('livewire.ecommerce.checkout',[
           'checkoutItems' => $this->checkoutItems,
           'subtotal' => $this->subtotal,
           'totalShipping' => $this->totalShipping,
           'barangays' => $this->barangays,
           'cities' => $this->cities
        ]);
    }
}
