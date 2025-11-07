<?php

use App\Http\Controllers\Admin\CardController; // Մեր ադմին կոնտրոլերը
use App\Http\Controllers\ProfileController; // Breeze-ի կոնտրոլերը
use App\Http\Controllers\PublicCardController; // Մեր նոր PUBLIC կոնտրոլերը
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| ՈՒՇԱԴՐՈՒԹՅՈՒՆ: 
| 1. Auth-ի երթուղիները (login, register) պետք է լինեն վայրի քարտ (wildcard)
|    երթուղուց առաջ (օրինակ՝ /{card:slug}).
| 2. Վայրի քարտ երթուղին պետք է լինի ֆայլի ամենավերջում։
|
*/

// =======================================================================
// === 1. AUTH ROUTES (Login, Register...) ===
// =======================================================================
require __DIR__.'/auth.php';

// Redirect root to dashboard after auth routes are loaded
Route::get('/', function () {
    return redirect()->route('dashboard'); 
});

// =======================================================================
// === 2. ԱԴՄԻՆԻ ԲԱԺԻՆ (ՊԱՇՏՊԱՆՎԱԾ) ===
// =======================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard (Ցանկ)
    Route::get('/dashboard', [CardController::class, 'index'])->name('dashboard');
    
    // CRUD (Create, Read, Update, Delete)
    // ՓՈՓՈԽՈՒԹՅՈՒՆ: Հեռացրել ենք except(['destroy'])
    Route::resource('cards', CardController::class);
    
    // QR կոդը ներբեռնելու համար
    Route::get('/cards/{card}/qr-download', [CardController::class, 'downloadQr'])->name('cards.qr.download');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =======================================================================
// === 3. ՀԱՆՐԱՅԻՆ ԲԱԺԻՆ (ՎԱՅՐԻ ՔԱՐՏ - ՊԵՏՔ Է ԼԻՆԻ ԱՄԵՆԱՎԵՐՋՈՒՄ) ===
// =======================================================================
// Սա բռնում է բոլոր այն հղումները, որոնք չեն համապատասխանել վերևի երթուղիներին
Route::get('/{card:slug}', [PublicCardController::class, 'show'])->name('card.public.show');