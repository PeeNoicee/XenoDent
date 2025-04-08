<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthControl;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



//For Xray App

Route::get('/xrayLanding/{id}',[AuthControl::class, 'getLandingPage']);
Route::get('/homepage',[AuthControl::class, 'home'])->name('homepage');
Route::get('/premiumPage',[AuthControl::class, 'premium'])->name('premium');
Route::match(['get','patch','post'],'/updateUSer',[AuthControl::class, 'updateUSer'])->name('updateUSer');