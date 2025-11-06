<?php

use App\Http\Controllers\Admin\CardController; // Մեր ադմին կոնտրոլերը
use App\Http\Controllers\ProfileController; // Breeze-ի կոնտրոլերը
use App\Http\Controllers\PublicCardController; // Մեր նոր PUBLIC կոնտրոլերը
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('dashboard'); 
});

// =======================================================================
// === ԱԴՄԻՆԻ ԲԱԺԻՆ (ՊԱՇՏՊԱՆՎԱԾ) ===
// =======================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard (Ցանկ)
    Route::get('/dashboard', [CardController::class, 'index'])->name('dashboard');
    
    // CRUD (Create, Read, Update, Delete)
    Route::resource('cards', CardController::class);
    
    // *** ԱՎԵԼԱՑՎԱԾ ՏՈՂ ***
    // Սա մեր նոր route-ն է QR կոդը ներբեռնելու համար
    Route::get('/cards/{card}/qr-download', [CardController::class, 'downloadQr'])->name('cards.qr.download');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =======================================================================
// === ՀԱՆՐԱՅԻՆ ԲԱԺԻՆ (ԲԱՑ ԲՈԼՈՐԻ ՀԱՄԱՐ) ===
// =======================================================================
Route::get('/{card:slug}', [PublicCardController::class, 'show'])->name('card.public.show');


// Auth routes (Login, Register...)
require __DIR__.'/auth.php';