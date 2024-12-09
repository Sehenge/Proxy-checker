<?php

use App\Http\Controllers\ProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/proxies', [ProxyController::class, 'getAllProxies']);
Route::get('/active', [ProxyController::class, 'getActiveProxies']);
