<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', static function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', static function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('properties')->group(static function () {
    Route::get('/search', static function () {
        return Inertia::render('Properties/Search');
    })->name('properties.search');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
