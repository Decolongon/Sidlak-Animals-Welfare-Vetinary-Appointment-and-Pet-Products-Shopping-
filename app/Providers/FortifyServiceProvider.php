<?php

namespace App\Providers;

use App\Actions\LogoutJet;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use App\Actions\RoleBasedRedirect;

use App\Http\Responses\LoginResponse;
use Illuminate\Support\Facades\Event;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Responses\LogoutResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;

// use Laravel\Fortify\Contracts\LoginResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Container\Attributes\Log;
use App\Http\Responses\JetLogoutResponse;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;

use Illuminate\Support\Facades\RateLimiter;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Laravel\Fortify\Contracts\LogoutResponse as JetsstreamLogout;
use Filament\Http\Responses\Auth\LoginResponse as AuthLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutContractResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->app->singleton(LoginResponse::class, RoleBasedRedirect::class);
           $this->app->singleton(
            AuthLoginResponse::class,
            LoginResponse::class,
        );
        //for filament admin
        $this->app->bind(
            LogoutContractResponse::class,
            LogoutResponse::class,
        );
      
     
      
       
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fortify::createUsersUsing(CreateNewUser::class);
        // Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        // Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        // Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // RateLimiter::for('login', function (Request $request) {
        //     $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

        //     return Limit::perMinute(5)->by($throttleKey);
        // });

        // RateLimiter::for('two-factor', function (Request $request) {
        //     return Limit::perMinute(5)->by($request->session()->get('login.id'));
        // });

     

         

        
    }
}
