<?php

use App\Controllers\HomeController;
use App\Controllers\LoginController;
use App\Controllers\TestController;
use App\Controllers\PetController;
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
        Route::get('/my/pets', [PetController::class, 'index'])->name('user.pets.view');
        Route::get('/my/pets/edit/{id}', [PetController::class, 'edit'])->name('user.pets.edit');
        Route::get('/my/pets/new', [PetController::class, 'newForm'])->name('user.pets.create');
        Route::post('/my/pets/create', [PetController::class, 'create']);
        Route::put('/my/pets/update/{id}', [PetController::class, 'update']);
        Route::delete('/my/pets/delete/{id}', [PetController::class, 'delete'])->name('user.pets.delete');
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
