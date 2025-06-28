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
use App\Models\Appointment\VetSchedule;
use App\Models\Ecommerce\ProductReview;
use App\Observers\AnnouncementObserver;
use App\Policies\Contact\InquiryPolicy;
use App\Policies\Ecommerce\OrderPolicy;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Filament\Navigation\NavigationGroup;
use App\Models\Ecommerce\ProductCategory;
use App\Models\Ecommerce\ProductDiscount;
use App\Observers\VetAppointmentObserver;
use App\Policies\Adoption\AdoptionPolicy;
use App\Policies\Donation\DonationPolicy;
use App\Policies\Ecommerce\ProductPolicy;
use App\Policies\Volunteer\VolunteerPolicy;
use App\Models\Appointment\AppointmentCategory;
// use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Policies\Appointment\AppointmentPolicy;
use App\Policies\Appointment\VetSchedulePolicy;
use App\Policies\Ecommerce\ProductReviewPolicy;
use App\Policies\Ecommerce\ProductCategoryPolicy;
use App\Policies\Ecommerce\ProductDiscountPolicy;
use App\Policies\Appointment\AppointmentCategoryPolicy;
use Filament\Http\Responses\Auth\LoginResponse as AuthLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutContractResponse;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
     protected $listen = [];
   
    public function register(): void
    {
         $this->registerPolicies();
        //  $this->registerEvent();
        // $this->app->bind(
        //     LogoutContractResponse::class,
        //     LogoutResponse::class,
        // );
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        Announcement::observe(AnnouncementObserver::class);
        Appointment::observe(VetAppointmentObserver::class);
      
       
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
           VetSchedule::class => VetSchedulePolicy::class,
           ProductDiscount::class => ProductDiscountPolicy::class,
        //    User::class => UserPolicy::class,
           
       ];

       $this->inBulkPolicies($policies);
   }

   protected function registerEvent(): void
   {
       $this->listen = [
            \Illuminate\Auth\Events\Registered::class => [
                \App\Listeners\MergeCart::class,
            ],
        ];
   }

  

}
