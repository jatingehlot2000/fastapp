<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Login;
use App\Livewire\Dashboard;

// Default route redirects to dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Login route
Route::get('/login', Login::class)->name('login');

// Dashboard route
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// Logout route
Route::get('/logout', function () {
    \Illuminate\Support\Facades\Session::forget('admin_user');
    return redirect()->route('login');
})->name('logout');
