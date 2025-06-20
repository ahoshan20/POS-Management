<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::get('/home', function () {
    return view('welcome');
});
// Route::get('/', function () {
//     return view('components.layouts.app');
// });
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
Route::middleware('auth')->group(function () {

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});

// Route::get('/', function () {
//     return Auth::check() ? redirect()->route('welcome') : redirect()->route('login');
// });