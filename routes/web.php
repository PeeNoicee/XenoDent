<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthControl;
use App\Http\Controllers\PageController;
use App\Http\Controllers\XrayControl;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;

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
Route::match(['get','patch','post'],'/uploadImage', [XrayControl::class, 'upload'])->name('uploadImage');
Route::match(['get','patch','post'],'/getImages', [XrayControl::class, 'getImages'])->name('getImages');

Route::middleware(['auth'])->group(function () {
    Route::get('/xray-count', [XrayControl::class, 'getXrayCount'])->name('xray.count');
});

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/how-to-use', [PageController::class, 'howToUse'])->name('how-to-use');

Route::patch('/updateUser', [AuthControl::class, 'updateUser'])->name('updateUser');

// Upload routes - need web middleware for CSRF protection
Route::middleware(['web', 'auth'])->group(function () {
    Route::post('/analyze', [XrayControl::class, 'analyze'])->name('analyze');
});

//Uploading Xray image - temporarily outside middleware to debug 405 error
Route::post('/upload', [XrayControl::class, 'upload'])->name('upload');
Route::get('/patientManagement', [PatientController::class, 'displayPatientManagement'])->name('patientManagement');
Route::get('/patientManagement/create', [PatientController::class, 'create'])->name('addPatient');
Route::post('/patientManagement', [PatientController::class, 'store'])->name('storePatient');
Route::get('/patientManagement/{id}/edit', [PatientController::class, 'edit'])->name('editPatient');
Route::put('/patients/{id}', [PatientController::class, 'update'])->name('updatePatient');
Route::delete('/patientManagement/{id}', [PatientController::class, 'destroy'])->name('deletePatient');