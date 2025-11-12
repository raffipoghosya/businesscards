<?php

use App\Http\Controllers\Admin\CardController; 
use App\Http\Controllers\ProfileController; 
use App\Http\Controllers\PublicCardController; 
use Illuminate\Support\Facades\Route;


require __DIR__.'/auth.php';

// Redirect root to dashboard after auth routes are loaded
Route::get('/', function () {
    return redirect()->route('dashboard'); 
});

Route::middleware('auth')->group(function () {
    
    // Dashboard (Ցանկ)
    Route::get('/dashboard', [CardController::class, 'index'])->name('dashboard');
    
    // CRUD (Create, Read, Update, Delete)
    Route::resource('cards', CardController::class);
    
    // QR կոդը ներբեռնելու համար
    Route::get('/cards/{card}/qr-download', [CardController::class, 'downloadQr'])->name('cards.qr.download');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/{card:slug}', [PublicCardController::class, 'show'])
    ->name('card.public.show')
    ->where('slug', '^(?!icons|storage|build|css|js|images|img|vendor).*$');