<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\TestController;
use Core\Router\Route;

// Authentication

Route::get('/login', [LoginController::class, 'index'])->name('login.view');
Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');

Route::middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('root');
});
Route::middleware('client')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/test/client', [TestController::class, 'client'])->name('test.client');
    });
});

Route::middleware('admin')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/test/admin', [TestController::class, 'admin'])->name('test.admin');
    });
});
Route::middleware('auth')->group(function () {
    Route::get('/logout', [LoginController::class, 'destroy'])->name('logout');
});
