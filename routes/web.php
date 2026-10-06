<?php

use App\Http\Controllers\CycleEntryController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionnaireController;
use App\Http\Controllers\ReminderController;
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
        Route::get('/paciente', PatientDashboardController::class)->name('patient.dashboard');
        Route::get('/exames', ExamController::class)->name('exams.index');
        Route::get('/historico', HistoryController::class)->name('history.index');

        Route::get('/ciclo', [CycleEntryController::class, 'index'])->name('cycle.index');
        Route::get('/ciclo/novo', [CycleEntryController::class, 'create'])->name('cycle.create');
        Route::post('/ciclo', [CycleEntryController::class, 'store'])->name('cycle.store');
        Route::get('/ciclo/{id}/editar', [CycleEntryController::class, 'edit'])->whereNumber('id')->name('cycle.edit');
        Route::put('/ciclo/{id}', [CycleEntryController::class, 'update'])->whereNumber('id')->name('cycle.update');
        Route::delete('/ciclo/{id}', [CycleEntryController::class, 'destroy'])->whereNumber('id')->name('cycle.destroy');

        Route::get('/lembretes', [ReminderController::class, 'index'])->name('reminders.index');
        Route::get('/lembretes/novo', [ReminderController::class, 'create'])->name('reminders.create');
        Route::post('/lembretes', [ReminderController::class, 'store'])->name('reminders.store');
        Route::get('/lembretes/{id}/editar', [ReminderController::class, 'edit'])->whereNumber('id')->name('reminders.edit');
        Route::put('/lembretes/{id}', [ReminderController::class, 'update'])->whereNumber('id')->name('reminders.update');
        Route::patch('/lembretes/{id}/concluir', [ReminderController::class, 'toggle'])->whereNumber('id')->name('reminders.toggle');
        Route::delete('/lembretes/{id}', [ReminderController::class, 'destroy'])->whereNumber('id')->name('reminders.destroy');

        Route::get('/questionario', [QuestionnaireController::class, 'create'])->name('questionnaires.create');
        Route::post('/questionario', [QuestionnaireController::class, 'store'])->name('questionnaires.store');
        Route::get('/questionario/historico', [QuestionnaireController::class, 'index'])->name('questionnaires.index');
        Route::get('/questionario/{id}', [QuestionnaireController::class, 'show'])->whereNumber('id')->name('questionnaires.show');
    });

    Route::middleware('role:professional')->group(function () {
        Route::get('/profissional', fn () => Inertia::render('Professional/Dashboard'))->name('professional.dashboard');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
