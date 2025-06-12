<?php

use Illuminate\Http\Request;
use App\Livewire\Ecommerce\Checkout;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


