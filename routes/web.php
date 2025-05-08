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
Route::get('/xrayLanding/{id}', [AuthControl::class, 'getLandingPage'])->name('xrayLanding');
Route::match(['get','patch','post'],'/updateUSer',[AuthControl::class, 'updateUSer'])->name('updateUSer');

//For Pages
Route::get('/homepage',[PageController::class, 'home'])->name('homepage');
Route::get('/premiumPage', [PageController::class, 'premium'])->name('premiumPage');
Route::match(['get','patch','post'],'/xrayPage', [PageController::class, 'xray'])->name('xray');

//For Xray image API
Route::match(['get','patch','post'],'/uploadImage', [XrayControl::class, 'upload'])->name('upload');
Route::match(['get','patch','post'],'/getImages', [XrayControl::class, 'getImages'])->name('getImages');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/how-to-use', [PageController::class, 'howToUse'])->name('how-to-use');

Route::patch('/updateUser', [AuthControl::class, 'updateUser'])->name('updateUser');

//Uploading Xray image
Route::post('/upload', [App\Http\Controllers\XrayControl::class, 'upload'])->name('upload');
