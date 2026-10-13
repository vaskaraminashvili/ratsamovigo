<?php

use App\Livewire\Lists\Index;
use App\Livewire\Lists\Join;
use App\Livewire\Lists\Preview;
use App\Livewire\Lists\Show;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::get('view/{token}', Preview::class)->name('lists.view');
Route::get('join/{token}', Join::class)->name('lists.join');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('dashboard', '/lists')->name('dashboard');
    Route::get('lists', Index::class)->name('lists.index');
    Route::get('lists/{shoppingList}', Show::class)->name('lists.show');
});

require __DIR__.'/settings.php';
