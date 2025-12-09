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
use App\Helpers\PostalCodeHelper;
use App\Models\Ecommerce\Address;
use App\Models\Ecommerce\Product;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\DB;
use Luigel\Paymongo\Traits\Request;
use Illuminate\Support\Facades\Auth;
use Luigel\Paymongo\Facades\Paymongo;
use App\Helpers\ProductDiscountHelper;
use Illuminate\Support\Facades\Session;
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

    // public $cities = [];
    // public $barangays = [];
    public $street;

    #[Locked]
    public $selectedRegion, $selectedProvince, $selectedCity, $selectedBarangay, $postal_code;

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
    public $bil_postalCode;
    // public $bil_barangays = [];
    // public $bil_cities = [];
    public $billingSearchCity = '';
    public $billingSearchBrgy = '';
    public $bil_street;

    public $total_weight = 0;

    public $tax = 0;

    // public $originalQuantities = [];

    protected $listeners = [
        'confirmedCancelOrder' => 'confirmedCancelOrder',
        'removeItem' => 'removeItem',
    ];

    public function mount()
    {
        $this->selectedRegion = $this->region_id;
        $this->selectedProvince = '0645'; //provincce_code of negroos occidental

        // Load cities for the province
        // $this->getCities();
        // $this->getBillingCities();

        $this->getCheckoutItems();
        //$this->getTotalWeight();
        $this->loadUserAddress();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();

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

    private function PostalCodeHelper()
    {
        return app(PostalCodeHelper::class);
    }

    #[Computed()]
    public function cities()
    {
        $query = PhilippineCity::where('province_code', $this->selectedProvince);

        if (!empty($this->searchCity)) {
            $query->where('name', 'like', '%' . $this->searchCity . '%');
        }

        return $query->get();
    }


    #[Computed()]
    public function barangays()
    {
        if (!$this->selectedCity) {
            return collect();
        }

        $query = PhilippineBarangay::where('city_code', $this->selectedCity);

        if (!empty($this->searchBrgy)) {
            $query->where('name', 'like', '%' . $this->searchBrgy . '%');
        }

        return $query->get();
    }


    #[Computed()]
    public function bil_barangays()
    {
        if (!$this->billing_city) {
            return collect();
        }

        $query = PhilippineBarangay::where('city_code', $this->billing_city);

        if (!empty($this->billingSearchBrgy)) {
            $query->where('name', 'like', '%' . $this->billingSearchBrgy . '%');
        }

        return $query->get();
    }

    #[Computed()]
    public function bil_cities()
    {
        $query = PhilippineCity::where('province_code', $this->selectedProvince);

        if (!empty($this->billingSearchCity)) {
            $query->where('name', 'like', '%' . $this->billingSearchCity . '%');
        }

        return $query->get();
    }


    // address search
    public function updatedSearchCity()
    {
        $this->calculateSubtotal();
    }

    public function updatedSameAsBilling()
    {
        $this->getCheckoutItems();
        $this->calculateSubtotal();
    }
    // address search
    public function updatedSearchBrgy()
    {
        $this->calculateSubtotal();
    }

    // //billing address search
    // public function updatedBillingSearchCity()
    // {

    //     $this->bil_cities = PhilippineCity::where('province_code', $this->selectedProvince)
    //         ->where('name', 'like', '%' . $this->billingSearchCity . '%')
    //         ->get();
    //     // $this->loadUserAddress();
    // }

    // for shipping address display the selected city if my ara kng wla display something else
    #[Computed]
    public function selectedCityName()
    {
        if (!$this->selectedCity) {
            return 'Select or Search City';
        }

        return PhilippineCity::where('code', $this->selectedCity)->value('name') ?? '';
    }


    // for shipping adddress
    protected function getPostalCodeForSelectedCity()
    {
        if ($this->selectedCity) {
            $city = PhilippineCity::where('code', $this->selectedCity)->first();

            if ($city) {
                $cityName = $city->name;
                $this->postal_code = $this->PostalCodeHelper()->getPostalCodeByCityName($cityName);
            }
        }
    }

    //for billing address
    protected function getBillingPostalCodeForSelectedCity()
    {
        if ($this->billing_city) {
            $city = PhilippineCity::where('code', $this->billing_city)->first();

            if ($city) {
                $cityName = $city->name;
                $this->bil_postalCode = $this->PostalCodeHelper()->getPostalCodeByCityName($cityName);
            }
        }
    }


    // for shipping address display the selected barangay if my ara kng wla display something else
    #[Computed]
    public function selectedBrgyName()
    {
        if (!$this->selectedBarangay) {
            return 'Select or Search Barangay';
        }

        return PhilippineBarangay::where('name', $this->selectedBarangay)->value('name') ?? '';
    }

    // for billing address display the selected city if my ara kng wla display something else
    #[Computed]
    public function selectedBillingCityName()
    {
        if (!$this->billing_city) {
            return 'Select or Search City';
        }

        return PhilippineCity::where('code', $this->billing_city)->value('name') ?? '';
    }

    // for billing address display the selected barangay if my ara kng wla display something else
    #[Computed]
    public function selectedBillingBrgyName()
    {
        if (!$this->billing_brgy) {
            return 'Select or Search Barangay';
        }

        return PhilippineBarangay::where('name', $this->billing_brgy)->value('name') ?? '';
    }

    //billing address search
    public function updatedBillingSearchBrgy()
    {
        //  $this->loadUserAddress();
        $this->calculateSubtotal();
    }


    protected function getCheckoutItems()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        // Handle single product checkout ((buy now)
        $this->isBuyNowMode = session()->get('buy_now_product');
        //  dd($this->isBuyNowMode);
        if ($this->isBuyNowMode) {
            $this->checkoutItems = $this->checkoutHelper()->getBuynowItem();
            return;
            //return $this->checkoutHelper()->getBuynowItem();
        }

        // Handle cart checkout
        $this->checkoutItems = $this->checkoutHelper()->getNormalCheckoutItems();
        //return $this->checkoutHelper()->getNormalCheckoutItems();
    }

    //calculate subtotal
    protected function calculateSubtotal()
    {
        $this->itemsTotal = 0;
        $this->totalDiscount = 0; // Initialize discount total
        $this->tax = 0;

        $this->itemsTotal =  $this->checkoutItems->sum(function ($item) {
            $product = $item->product;
            $originalPrice = $item->variant->price ?? $product->prod_price;
            $discountedPrice = $this->ProductDiscountHelper()->getDiscountedPrice($product, $item);
            $finalPrice = $discountedPrice ?? $originalPrice;
            $this->isDiscounted[$item->id] = $finalPrice;

            // Calculate discount amount for this item (if my discount)
            if ($discountedPrice !== null) {
                $this->totalDiscount += ($originalPrice - $discountedPrice) * $item->quantity;
            }

            return $finalPrice * $item->quantity;
        });

        $this->tax = $this->itemsTotal * 0.12;
        

        $this->subtotal = $this->itemsTotal + $this->totalShipping + $this->tax;
    }

    //calculate total shipping
    protected function calculateTotalShipping()
    {
        $this->totalShipping = $this->checkoutHelper()->calculateTotalShipping($this->checkoutItems, $this->selectedCity);
    }


    protected function loadUserAddress()
    {
        //$userAddress = Address::orderBy('created_at', 'desc')->first();
        $userAddress = Address::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($userAddress) {

            // Find and set the city code if it exists
            $city = PhilippineCity::where('name', $userAddress->city)->first();
            if ($city) {
                $this->selectedCity = $city->code;

                // Load specific barangay base sa city na gin select n user
                //$this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)->get();

                // Find and set the barangay this only works if user my ga exist na nga daan na address
                $barangay = PhilippineBarangay::where('name', $userAddress->barangay)
                    ->where('city_code', $this->selectedCity)
                    ->first();

                if ($barangay) {
                    $this->selectedBarangay = $barangay->name;
                    $this->street = ucwords(strtolower($userAddress->street));
                }
                // this is for shipping
                $this->getPostalCodeForSelectedCity();
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
            // 'shipping_method' => 'required',
            'notes' => 'nullable|max:1000',
            'shipping_method' => 'required|in:COD,e-wallet,gcash,paymaya,grab_pay,card',
            'street' => 'required',
        ];

        if ($this->same_as_billing == false) {
            $rules['billing_city'] = 'required';
            $rules['billing_brgy'] = 'required';
            $rules['bil_street'] = 'required';
        }
        // $rules['checkoutItems'] = 'min:1';

        // if ($this->shipping_method === 'e-wallet') {
        //     $rules['shipping_method'] = 'required|in:gcash,card,paymaya,grab_pay';
        // }

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
            'street' => 'street',
            'bil_street' => 'billing street',

        ];
    }

    protected function getTotalWeight()
    {
        $totalWeight = 0;
        foreach ($this->checkoutItems as $item) {
            $productUnit = $item->product->prod_unit;
            $weight = $item->product->prod_weight;
            $quantity = $item->quantity;

            $cost_weight_kg = 0;
            $cost_weight_g = 0;
            $dimentions = 0;

            if ($productUnit == 'kg') {
                $totalWeight += $weight * $quantity;
                //dd($totalWeight);
                // $cost_weight_kg = $totalWeight * 15;
            }

            if ($productUnit == 'g') {
                $weightInKg = $weight / 1000;
                $totalWeight += $weightInKg * $quantity;
                // $cost_weight_g = $totalWeight * 15;
            }

            if ($productUnit == 'has_dimensions') {
                $productLentgth = $item->product->prod_length;
                $productWidth = $item->product->prod_width;
                $productHeight = $item->product->prod_height;
                $dimensional_weight = ($productLentgth * $productWidth * $productHeight) / 3500;
                $totalWeight += $dimensional_weight * $quantity;
                // $dimentions = $dimentional_weight * 15;
            }
        }
        return $totalWeight;
    }


    public function placeOrder()
    {
        $validatedData = $this->validate();

        if ($this->shipping_method === 'e-wallet') {
            $this->addError('shipping_method', 'Please select a specific e-wallet.');
            return;
        }
        $this->getCheckoutItems();
        if ($this->checkoutItems->isEmpty()) {
            $this->alert('error', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Please add items before placing an order.',
            ]);
            return;
        }

        $totalWeight = $this->getTotalWeight();

        if ($totalWeight > 50) {
            $this->alert('error', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Order weight must be less than or equal to 50kg. Total weight: ' . number_format($totalWeight, 2) . 'kg',
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
                // $order->shippingAddresses()->create([
                //     'barangay' => $brgyName,
                //     'city' => $cityName,
                //     'user_id' => auth()->user()->id
                // ]);
                // Update user address




                Address::updateOrCreate([
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'barangay' => $brgyName,
                    'city' => $cityName,
                    'postal_code' => $this->postal_code,
                    'street' => $sanitizedData['street'],
                ]);


                // Add billing address
                if ($this->same_as_billing == true) {
                    $order->billingAddress()->create([
                        'bil_city' => $cityName,
                        'bil_barangay' => $brgyName,
                        'postal_code' => $this->postal_code,
                        'street' => ucwords($sanitizedData['street'])
                    ]);
                }

                if ($this->same_as_billing == false) {
                    $bil_brgyName = PhilippineBarangay::where('name', $sanitizedData['billing_brgy'])->value('name');
                    $bil_cityName = PhilippineCity::where('code', $sanitizedData['billing_city'])->value('name');

                    $order->billingAddress()->create([
                        'bil_city' => $bil_cityName,
                        'bil_barangay' => $bil_brgyName,
                        'postal_code' => $this->bil_postalCode,
                        'street' => $sanitizedData['bil_street'],
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
                    // $order->shippingAddresses()->create([
                    //     'barangay' => $brgyName,
                    //     'city' => $cityName,
                    //     'user_id' => auth()->user()->id
                    // ]);
                    Address::updateOrCreate([
                        'user_id' => Auth::id(),
                        'order_id' => $order->id,
                        'barangay' => $brgyName,
                        'city' => $cityName,
                        'postal_code' => $this->postal_code,
                        'street' => $sanitizedData['street'],

                    ]);


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



                    return $this->redirect(route('page.shop'));
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
                    'bil_barangay' => $brgyName,
                    'postal_code' => $this->postal_code,
                    'street' => $sanitizedData['street']
                ]);
            }

            if ($this->same_as_billing == false) {
                $bil_brgyName = PhilippineBarangay::where('name', $sanitizedData['billing_brgy'])->value('name');
                $bil_cityName = PhilippineCity::where('code', $sanitizedData['billing_city'])->value('name');

                $order->billingAddress()->create([
                    'bil_city' => $bil_cityName,
                    'bil_barangay' => $bil_brgyName,
                    'postal_code' => $this->bil_postalCode,
                    'street' => $sanitizedData['bil_street']
                ]);
            }

            // Process order items
            $this->processOrderItems($order);

            // Update user address
            Address::updateOrCreate([
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'barangay' => $brgyName,
                'city' => $cityName,
                'postal_code' => $this->postal_code,
                'street' => $sanitizedData['street']

            ]);
            // $order->shippingAddresses()->create([
            //     'barangay' => $brgyName,
            //     'city' => $cityName,
            //     'user_id' => auth()->user()->id
            // ]);

            //Clear session data
            session()->forget([
                'buy_now_product',
                'buy_now_mode',
                'buy_now_quantity',
                'cart_quantities',
                'selected_checkout_items'
            ]);

            DB::commit();

            $this->reset();
            // $this->alert('success', '', [
            //     'position' => 'top-end',
            //     'timer' => 3000,
            //     'toast' => true,
            //     'text' => 'Product ordered!',
            // ]);
            return redirect()->route('view-order')->with('success', 'Product ordered Succesfully!');
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'toast' => true,
                'text' => 'Order processing failed: ' . $e->getMessage(),
            ]);
            // dd($e->getMessage());

            return $this->redirect(route('checkout'));
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

    public function selectCity($value)
    {
        $this->selectedCity = $value;

        // dd($this->selectedCity);
        // reset selected barangay if my changes sa city kg recalculate shipping and subtotal
        $this->selectedBarangay = null;
        $this->street = null;
        $this->getPostalCodeForSelectedCity();
        $this->getCheckoutItems();

        $this->calculateTotalShipping();
        $this->calculateSubtotal();
    }

    //for billing address select city
    public function bilSelectCity($value)
    {
        $this->billing_city = $value;

        $this->billing_brgy = null;
        $this->bil_street = null;
        $this->getBillingPostalCodeForSelectedCity();
        $this->getCheckoutItems();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();
    }

    //for billing address select brgy correspond sa city
    public function bilSelectBrgy($value)
    {
        $this->billing_brgy = $value;
        $this->getCheckoutItems();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();
    }

    public function selectBrgy($value)
    {
        $this->selectedBarangay = $value;
        $this->getCheckoutItems();
        $this->calculateTotalShipping();
        $this->calculateSubtotal();
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
            $success = $this->checkoutHelper()->cartModeIncreaseQuantity($id, $this->checkoutItems);
        }

        if ($success) {
            $this->getCheckoutItems();

            $this->getTotalWeight();
            $this->calculateTotalShipping();
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
        $this->confirmedCancelOrder();
        // $this->getCheckoutItems();
    }

    public function confirmedCancelOrder()
    {
        $this->reset();
        session()->forget([
            'buy_now_product',
            'buy_now_mode',
            'buy_now_quantity',
            'cart_quantities',
            'selected_checkout_items'
        ]);
        //return to_route('page.shop');
        return $this->redirect(route('page.shop'));
    }

    public function decreaseQuantity($id = null)
    {
        if ($this->isBuyNowMode) {
            // Buy Now mode
            $this->checkoutHelper()->buyNowModeDecreaseQuantity($id, $this->checkoutItems);
            $this->getCheckoutItems();
            $this->calculateTotalShipping();
            $this->getTotalWeight();
            $this->calculateSubtotal();
        } else {
            // Cart mode
            $this->checkoutHelper()->cartModeDecreaseQuantity($id, $this->checkoutItems);
            $this->getCheckoutItems();
            $this->getTotalWeight();
            $this->calculateTotalShipping();
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
            // 'barangays' => $this->barangays, //address
            // 'cities' => $this->cities, // address
            // 'billing_cities' => $this->bil_cities, // billing address
            // 'billing_barangays' => $this->bil_barangays, // billing address
            'itemsTotal' => $this->itemsTotal,
            'totalDiscount' => $this->totalDiscount,
        ]);
    }
}
