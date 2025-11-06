<?php

use App\Http\Controllers\Admin\CardController; // Մեր կոնտրոլերը
use App\Http\Controllers\ProfileController; // Breeze-ի կոնտրոլերը
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Սա ուղղակի վերահղում է root-ից (/) դեպի /dashboard
// Եթե օգտատերը բացի http://127.0.0.1:8000, կտեղափոխվի դեպի dashboard
Route::get('/', function () {
    // Մենք դեռ մուտքի middleware-ի տակ չենք, 
    // այնպես որ, սա ավտոմատ կտանի դեպի /login, եթե մուտք գործած չեք
    return redirect()->route('dashboard'); 
});

// Սա մեր ԱԴՄԻՆ վահանակն է, որը հասանելի է ՄԻԱՅՆ մուտք գործելուց հետո
Route::middleware('auth')->group(function () {
    
    // **Ահա ՇՏԿՈՒՄԸ:**
    // Breeze-ը փնտրում է /dashboard ՈՒՂԻՆ (path), 
    // այնպես որ, մենք ստեղծում ենք այն։
    Route::get('/dashboard', [CardController::class, 'index'])->name('dashboard');
    
    // Սա կառավարում է մեր բոլոր CRUD գործողությունները
    Route::resource('cards', CardController::class);
    
    // Սրանք Breeze-ի ավելացրած էջերն են (օրինակ՝ Profile խմբագրելը)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Սա ներառում է Login, Register, Password Reset էջերի route-երը
require __DIR__.'/auth.php';