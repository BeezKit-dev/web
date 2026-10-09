<?php

use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\EnsureRegistrationIsOpen;
use App\Http\Middleware\RedirectToRegisterWhenNoUsers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::livewire('/login', 'pages::login')
        ->middleware(RedirectToRegisterWhenNoUsers::class)
        ->name('login');
    Route::livewire('/register', 'pages::register')
        ->middleware(EnsureRegistrationIsOpen::class)
        ->name('register');
});

Route::view('/dashboard', 'dashboard')->middleware('auth')->name('dashboard');

Route::match(['get', 'post'], '/logout', LogoutController::class)->name('logout');
