<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use App\Helpers\CheckOutHelper;
use App\Models\Ecommerce\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Models\Ecommerce\Address;
use App\Models\Ecommerce\Product;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Luigel\Paymongo\Traits\Request;
use Illuminate\Support\Facades\Auth;
use Luigel\Paymongo\Facades\Paymongo;
use App\Helpers\ProductDiscountHelper;
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Woenel\Prpcmblmts\Models\PhilippineRegion;
use Woenel\Prpcmblmts\Models\PhilippineBarangay;
use Woenel\Prpcmblmts\Models\PhilippineProvince;

class Checkout extends Component
{
    use LivewireAlert;

    public $checkoutItems = [];

    #[locked]
    public $subtotal = 0;

    #[locked]
    public $totalShipping = 0;

    public $shipping_street, $shipping_city, $shipping_state, $shipping_zip;
    public $same_as_billing = false;
    public $is_notes;
    public $shipping_method;
    public $notes;
    public $payment_method;

    //paymonggo
    protected $paymentMethod;
    protected $paymentIntent;
    protected $paymentIntent_id;
    protected $createPaymentIntent;

    #[Locked]
    public $province_id = 37; //negros occidental

    #[Locked]
    public $region_id = 7; //region VI (western visayas)

    public $cities = [];
    public $barangays = [];
    public $selectedRegion, $selectedProvince, $selectedCity, $selectedBarangay;

    public $card_name;
    public $card_number;
    public $expiration_month;
    public $expiration_year;
    public $cvv;

    #[Locked]
    public $itemsTotal;

    #[Locked]
    public $itemId;

    #[Locked]
    public $totalDiscount = 0;

    #[Locked]
    public $discountPrice = 0;

    #[Locked]
    public $isDiscounted = [];

    public $isBuyNowMode = false;
    public $searchCity = '';
    public $searchBrgy = '';

    //billing address properties
    public $billing_city;
    public $billing_brgy;
    public $bil_barangays = [];
    public $bil_cities = [];
    public $billingSearchCity = '';
    public $billingSearchBrgy = '';

    // public $originalQuantities = [];

    protected $listeners = [
        'confirmedCancelOrder' => 'confirmedCancelOrder',
    ];

    public function mount()
    {
        $this->itemId =  session('buy_now_product');

        $this->getCheckoutItems();
        $this->loadUserAddress();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();
        $this->selectedRegion = $this->region_id;
        $this->selectedProvince = '0645'; //provincce_code of negroos occidental

        // Load cities for the province
        $this->cities = PhilippineCity::where('province_code', $this->selectedProvince)
            //->limit(10)
            ->get();
        //dd($this->cities);

        $this->bil_cities = PhilippineCity::where('province_code', $this->selectedProvince)
            ->get();

        // // Load barangays if a city is already selected
        // if ($this->selectedCity) {
        //     $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
        //             ->limit(5)
        //             ->get();
        // }

    }

    //checkout helper class
    private function checkoutHelper()
    {
        return app(CheckOutHelper::class);
    }

    private function ProductDiscountHelper()
    {
        return app(ProductDiscountHelper::class);
    }

    // address search
    public function updatedSearchCity()
    {
        $this->cities = PhilippineCity::where('province_code', $this->selectedProvince)
            ->where('name', 'like', '%' . $this->searchCity . '%')
            //->limit(10)
            ->get();
    }
    // address search
    public function updatedSearchBrgy()
    {
        $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
            ->where('name', 'like', '%' . $this->searchBrgy . '%')
            //->limit(10)
            ->get();
    }

    //billing address search
    public function updatedBillingSearchCity()
    {
        $this->bil_cities = PhilippineCity::where('province_code', $this->selectedProvince)
            ->where('name', 'like', '%' . $this->billingSearchCity . '%')
            ->get();
    }

    //billing address search
    public function updatedBillingSearchBrgy()
    {
        $this->bil_barangays = PhilippineBarangay::where('city_code', $this->billing_city)
            ->where('name', 'like', '%' . $this->billingSearchBrgy . '%')
            ->get();
    }


    public function getCheckoutItems()
    {
        if (!Auth::check()) {
            return redirect()->route('filament.auth.auth.login');
        }
        // Handle single product checkout ((buy now)
        $this->isBuyNowMode = session()->get('buy_now_mode', false);
        if ($this->isBuyNowMode) {
            $this->checkoutItems = $this->checkoutHelper()->getBuynowItem();
            return;
        }
        // Handle cart checkout
        $this->checkoutItems = $this->checkoutHelper()->getCheckoutItems();
    }

