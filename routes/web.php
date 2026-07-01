<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\IntegrationMatrixController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TreinamentosController;
use App\Models\Department;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/departments/{department:slug}/documents', function (Request $request, Department $department) {
        abort_if(in_array($department->slug, ['ti', 't-i', 'manutencao', 'fabrica'], true), 404);

        return app(DocumentController::class)->index($request, $department);
    })
        ->name('departments.documents.index');

    Route::resource('documents', DocumentController::class);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    $departmentDocuments = function (string|array $slugs, string $page = 'Documents/Index') {
        return function () use ($slugs, $page) {
            $department = Department::query()
                ->whereIn('slug', (array) $slugs)
                ->firstOrFail();

            return app(DocumentController::class)->index(request(), $department, $page);
        };
    };

    $departmentUrl = function (?string $slug): string {
        return match ($slug) {
            'comercial' => '/comercial',
            'departamento-pessoal' => '/departamento-pessoal',
            'financeiro' => '/financeiro',
            'desenvolvimento' => '/desenvolvimento',
            'suporte' => '/suporte',
            'treinamentos' => '/treinamentos',
            'fabrica' => '/fabrica',
            'manutencao' => '/manutencao',
            'produtos' => '/produtos',
            default => '/documents',
        };
    };

    Route::get('/search/preview', function (Request $request) use ($departmentUrl) {
        $search = trim((string) $request->query('search', ''));

        if (mb_strlen($search) < 2) {
            return response()->json(['documents' => []]);
        }

        $documents = Document::query()
            ->with(['department.area', 'category'])
            ->whereDoesntHave('department', fn ($query) => $query->whereIn('slug', ['ti', 't-i']))
            ->where(function ($query) use ($request) {
                $query->where('visibility', 'public')
                    ->orWhere(function ($query) use ($request) {
                        $query->where('visibility', 'department')
                            ->where('department_id', $request->user()?->department_id);
                    });
            })
            ->where(function ($query) use ($search) {
                $query->where('title', 'ilike', "%{$search}%")
                    ->orWhere('summary', 'ilike', "%{$search}%");
            })
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(function (Document $document) use ($departmentUrl) {
                $department = $document->department;
                $target = $departmentUrl($department?->slug);

                return [
                    'id' => $document->id,
                    'title' => $document->title,
                    'summary' => $document->summary,
                    'department' => $department?->name ?? 'Central de Arquivos',
                    'area' => $department?->area?->name,
                    'category' => $document->category?->name,
                    'href' => $target.'?search='.urlencode($document->title),
                ];
            })
            ->values();

        return response()->json(['documents' => $documents]);
    })->name('search.preview');

    Route::get('/', fn () => Inertia::render('Dashboard'))->name('dashboard');

    Route::get('/meu-perfil', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/meu-perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/meu-perfil/foto', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/meu-perfil/foto', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    // Corporativo
    Route::get('/comercial', $departmentDocuments('comercial', 'Comercial/Index'))->name('comercial.index');
    Route::get('/departamento-pessoal', $departmentDocuments('departamento-pessoal', 'DepartamentoPessoal/Index'))->name('dp.index');
    Route::get('/financeiro', $departmentDocuments('financeiro', 'Financeiro/Index'))->name('financeiro.index');

    // Area Tecnica
    Route::get('/desenvolvimento', [IntegrationMatrixController::class, 'index'])->name('desenvolvimento.index');
    Route::get('/desenvolvimento/matriz', [IntegrationMatrixController::class, 'matrix'])->name('desenvolvimento.matrix');
    Route::get('/desenvolvimento/equipamentos', [IntegrationMatrixController::class, 'equipment'])->name('desenvolvimento.equipment');
    Route::post('/desenvolvimento/importar', [IntegrationMatrixController::class, 'import'])->name('desenvolvimento.import');
    Route::get('/suporte', fn () => Inertia::render('Support/Index'))->name('suporte.index');
    Route::get('/ti', fn () => Inertia::render('TI/Index'))->name('ti.index');
    Route::get('/treinamentos', [TreinamentosController::class, 'index'])->name('treinamentos.index');

    // Operacional
    Route::get('/fabrica', fn () => Inertia::render('Fabrica/Index'))->name('fabrica.index');
    Route::get('/manutencao', fn () => Inertia::render('Manutencao/Index'))->name('manutencao.index');
    Route::get('/produtos', $departmentDocuments('produtos', 'Produtos/Index'))->name('produtos.index');
});
