<?php

use App\Http\Controllers\InfoController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/informacoes', [InfoController::class, 'index'])->name('info.index');
Route::get('/informacoes/{slug}', [InfoController::class, 'show'])->name('info.show');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', fn () => redirect(auth()->user()->homeRoute()))->name('dashboard');

    Route::middleware('role:patient')->group(function () {
        Route::get('/paciente', fn () => Inertia::render('Patient/Dashboard'))->name('patient.dashboard');
    });

    Route::middleware('role:professional')->group(function () {
        Route::get('/profissional', fn () => Inertia::render('Professional/Dashboard'))->name('professional.dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
