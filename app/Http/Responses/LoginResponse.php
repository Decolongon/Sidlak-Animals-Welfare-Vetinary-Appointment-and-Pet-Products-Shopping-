<?php


namespace App\Http\Responses;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLogin;


class LoginResponse extends BaseLogin
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     */

    public function toResponse($request): RedirectResponse | Redirector
    {
        $redirect_to = $this->getRedirectTo();
        return redirect()->to($redirect_to);

    }

    protected function getRedirectTo():string
    {
        if(auth()->user()->roles()->exists()){
            return route('filament.admin.pages.dashboard'); //admin dashboard
        }

        return route('dashboard');
    }
}
