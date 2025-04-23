<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthControl;
use App\Http\Controllers\PageController;
use App\Http\Controllers\XrayControl;
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



//For Xray App Auth User Controller
Route::get('/xrayLanding/{id}',[AuthControl::class, 'getLandingPage']);
Route::match(['get','patch','post'],'/updateUSer',[AuthControl::class, 'updateUSer'])->name('updateUSer');

//For Pages
Route::get('/homepage',[PageController::class, 'home'])->name('homepage');
Route::get('/premiumPage',[PageController::class, 'premium'])->name('premium');
Route::match(['get','patch','post'],'/xrayPage', [PageController::class, 'xray'])->name('xray');

//For Xray image API
Route::match(['get','patch','post'],'/uploadImage', [XrayControl::class, 'upload'])->name('upload');
Route::match(['get','patch','post'],'/getImages', [XrayControl::class, 'getImages'])->name('getImages');

