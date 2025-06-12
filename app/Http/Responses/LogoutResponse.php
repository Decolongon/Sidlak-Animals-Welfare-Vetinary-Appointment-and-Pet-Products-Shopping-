<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): RedirectResponse
    {
         return redirect()->route('page.home');   
    }

}
