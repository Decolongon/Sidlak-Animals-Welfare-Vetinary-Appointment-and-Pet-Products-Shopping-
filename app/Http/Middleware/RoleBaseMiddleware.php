<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RoleBaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $currentPanel = Filament::getCurrentPanel()?->getId();

        if (!$user || !$currentPanel) {
            return redirect()->route('login');
            // return redirect(Filament::getPanel('auth')->getLoginUrl());
        }


        $userRoles = $user->roles()->pluck('name')->toArray();
        //dd($userRoles);

         if($user && empty($userRoles) && $currentPanel === 'admin') {
            return back();
        }


        // If user has no roles and is trying to access the 'admin' panel, redirect them to normal dashboard
        if (empty($userRoles) && $currentPanel === 'auth') {
            // Session::flush();
            return redirect()->route('dashboard');
           
        }


        // If user has roles and is trying to access the 'auth' panel, redirect them to admin dashboard
        if (!empty($userRoles) && $currentPanel === 'auth') {
            //Session::flush();
            return redirect()->route('filament.admin.pages.dashboard'); // admin dashboard

        }



        return $next($request);



       




    }
}
