<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/chamados', fn () => Inertia::render('ModulePlaceholder', [
        'title' => 'Chamados',
        'description' => 'Fluxo de atendimento e acompanhamento de solicitações internas.',
    ]))->name('tickets.index');

    Route::get('/comunicados', fn () => Inertia::render('ModulePlaceholder', [
        'title' => 'Comunicados',
        'description' => 'Publicação e leitura de avisos internos.',
    ]))->name('announcements.index');

    Route::get('/conhecimento', fn () => Inertia::render('ModulePlaceholder', [
        'title' => 'Base de conhecimento',
        'description' => 'Documentação operacional, processos e respostas recorrentes.',
    ]))->name('knowledge.index');

    Route::get('/colaboradores', fn () => Inertia::render('ModulePlaceholder', [
        'title' => 'Colaboradores',
        'description' => 'Diretório e dados básicos do time.',
    ]))->name('people.index');
});
