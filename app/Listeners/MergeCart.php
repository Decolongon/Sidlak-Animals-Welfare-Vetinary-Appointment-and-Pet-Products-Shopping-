<?php

namespace App\Listeners;

use App\Models\Ecommerce\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MergeCart
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;
        $session_id = Session::getId();
        // $session_id = Session::get('guest_session_id');

        //$carts= session()->pull('cart');

        //dd($carts);
        $isCart = Cart::where('session_id', $session_id)->exists();
            
        //if session cart exist update ang gin png add ni guess user asign sa user_id kag delete ang session
        if($isCart){
            Cart::where('session_id', $session_id)->update([
                'user_id' => $user->id,
                'session_id' => null,
            ]);
           Session::forget($session_id);
           Session::flush();
         
          
        }
       
    // if($carts  && is_array($carts)){
    //      foreach ($carts as $productId => $quantity) {
    //         Cart::create([
    //             'product_id' => $productId,
    //             'user_id'    => $user->id,
    //             'quantity'   => $quantity,
    //         ]);
    //     }
            
    // }
        
    
       
       

           
     
        
        
    
    }
}
