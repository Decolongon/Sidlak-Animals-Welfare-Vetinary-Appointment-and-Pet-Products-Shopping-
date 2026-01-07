<?php

namespace App\Traits;

use Illuminate\Support\Facades\RateLimiter;
use Jantinnerezo\LivewireAlert\LivewireAlert;

trait AddToCartTrait
{
    use LivewireAlert;

    public function checkRateLimit($user_id, $session_id): bool
    {
        $key = 'add-to-cart:' . ($user_id ?: $session_id);

        if (RateLimiter::tooManyAttempts($key, 10)) { // 10 attempts per minute
            $seconds = RateLimiter::availableIn($key);
            //$this->showAlert('error', "Too many attempts. Please try again in {$seconds} seconds.");
            $this->alert('warning', '', [
                'position' => 'top-end',
                'timer' => 3000,
                'showConfirmButton' => false,
                'showCloseButton' => true,
                'toast' => true,
                'text' => "Too many attempts.Please try again in {$seconds} seconds",
            ]);
            return true;
        }

        RateLimiter::hit($key, 60); // 60 seconds = 1 minute
        return false;
    }
}
