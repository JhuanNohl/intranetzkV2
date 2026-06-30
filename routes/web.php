<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DocumentController;
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
    $departmentDocuments = function (string|array $slugs) {
        return function () use ($slugs) {
            $department = Department::query()
                ->whereIn('slug', (array) $slugs)
                ->firstOrFail();

            return app(DocumentController::class)->index(request(), $department);
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

    Route::get('/meu-perfil', fn () => Inertia::render('Profile/Show'))->name('profile.show');

    // Corporativo
    Route::get('/comercial', $departmentDocuments('comercial'))->name('comercial.index');
    Route::get('/departamento-pessoal', $departmentDocuments('departamento-pessoal'))->name('dp.index');
    Route::get('/financeiro', $departmentDocuments('financeiro'))->name('financeiro.index');

    // Area Tecnica
    Route::get('/desenvolvimento', $departmentDocuments('desenvolvimento'))->name('desenvolvimento.index');
    Route::get('/suporte', fn () => Inertia::render('Support/Index'))->name('suporte.index');
    Route::get('/ti', fn () => Inertia::render('TI/Index'))->name('ti.index');
    Route::get('/treinamentos', $departmentDocuments('treinamentos'))->name('treinamentos.index');

    // Operacional
    Route::get('/fabrica', fn () => Inertia::render('Fabrica/Index'))->name('fabrica.index');
    Route::get('/manutencao', fn () => Inertia::render('Manutencao/Index'))->name('manutencao.index');
    Route::get('/produtos', $departmentDocuments('produtos'))->name('produtos.index');
});
