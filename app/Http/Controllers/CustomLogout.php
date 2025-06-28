<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;

class CustomLogout extends FortifyAuthenticatedSessionController

{
     /**
     * Destroy an authenticated session (logout).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */ 
    public function logout(Request $request)
    {
        Auth::logout();
       
        return redirect()->route('page.home');
    
    }
}