    //calculate subtotal
    protected function calculateSubtotal()
    {
        $this->itemsTotal = 0;
        $this->totalDiscount = 0; // Initialize discount total

        $this->itemsTotal = $this->checkoutItems->sum(function ($item) {
            $product = $item->product;
            $originalPrice = $item->variant->price ?? $product->prod_price;
            $discountedPrice = $this->ProductDiscountHelper()->getDiscountedPrice($product);
            $finalPrice = $discountedPrice ?? $originalPrice;
            $this->isDiscounted[$item->id] = $finalPrice;

            // Calculate discount amount for this item (if my discount)
            if ($discountedPrice !== null) {
                $this->totalDiscount += ($originalPrice - $discountedPrice) * $item->quantity;
            }

            return $finalPrice * $item->quantity;
        });

        $this->subtotal = $this->itemsTotal + $this->totalShipping;
    }

    //calculate total shipping
    protected function calculateTotalShipping()
    {
        $this->totalShipping = $this->checkoutHelper()->calculateTotalShipping($this->checkoutItems, $this->selectedCity);
    }



    protected function loadUserAddress()
    {
        $userAddress = Address::where('user_id', Auth::id())->first();

        if ($userAddress) {

            // Find and set the city code if it exists
            $city = PhilippineCity::where('name', $userAddress->city)->first();
            if ($city) {
                $this->selectedCity = $city->code;

                // Load specific barangay base sa city na gin select n user
                $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)->get();

                // Find and set the barangay this only works if user my ga exist na nga daan na address
                $barangay = PhilippineBarangay::where('name', $userAddress->barangay)
                    ->where('city_code', $this->selectedCity)
                    ->first();

                if ($barangay) {
                    $this->selectedBarangay = $barangay->name;
                }
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
    protected function rules()
    {
        $rules = [
            //    'payment_method' => 'required|in:gcash,card,paymaya,grab_pay'
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
            'shipping_method' => 'required',
            'notes' => 'nullable|max:1000',
        ];
        if ($this->same_as_billing == false) {
            $rules['billing_city'] = 'required';
            $rules['billing_brgy'] = 'required';
        }
        // $rules['checkoutItems'] = 'min:1';

        if ($this->shipping_method === 'e-wallet') {
            $rules['shipping_method'] = 'required|in:gcash,card,paymaya,grab_pay';
        }

        if ($this->shipping_method === 'card') {
            // $this->card_number = preg_replace('/\D/', '', $this->card_number);
            $rules['card_name'] = 'required|string|min:3|max:100';
            $rules['card_number'] = 'required|digits:16|numeric';
            $rules['expiration_month'] = 'required|numeric|min:1|max:12';
            $rules['expiration_year'] = 'required|numeric|min:' . date('Y') . '|max:' . (date('Y') + 15);
            $rules['cvv'] = 'required|numeric|digits:3';
        }

        return $rules;
    }
    protected function validationAttributes()
    {
        return [
            'selectedCity' => 'city',
            'selectedBarangay' => 'barangay',
            'shipping_method' => 'payment method',

        ];
    }
    //place order
    // public function placeOrder()
    // {

    //     $validatedData = $this->validate();
    //     if ($this->shipping_method === 'e-wallet' && !$this->payment_method) {
    //         $this->addError('payment_method', 'Please select a specific e-wallet.');
    //         return;
    //     }
    //     if ($this->checkoutItems->isEmpty()){
    //         $this->alert('error', '', [
    //             'position' => 'top-end',
    //             'timer' => 3000,
    //             'toast' => true,
    //             'text' => 'Please add items before placing an order.',
    //         ]);
    //         return;
    //     }

    //     $sanitizedData = $this->sanitizeInput($validatedData);

    //     $brgyName = PhilippineBarangay::where('name', $sanitizedData['selectedBarangay'])->value('name');
    //     $cityName = PhilippineCity::where('code', $sanitizedData['selectedCity'])->value('name');

    //     $order = Order::create([
    //         'user_id' => Auth::id(),
    //         'shipping_method' => $sanitizedData['shipping_method'],
    //         'notes' => $sanitizedData['notes'],
    //         'shipping_price' => $this->totalShipping,
    //         'total' => $this->subtotal,
    //         'payment_status' => 'pending',
    //         'order_num' => $this->checkoutHelper()->generateOrderNumber()
    //     ]);

    //     if($this->same_as_billing == true){
    //         $order->billingAddress()->create([
    //             'bil_city' => $cityName,
    //             'bil_barangay' => $brgyName
    //         ]);
    //     }

    //     if($this->same_as_billing == false)
    //     {
    //         $bil_brgyName = PhilippineBarangay::where('name', $sanitizedData['billing_brgy'])->value('name');
    //         $bil_cityName = PhilippineCity::where('code', $sanitizedData['billing_city'])->value('name');

    //         $order->billingAddress()->create([
    //             'bil_city' => $bil_cityName,
    //             'bil_barangay' => $bil_brgyName
    //         ]);
    //     }



    //     if (in_array($this->shipping_method, ['gcash', 'paymaya', 'grab_pay', 'card'])) {
    //         try {

    //             $this->paymentCreateIntent($this->subtotal);
    //             $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();
    //             $this->paymentCreateMethod($sanitizedData);

    //             $order->update([
    //                 'payment_intent_id' => $this->paymentIntent_id
    //             ]);

    //             //webhooks ara sa payment controller
    //             $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('payment.callback'));

    //             $this->processOrderItems($order);

    //             // For redirect-based payments
    //             if (isset($attachedPaymentIntent->next_action['redirect']['url'])) {

    //                 return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
    //             }

    //             // For immediate payments (like cards)
    //             if ($attachedPaymentIntent->status === 'succeeded') {
    //                 $order->update(['payment_status' => 'completed']);
    //                 $this->alert('success', '', [
    //                     'position' => 'top-end',
    //                     'timer' => 3000,
    //                     'toast' => true,
    //                     'text' => 'Product ordered!',
    //                 ]);
    //                 session()->forget([
    //                     'buy_now_product',
    //                     'buy_now_mode',
    //                     'buy_now_quantity',
    //                     'cart_quantities',
    //                     'selected_checkout_items'
    //                 ]);
    //                 return redirect()->route('page.shop');
    //             }

    //             $order->update(['payment_status' => 'failed']);
    //             $this->alert('warning', '', [
    //                 'position' => 'top-end',
    //                 'timer' => 3000,
    //                 'toast' => true,
    //                 'text' => 'Payment processing failed. Please try again.',
    //             ]);
    //             return redirect()->route('page.shop');
    //         } catch (\Exception $e) {
    //             $order->delete();

    //             $this->alert('warning', '', [
    //                 'position' => 'top-end',
    //                 'timer' => 3000,
    //                 'toast' => true,
    //                 'text' => 'Payment processing failed: ' . $e->getMessage(),
    //             ]);

    //             return redirect()->route('checkout');
    //         }
    //     }

    //     // $zipCode = PhilippineCity::where('code', $sanitizedData['selectedCity'])->value('province_code');
    //     // dd($zipCode);

    //     // For non-payment method orders
    //     $this->processOrderItems($order);
    //     //check if user has already an address if my ara eh update lng else create new address
    //     Address::updateOrCreate(
    //         ['user_id' => Auth::id()], // check if user has already an address
    //         [

    //             'barangay' => $brgyName,
    //             'city' => $cityName,
    //         ]
    //     );
    //     session()->forget([
    //         'buy_now_product',
    //         'buy_now_mode',
    //         'buy_now_quantity',
    //         'cart_quantities',
    //         'selected_checkout_items'
    //     ]);

    //     $this->reset();
    //     $this->alert('success', '', [
    //         'position' => 'top-end',
    //         'timer' => 3000,
    //         'toast' => true,
    //         'text' => 'Product ordered!',
    //     ]);
    //     return redirect()->route('page.shop');
    // }


    public function placeOrder()
    {
        $validatedData = $this->validate();

        if ($this->shipping_method === 'e-wallet' && !$this->payment_method) {
            $this->addError('payment_method', 'Please select a specific e-wallet.');
            return;
        }

        if ($this->checkoutItems->isEmpty()) {
            $this->alert('error', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Please add items before placing an order.',
            ]);
            return;
        }

        $sanitizedData = $this->sanitizeInput($validatedData);

        try {
            // Start database transaction
            DB::beginTransaction();

            $brgyName = PhilippineBarangay::where('name', $sanitizedData['selectedBarangay'])->value('name');
            $cityName = PhilippineCity::where('code', $sanitizedData['selectedCity'])->value('name');

            // Prepare order data
            $orderData = [
                'user_id' => Auth::id(),
                'shipping_method' => $sanitizedData['shipping_method'],
                'notes' => $sanitizedData['notes'],
                'shipping_price' => $this->totalShipping,
                'total' => $this->subtotal,
                'payment_status' => 'pending',
                'order_num' => $this->checkoutHelper()->generateOrderNumber()
            ];

            // online paynment method
            if (in_array($this->shipping_method, ['gcash', 'paymaya', 'grab_pay', 'card'])) {
                $this->paymentCreateIntent($this->subtotal);
                $this->paymentIntent = Paymongo::paymentIntent()->find($this->paymentIntent_id)->getAttributes();
                $this->paymentCreateMethod($sanitizedData);

                $orderData['payment_intent_id'] = $this->paymentIntent_id;

                // Create order record
                $order = Order::create($orderData);

                // Add billing address
                if ($this->same_as_billing == true) {
                    $order->billingAddress()->create([
                        'bil_city' => $cityName,
                        'bil_barangay' => $brgyName
                    ]);
                }

                if ($this->same_as_billing == false) {
                    $bil_brgyName = PhilippineBarangay::where('name', $sanitizedData['billing_brgy'])->value('name');
                    $bil_cityName = PhilippineCity::where('code', $sanitizedData['billing_city'])->value('name');

                    $order->billingAddress()->create([
                        'bil_city' => $bil_cityName,
                        'bil_barangay' => $bil_brgyName
                    ]);
                }

                // Process order items
                $this->processOrderItems($order);

                // Attach payment method to payment intent
                $attachedPaymentIntent = $this->createPaymentIntent->attach($this->paymentMethod->id, route('payment.callback'));

                // For redirect-based payments
                if (isset($attachedPaymentIntent->next_action['redirect']['url'])) {
                    DB::commit();
                    return redirect()->away($attachedPaymentIntent->next_action['redirect']['url']);
                }

                // payments (like cards)
                if ($attachedPaymentIntent->status === 'succeeded') {
                    $order->update(['payment_status' => 'completed']);

                    // Update user address
                    Address::updateOrCreate(
                        ['user_id' => Auth::id()],
                        [
                            'barangay' => $brgyName,
                            'city' => $cityName,
                        ]
                    );

                    // Clear session data
                    session()->forget([
                        'buy_now_product',
                        'buy_now_mode',
                        'buy_now_quantity',
                        'cart_quantities',
                        'selected_checkout_items'
                    ]);

                    DB::commit();

                    $this->alert('success', '', [
                        'position' => 'top-end',
                        'timer' => 3000,
                        'toast' => true,
                        'text' => 'Product ordered!',
                    ]);
                    return redirect()->route('page.shop');
                } else {
                    // Payment failed
                    throw new \Exception('Payment processing failed with status: ' . $attachedPaymentIntent->status);
                }
            }

            // (cash on delivery)
            $order = Order::create($orderData);

            // Add billing address
            if ($this->same_as_billing == true) {
                $order->billingAddress()->create([
                    'bil_city' => $cityName,
                    'bil_barangay' => $brgyName
                ]);
            }

            if ($this->same_as_billing == false) {
                $bil_brgyName = PhilippineBarangay::where('name', $sanitizedData['billing_brgy'])->value('name');
                $bil_cityName = PhilippineCity::where('code', $sanitizedData['billing_city'])->value('name');

                $order->billingAddress()->create([
                    'bil_city' => $bil_cityName,
                    'bil_barangay' => $bil_brgyName
                ]);
            }

            // Process order items
            $this->processOrderItems($order);

            // Update user address
            Address::updateOrCreate(
                ['user_id' => Auth::id()],
                [
                    'barangay' => $brgyName,
                    'city' => $cityName,
                ]
            );

            // Clear session data
            session()->forget([
                'buy_now_product',
                'buy_now_mode',
                'buy_now_quantity',
                'cart_quantities',
                'selected_checkout_items'
            ]);

            DB::commit();

            $this->reset();
            $this->alert('success', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Product ordered!',
            ]);
            return redirect()->route('page.shop');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Order processing failed: ' . $e->getMessage(),
            ]);

