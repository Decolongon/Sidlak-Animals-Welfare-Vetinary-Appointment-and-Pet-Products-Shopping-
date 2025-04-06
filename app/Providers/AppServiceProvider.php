<?php

namespace App\Providers;
use App\Models\User;
use App\Models\Animal\Dog;
use App\Models\Animal\Breed;
use App\Models\Announcement;
use App\Policies\UserPolicy;
use App\Models\Blog\BlogPost;
use App\Models\Blog\Category;
use Filament\Facades\Filament;
use App\Models\Contact\Inquiry;
use App\Models\Ecommerce\Order;
use App\Models\Adoption\Adoption;
use App\Models\Donation\Donation;
use App\Models\Ecommerce\Product;
use App\Policies\Animal\DogPolicy;
use App\Models\Volunteer\Volunteer;
use App\Policies\Animal\BreedPolicy;
use App\Policies\AnnouncementPolicy;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Policies\Blog\BlogPostPolicy;
use App\Policies\Blog\CategoryPolicy;
use App\Http\Responses\LogoutResponse;
use App\Models\Appointment\Appointment;
use App\Models\Ecommerce\ProductReview;
use App\Observers\AnnouncementObserver;
use App\Policies\Contact\InquiryPolicy;
use App\Policies\Ecommerce\OrderPolicy;
use Illuminate\Support\ServiceProvider;
use App\Models\Ecommerce\ProductCategory;
use App\Policies\Adoption\AdoptionPolicy;
use App\Policies\Donation\DonationPolicy;
use App\Policies\Ecommerce\ProductPolicy;
use App\Policies\Volunteer\VolunteerPolicy;
use App\Models\Appointment\AppointmentCategory;
use App\Policies\Appointment\AppointmentPolicy;
use App\Policies\Ecommerce\ProductReviewPolicy;
use App\Policies\Ecommerce\ProductCategoryPolicy;
use App\Policies\Appointment\AppointmentCategoryPolicy;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
   
    public function register(): void
    {
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        Announcement::observe(AnnouncementObserver::class);

        $this->registerPolicies();
        
    }

     /**
    * @param array $modelPolicies
    * @return void
    */
   protected function inBulkPolicies(array $modelPolicies)
   {
       foreach ($modelPolicies as $model => $policy) {
           Gate::policy($model, $policy);
       }
   }


   protected function registerPolicies()
   {
       $policies = [
           Adoption::class => AdoptionPolicy::class,
           Breed::class => BreedPolicy::class,
           Dog::class => DogPolicy::class,
           AppointmentCategory::class => AppointmentCategoryPolicy::class,
           Appointment::class => AppointmentPolicy::class,
           BlogPost::class => BlogPostPolicy::class,
           Category::class => CategoryPolicy::class,
           Inquiry::class => InquiryPolicy::class,
           Donation::class => DonationPolicy::class,
           Product::class => ProductPolicy::class,
           ProductCategory::class => ProductCategoryPolicy::class,
           ProductReview::class => ProductReviewPolicy::class,
           Order::class => OrderPolicy::class,
           Volunteer::class => VolunteerPolicy::class,
           Announcement::class => AnnouncementPolicy::class,
           User::class => UserPolicy::class,
          
       ];

       $this->inBulkPolicies($policies);
   }

}
