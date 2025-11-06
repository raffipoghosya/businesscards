<?php

use Illuminate\Support\Facades\Route;
// Ավելացնում ենք մեր նոր կոնտրոլերը
use App\Http\Controllers\Admin\CardController;

// Մեր գլխավոր էջը թող լինի ադմինի ցանկը
Route::get('/', [CardController::class, 'index'])->name('dashboard');

// Սա ավտոմատ ստեղծում է բոլոր անհրաժեշտ հղումները
// /cards, /cards/create, /cards/{id}/edit և այլն։
Route::resource('cards', CardController::class);