            return redirect()->route('checkout');
        }
    }

    public function paymentCreateIntent($amount)
    {

        $paymentIntent = Paymongo::paymentIntent()->create([
            'amount' => $amount,
            'payment_method_allowed' => [
                'card',
                'paymaya',
                'grab_pay',
                'gcash',
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
            $cardNumber = preg_replace('/[^0-9]/', '', $this->card_number);
            $this->paymentMethod = Paymongo::paymentMethod()->create([
                'type' => 'card',
                'details' => [
                    'card_number' => (string)$cardNumber,
                    'exp_month' => (int)$sanitizedData['expiration_month'],
                    'exp_year' => (int)$sanitizedData['expiration_year'],
                    'cvc' => $sanitizedData['cvv'],
                ],
                'billing' => [
                    'email' => Auth::user()->email,
                    'name' => $sanitizedData['card_name'],
                ],
            ]);
        }
    }


    // public function updatedSelectedCity($value)
    // {
    //      $this->barangays = PhilippineBarangay::where('city_code', $value)
    //      ->limit(5)
    //      ->get();
    // }

    public function updatedSelectedRegion($value)
    {
        // PhilippineRegion::where('id', $this->region_id)->first();
    }

    // public function updatedSelectedCity()
    // {


    // }

    public function selectCity($value)
    {
        $this->selectedCity = $value;

        // dd($this->selectedCity);
        $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
            //->limit(10)
            ->get();
        // reset selected barangay if my changes sa city kg recalculate shipping and subtotal
        $this->selectedBarangay = null;
        $this->getCheckoutItems();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();
    }

    //for billing address select city
    public function bilSelectCity($value)
    {
        $this->billing_city = $value;
        $this->bil_barangays = PhilippineBarangay::where('city_code', $this->billing_city)
            //->limit(10)
            ->get();
        $this->billing_brgy = null;
    }

    //for billing address select brgy correspond sa city
    public function bilSelectBrgy($value)
    {
        $this->billing_brgy = $value;
    }

    public function selectBrgy($value)
    {
        $this->selectedBarangay = $value;
        //dd($this->selectedBarangay);
    }

    protected function processOrderItems(Order $orders)
    {
        $this->checkoutHelper()
            ->getOrderItems($orders, $this->checkoutItems, $this->isDiscounted);
    }

    public function increaseQuantity($id = null)
    {
        if ($this->isBuyNowMode) {
            $success = $this->checkoutHelper()->buyNowModeIncreaseQuantity($id, $this->checkoutItems);
        } else {
            $success = $this->checkoutHelper()->cartModeIncreaseQuantity($id);
        }

        if ($success) {
            $this->getCheckoutItems();
            $this->calculateSubtotal();
        } else {
            $this->getCheckoutItems();
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'showCloseButton' => true,
                'text' => 'Not enough stock available!',
            ]);
        }
    }

    public function cancelOrder()
    {
        $this->getCheckoutItems();
        $this->alert('question', 'Are you sure you want to cancel this order?', [
            'position' => 'center',
            'timer' => false,
            'toast' => false,
            'showConfirmButton' => true,
            'showCancelButton' => true,
            'confirmButtonText' => 'Yes',
            'cancelButtonText' => 'No',
            'onConfirmed' => 'confirmedCancelOrder',
        ]);
    }

    public function confirmedCancelOrder()
    {
        $this->reset(['checkoutItems', 'shipping_method', 'notes', 'card_name', 'card_number', 'expiration_month', 'expiration_year', 'cvv']);
        session()->forget(['selected_checkout_items', 'buy_now_product', 'buy_now_mode', 'buy_now_quantity', 'cart_quantities']);
        return to_route('page.shop');
    }


    public function decreaseQuantity($id = null)
    {
        if ($this->itemId) {
            // Buy Now mode
            $this->checkoutHelper()->buyNowModeDecreaseQuantity($id, $this->checkoutItems);
            $this->calculateSubtotal();
        } else {
            // Cart mode
            $this->checkoutHelper()->cartModeDecreaseQuantity($id);
            $this->getCheckoutItems();
            $this->calculateSubtotal();
        }
    }


    #[Layout('layouts.app')]
    #[Title('Checkout')]
    public function render()
    {
        return view('livewire.ecommerce.checkout', [
            'checkoutItems' => $this->checkoutItems,
            'subtotal' => $this->subtotal,
            'totalShipping' => $this->totalShipping,
            'barangays' => $this->barangays, //address
            'cities' => $this->cities, // address
            'billing_cities' => $this->bil_cities, // billing address
            'billing_barangays' => $this->bil_barangays, // billing address
            'itemsTotal' => $this->itemsTotal,
            'totalDiscount' => $this->totalDiscount,
        ]);
    }
}
