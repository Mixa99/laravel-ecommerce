<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CRUD\CommentsController;
use App\Http\Controllers\CRUD\OrdersController;
use App\Http\Controllers\CRUD\ProductController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware'=>'api',
    'prefix'=>'authh'
], function($router){
    Route::post('register', [AuthController::class, 'apiRegister']);
    Route::post('login', [AuthController::class, 'apiLogin']);
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix'=>'products'
], function($router){
    Route::controller(ProductController::class)->group(function(){
        Route::get('index', 'apiIndex')->withoutMiddleware('auth:sanctum');
        Route::get('show/{id}', 'apiShow');
        Route::post('store', 'apiStore');
        Route::put('edit/{id}', 'apiEdit');
        Route::delete('delete/{id}', 'apiDestroy');
    });
});

Route::group([
    'middleware' => 'auth:sanctum',
    'prefix' => 'orders'
], function ($router){
    Route::controller(OrdersController::class)->group(function(){
        Route::get('index', 'apiShowOrdersForUser');
        Route::get('show/{id}', 'apiShow');
        Route::get('showOrders', 'apiShowOrdersForUser');
        Route::post('store', 'apiStore');
        Route::put('edit/{id}', 'apiEdit');
        Route::delete('delete/{id}', 'apiDestroy');
    });
});

Route::group([
    'middleware' => "auth:sanctum",
    'prefix' => 'comments',
], function($router){
    Route::controller(CommentsController::class)->group(function(){
        Route::get('index', 'apiIndex');
        Route::get('show/{id}', 'apiShow')->withoutMiddleware('auth:sanctum');
        Route::post('store/{id}', 'apiStore');
        Route::delete('delete/{id}', 'apiDestroy');
    });
});

