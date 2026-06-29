<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Inertia\Inertia;
use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/departments/{department:slug}/documents', [DocumentController::class, 'index'])
        ->name('departments.documents.index');

    Route::resource('documents', DocumentController::class);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/', fn() => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/meu-perfil', fn() => Inertia::render('Profile/Show'))->name('profile.show');

    // Corporativo
    Route::get('/comercial', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Comercial',
        'description' => 'Gestão de oportunidades, clientes e funil de vendas.',
        'group'       => 'Corporativo',
    ]))->name('comercial.index');

    Route::get('/departamento-pessoal', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Departamento Pessoal',
        'description' => 'Admissões, férias, folha de pagamento e documentação de colaboradores.',
        'group'       => 'Corporativo',
    ]))->name('dp.index');

    Route::get('/financeiro', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Financeiro',
        'description' => 'Contas a pagar, a receber, fluxo de caixa e relatórios financeiros.',
        'group'       => 'Corporativo',
    ]))->name('financeiro.index');

    // Área Técnica
    Route::get('/desenvolvimento', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Desenvolvimento',
        'description' => 'Projetos de software, versionamento, deploys e documentação técnica.',
        'group'       => 'Área Técnica',
    ]))->name('desenvolvimento.index');

    Route::get('/suporte', fn() => Inertia::render('Support/Index'))->name('suporte.index');

    Route::get('/ti', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'T.I.',
        'description' => 'Infraestrutura, ativos, acessos e gestão de ambiente tecnológico.',
        'group'       => 'Área Técnica',
    ]))->name('ti.index');

    Route::get('/treinamentos', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Treinamentos',
        'description' => 'Trilhas de aprendizado, certificações e histórico de capacitações.',
        'group'       => 'Área Técnica',
    ]))->name('treinamentos.index');

    // Operacional
    Route::get('/fabrica', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Fábrica',
        'description' => 'Controle de produção, ordens de serviço e indicadores de chão de fábrica.',
        'group'       => 'Operacional',
    ]))->name('fabrica.index');

    Route::get('/manutencao', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Manutenção',
        'description' => 'Planos de manutenção preventiva, corretiva e histórico de equipamentos.',
        'group'       => 'Operacional',
    ]))->name('manutencao.index');

    Route::get('/produtos', fn() => Inertia::render('ModulePlaceholder', [
        'title'       => 'Produtos',
        'description' => 'Catálogo, especificações técnicas e ciclo de vida de produtos.',
        'group'       => 'Operacional',
    ]))->name('produtos.index');
});
