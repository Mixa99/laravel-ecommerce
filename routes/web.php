<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CRUD\CartController;
use App\Http\Controllers\CRUD\CommentsController;
use App\Http\Controllers\CRUD\OrdersController;
use App\Http\Controllers\CRUD\ProductController;
use App\Http\Controllers\Pages\PagesController;
use Illuminate\Support\Facades\Route;

// Pages Controller
Route::controller(PagesController::class)->group(function(){
    Route::get('register_form', 'register_form')->name('register.form');
    Route::get('login_form', 'login_form')->name('login.form');
    Route::get('about', 'about')->name('about');
});

// Authentication Controller
Route::controller(AuthController::class)->group(function(){
    Route::post('register', 'register')->name('auth.register');
    Route::post('login', 'login')->name('auth.login');
    Route::post('logout', 'logout')->name('auth.logout');
});

// Product Controller
Route::controller(ProductController::class)->group(function(){
    Route::get('/', 'index')->name('product.index');
    Route::get('/products/show/{id}', 'show')->name('product.show');
    Route::post('/product/store', 'store')->name('product.store');
    Route::get('/products/add', 'storeView')->name('product.storeView');
    //Route::get('/product/search', 'showProductsFilteredFromCache')->name('product.search');
    Route::get('/product/search', 'search')->name('product.search.vulnearble');
});

// Comment Controller
Route::controller(CommentsController::class)->group(function(){
    Route::post('/product/{id}/addComment', 'store')->name('comment.store');
});

// Orders Controller
Route::controller(OrdersController::class)->group(function(){
    Route::get('/orders/show', 'showOrdersForUser')->name('order.show');
    Route::post('/orders/newOrder', 'makeOrder')->name('order.makeOrder');
});

// Cart Controller
Route::controller(CartController::class)->group(function(){
    Route::post('/cart/add', 'addToCart')->name('cart.add');
    Route::get('/cart', 'showCart')->name('cart.show');
    //Route::post('cart/remove', 'removeFromCart')->name('cart.remove');
    Route::get('cart/remove/{id}', 'remove')->name('cart.remove');
});
