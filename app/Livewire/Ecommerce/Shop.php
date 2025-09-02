<?php

namespace App\Livewire\Ecommerce;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ecommerce\Cart;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Ecommerce\Product;
use Livewire\WithoutUrlPagination;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProductDiscountHelper;
use Illuminate\Support\Facades\Session;
use App\Models\Ecommerce\ProductCategory;
use App\Models\Ecommerce\ProductDiscount;
use App\Models\Ecommerce\ProductImage as ProductVariant;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Lazy;


class Shop extends Component
{
    use WithPagination, WithoutUrlPagination;

    public $query = ''; // search for products
    public $searchCat = ''; // search for prod categories
    public $categories;
    //public $products;
    public $selectedCat = null; // hold the selected category id
    public $selectedCatName = null; // seleceted category name
    public $sortBy = 'asc'; // default sort order

    // public $selectedSize;
    // public $price = 0;

    public function mount()
    {
        $this->categories = ProductCategory::get(['id', 'prod_cat_name']);
        //$this->getProducts();
    }

    public function placeholder()
    {
        return view('livewire.ecommerce.lazy_loading.shop-lazy');
    }

  
    public function updatedQuery()
    {
        $this->getProducts();

        // $this->query = trim($this->query);
        $this->resetPage();
    }

    // Update category search dynamically
    public function updatedSearchCat()
    {
        $this->categories = ProductCategory::where('prod_cat_name', 'LIKE', '%' . $this->searchCat . '%')
            ->get(['id', 'prod_cat_name']);
    }

    //get the products para categories and for search bar
    #[Computed()]
    public function getProducts()
    {

        $query = Product::with([
            'images',
            'productCategories',
            'productDiscounts' => function ($query) {
                $query->ActiveProdDiscount(); // scope ara sa productDiscount model
                //$query->withPivot
            }
        ])
            ->where([
                ['is_visible_to_market', true]

            ]);

        if(! empty($this->query)){
            $query->where(function ($q) { // for search
                $q->where('prod_name', 'Like', '%' . $this->query . '%')
                    ->orWhere('prod_sku', 'Like', '%' . $this->query . '%');
            });
        }

        //filter product base sa category
        if ($this->selectedCat) {
            $query->whereHas('productCategories', function ($q) {
                $q->where('product_category_id', $this->selectedCat);
            });
        }

        //order by product base sa price
        $query->orderBy('prod_price', $this->sortBy);



        $products = $query->paginate(12);


        // Calculate discounted prices for each product
        app(ProductDiscountHelper::class)->calculateDiscountedPrice($products);

        // Set primary image for each product if wala primary first
        // image ma display and gin upload n shop admin
        $products->each(function ($product) {
            $product->primary_image = $product->images->where('is_primary', true)->first()
            ?? $product->images->first();
        });
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
        return view('livewire.ecommerce.shop', [
            //'products' => $this->getProducts(),
            'categories' => $this->categories
        ]);
    }
}
