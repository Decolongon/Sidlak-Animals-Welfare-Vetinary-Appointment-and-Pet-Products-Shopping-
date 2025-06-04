<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\Product;
use App\Models\Ecommerce\ProductCategory;
use App\Models\Ecommerce\ProductDiscount;
Use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Shop extends Component
{
    use WithPagination;
   
    public $query = ''; // search for products
    public $searchCat = ''; // search for prod categories
    public $categories;
    //public $products;
    public $selectedCat = null; // hold the selected category id
    public $selectedCatName = null; // seleceted category name
    public $sortBy = 'asc'; // default sort order
   
    
    public function mount(){
        // $this->products = Product::with(['images','productCategories'])
        // ->where('prod_quantity', '>' , 0)
        // ->get();
        $this->categories = ProductCategory::get(['id','prod_cat_name']);
        $this->getProducts();

    }


    public function updatedQuery()
    {   
        $this->getProducts();
       
        // $this->query = trim($this->query);
        // $this->resetPage();
    }

     // Update category search dynamically
     public function updatedSearchCat()
     {
         $this->categories = ProductCategory::where('prod_cat_name', 'LIKE', '%' . $this->searchCat . '%')
                             ->get(['id', 'prod_cat_name']);
     }

    // public function getProducts (){

    //     $this->products = Product::with(['images','productCategories'])
    //          ->where('prod_quantity', '>' , 0)
    //          ->where( function ( $query) {
    //               $query->where('prod_name', 'Like', '%' . $this->query . '%');
    //               $query->orWhere('prod_description', 'Like', '%' . $this->query . '%');
    //          })
    //          ->get();
    // }


    //get the products para categories and for search bar
    public function getProducts(){

        $query = Product::with(['images',
        'productCategories',
        'productDiscounts' => function ($query) {
             $query->select('product_discounts.id', 'discount_name', 'start_at', 'end_at',)
                      ->where('start_at', '<=', now())
                      ->where('end_at', '>=', now());
            //$query->withPivot
            } 
        ])
        ->where([ 
                  ['is_visible_to_market', true]
                 
        ])
        ->where( function ( $q) { // for search
                $q->where('prod_name', 'Like', '%' . $this->query . '%')
                ->orWhere('prod_sku', 'Like', '%' . $this->query . '%');
             });
            //  ->paginate(6);

             //filter product base sa category
            if($this->selectedCat){
                $query->whereHas('productCategories', function ($q) {
                    $q->where('product_category_id', $this->selectedCat);
                });

            }
            $query->orderBy('prod_price', $this->sortBy);
            // if (in_array($this->sortBy, ['asc', 'desc'])) {
            //     $query->orderBy('prod_price', $this->sortBy);
            // }
            // $this->products = $query->paginate(4);
            
            $products = $query->paginate(12);

            // Calculate discounted prices for each product
           foreach ($products as $product) {
                $product->discounted_price = null;
                $product->discount_amount = null;
                $product->discount_label = null;

                $discount = $product->productDiscounts->first();

                if (!$discount || !$discount->pivot) {
                    continue;
                }

                $type = $discount->pivot->discount_type;
                $value = floatval($discount->pivot->discounted_price);

                if ($type === 'fixed') {
                    $product->discount_amount = $value;
                    $product->discounted_price = $product->prod_price - $value;
                    $product->discount_label = ' ₱' . number_format($value, 0) . ' off';
                } 
                if ($type === 'percent') {
                    $discountValue = $product->prod_price * ($value / 100);
                    $product->discount_amount = $discountValue;
                    $product->discounted_price = $product->prod_price - $discountValue;
                    $product->discount_label = number_format($value, 0) . ' % off';
                }
            }       

            return $products;
           
          
    }


    // //default sorting asc 
    // public function filterByCategory($id)
    // {
    //     $this->filterByCategoryAndOrder($id, $this->sortBy ?? 'asc');
    // }

    //filter by category kg order is asc or desc? kng null default asc
    public function filterByCategoryAndOrder($id = null, $order = 'asc')
    {
        $this->selectedCat = $id;
        $this->sortBy = $order;

        // Update selected category name
        $this->selectedCatName = $id ? optional(ProductCategory::find($id))->prod_cat_name : null;

        $this->getProducts();
    }

    //arrange by trigger lng ang filter by category kng gstu ni user eh change ang order
    public function arrangeBy($order)
    {
        $this->filterByCategoryAndOrder($this->selectedCat, $order);
    }

    

    #[Layout('layouts.app')]
    #[Title('Shop')]
    public function render()
    {
        return view('livewire.ecommerce.shop',[
            'products' => $this->getProducts(),
            'categories' => $this->categories
        ]);
    }
}
