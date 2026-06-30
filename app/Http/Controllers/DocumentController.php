<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    private const FIXED_CONTENT_DEPARTMENT_SLUGS = ['ti', 't-i', 'manutencao', 'fabrica'];

    public function index(Request $request, ?Department $department = null): Response
    {
        $filters = $request->only(['search', 'department_id', 'category_id', 'status', 'source_type']);

        $documents = Document::query()
            ->with(['department.area', 'category', 'creator'])
            ->whereDoesntHave('department', fn ($query) => $query->whereIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS))
            ->when($department, function ($query) use ($department) {
                $query->where(function ($query) use ($department) {
                    $query->where('department_id', $department->id)
                        ->orWhereHas('visibleDepartments', fn ($query) => $query->whereKey($department->id));
                });
            })
            ->when(! $department && $request->filled('department_id'), fn ($query) => $query->where('department_id', $request->integer('department_id')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('document_category_id', $request->integer('category_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('source_type'), fn ($query) => $query->where('source_type', $request->input('source_type')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function ($query) use ($search) {
                    $query->where('title', 'ilike', "%{$search}%")
                        ->orWhere('summary', 'ilike', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Document $document) => $this->documentSummary($document));

        return Inertia::render('Documents/Index', [
            'documents' => $documents,
            'filters' => $filters,
            'selectedDepartment' => $department ? $this->departmentOption($department->loadMissing('area')) : null,
            'departments' => $this->departmentOptions(),
            'categories' => $this->categoryOptions(),
            'statusOptions' => $this->statusOptions(),
            'sourceTypeOptions' => $this->sourceTypeOptions(),
        ]);
    }

    public function create(Request $request): Response
    {
        $department = $request->filled('department_id')
            ? Department::query()
                ->with('area')
                ->whereNotIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS)
                ->find($request->integer('department_id'))
            : null;

        return Inertia::render('Documents/Create', [
            'document' => $this->blankDocument($department),
            'departments' => $this->departmentOptions(),
            'categories' => $this->categoryOptions(),
            'statusOptions' => $this->statusOptions(),
            'sourceTypeOptions' => $this->sourceTypeOptions(),
            'documentTypeOptions' => $this->documentTypeOptions(),
            'visibilityOptions' => $this->visibilityOptions(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $document = DB::transaction(function () use ($request) {
            $data = $this->documentData($request);
            $data['slug'] = $this->uniqueSlug($request->string('title')->toString());
            $data['created_by'] = $request->user()->id;
            $data['updated_by'] = $request->user()->id;

            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = now();
            }

            if ($request->hasFile('file')) {
                $this->applyUploadedFile($request, $data);
            }

            $document = Document::query()->create($data);
            $this->syncVisibleDepartments($document, $request->input('visible_department_ids', []));
            $this->createVersion($document, $request->user()->id);

            return $document;
        });

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Documento criado com sucesso.');
    }

    public function show(Document $document): Response
    {
        $document->load(['department.area', 'category', 'creator', 'updater', 'approver', 'visibleDepartments', 'versions.creator']);

        return Inertia::render('Documents/Show', [
            'document' => $this->documentDetail($document),
        ]);
    }

    public function edit(Document $document): Response
    {
        $document->load(['visibleDepartments']);

        return Inertia::render('Documents/Edit', [
            'document' => $this->documentFormData($document),
            'departments' => $this->departmentOptions(),
            'categories' => $this->categoryOptions(),
            'statusOptions' => $this->statusOptions(),
            'sourceTypeOptions' => $this->sourceTypeOptions(),
            'documentTypeOptions' => $this->documentTypeOptions(),
            'visibilityOptions' => $this->visibilityOptions(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        DB::transaction(function () use ($request, $document) {
            $data = $this->documentData($request);
            $data['updated_by'] = $request->user()->id;

            if ($document->title !== $data['title']) {
                $data['slug'] = $this->uniqueSlug($data['title'], $document);
            }

            if ($data['status'] === 'published' && empty($data['published_at'])) {
                $data['published_at'] = $document->published_at ?? now();
            }

            if ($request->hasFile('file')) {
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $this->applyUploadedFile($request, $data);
            }

            if ($data['source_type'] !== 'upload' && ! $request->hasFile('file')) {
                $data['file_path'] = null;
                $data['original_filename'] = null;
                $data['mime_type'] = null;
                $data['size_bytes'] = null;
            }

            $document->update($data);
            $this->syncVisibleDepartments($document, $request->input('visible_department_ids', []));
        });

        return redirect()
            ->route('documents.show', $document)
            ->with('success', 'Documento atualizado com sucesso.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        DB::transaction(function () use ($document) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->versions()
                ->whereNotNull('file_path')
                ->pluck('file_path')
                ->each(fn (string $path) => Storage::disk('public')->delete($path));

            $document->delete();
        });

        return redirect()
            ->route('documents.index')
            ->with('success', 'Documento removido com sucesso.');
    }

    private function documentData(StoreDocumentRequest|UpdateDocumentRequest $request): array
    {
        $this->ensureDocumentDepartmentCanUseCrud($request);

        return [
            'department_id' => $request->filled('department_id') ? $request->integer('department_id') : null,
            'document_category_id' => $request->filled('document_category_id') ? $request->integer('document_category_id') : null,
            'title' => $request->string('title')->toString(),
            'summary' => $request->input('summary'),
            'content' => $request->input('content'),
            'source_type' => $request->input('source_type'),
            'document_type' => $request->input('document_type'),
            'external_url' => $request->input('source_type') === 'external' ? $request->input('external_url') : null,
            'status' => $request->input('status'),
            'visibility' => $request->input('visibility'),
            'version' => $request->input('version', '1.0'),
            'requires_read_confirmation' => $request->boolean('requires_read_confirmation'),
            'is_featured' => $request->boolean('is_featured'),
            'published_at' => $request->input('published_at'),
            'expires_at' => $request->input('expires_at'),
        ];
    }

    private function applyUploadedFile(StoreDocumentRequest|UpdateDocumentRequest $request, array &$data): void
    {
        $file = $request->file('file');

        $data['file_path'] = $file->store('documents', 'public');
        $data['original_filename'] = $file->getClientOriginalName();
        $data['mime_type'] = $file->getClientMimeType();
        $data['size_bytes'] = $file->getSize();
        $data['external_url'] = null;
    }

    private function createVersion(Document $document, int $userId): void
    {
        $document->versions()->create([
            'version' => $document->version,
            'title' => $document->title,
            'file_path' => $document->file_path,
            'original_filename' => $document->original_filename,
            'mime_type' => $document->mime_type,
            'size_bytes' => $document->size_bytes,
            'external_url' => $document->external_url,
            'content' => $document->content,
            'change_summary' => 'Versão inicial',
            'created_by' => $userId,
        ]);
    }

    private function syncVisibleDepartments(Document $document, array $departmentIds): void
    {
        $syncData = collect($departmentIds)
            ->filter()
            ->unique()
            ->reject(fn ($id) => Department::query()
                ->whereKey((int) $id)
                ->whereIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS)
                ->exists())
            ->mapWithKeys(fn ($id) => [(int) $id => ['permission' => 'view']])
            ->all();

        $document->visibleDepartments()->sync($syncData);
    }

    private function uniqueSlug(string $title, ?Document $document = null): string
    {
        $baseSlug = Str::slug($title) ?: 'documento';
        $slug = $baseSlug;
        $counter = 2;

        while (Document::query()
            ->where('slug', $slug)
            ->when($document, fn ($query) => $query->whereKeyNot($document->id))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    private function documentSummary(Document $document): array
    {
        return [
            'id' => $document->id,
            'title' => $document->title,
            'summary' => $document->summary,
            'status' => $document->status,
            'visibility' => $document->visibility,
            'source_type' => $document->source_type,
            'document_type' => $document->document_type,
            'version' => $document->version,
            'is_featured' => $document->is_featured,
            'updated_at' => optional($document->updated_at)->format('d/m/Y H:i'),
            'file_meta' => $this->documentFileMeta($document),
            'file_url' => $document->file_path ? Storage::disk('public')->url($document->file_path) : null,
            'original_filename' => $document->original_filename,
            'department' => $document->department ? $this->departmentOption($document->department) : null,
            'category' => $document->category ? $this->categoryOption($document->category) : null,
            'creator' => $document->creator?->name,
        ];
    }

    private function documentFileMeta(Document $document): array
    {
        if ($document->source_type === 'external') {
            return [
                'label' => 'LINK',
                'type' => 'Link externo',
                'icon' => 'bi bi-link-45deg',
                'class' => 'bg-sky-500/10 text-sky-700 dark:bg-sky-500/15 dark:text-sky-300',
            ];
        }

        if ($document->source_type === 'content') {
            return [
                'label' => 'TXT',
                'type' => 'Texto interno',
                'icon' => 'bi bi-file-text',
                'class' => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
            ];
        }

        $extension = Str::lower(pathinfo((string) $document->original_filename, PATHINFO_EXTENSION));
        $mimeType = Str::lower((string) $document->mime_type);

        $types = [
            'pdf' => ['PDF', 'Arquivo PDF', 'bi bi-file-earmark-pdf', 'bg-red-500/10 text-red-700 dark:bg-red-500/15 dark:text-red-300'],
            'doc' => ['DOC', 'Documento Word', 'bi bi-file-earmark-word', 'bg-blue-500/10 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'],
            'docx' => ['DOCX', 'Documento Word', 'bi bi-file-earmark-word', 'bg-blue-500/10 text-blue-700 dark:bg-blue-500/15 dark:text-blue-300'],
            'txt' => ['TXT', 'Arquivo de texto', 'bi bi-filetype-txt', 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300'],
            'xls' => ['XLS', 'Planilha Excel', 'bi bi-file-earmark-excel', 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'],
            'xlsx' => ['XLSX', 'Planilha Excel', 'bi bi-file-earmark-excel', 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'],
            'csv' => ['CSV', 'Arquivo CSV', 'bi bi-filetype-csv', 'bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300'],
            'xml' => ['XML', 'Arquivo XML', 'bi bi-filetype-xml', 'bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300'],
            'html' => ['HTML', 'Arquivo HTML', 'bi bi-filetype-html', 'bg-orange-500/10 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300'],
            'htm' => ['HTML', 'Arquivo HTML', 'bi bi-filetype-html', 'bg-orange-500/10 text-orange-700 dark:bg-orange-500/15 dark:text-orange-300'],
            'png' => ['PNG', 'Imagem PNG', 'bi bi-file-earmark-image', 'bg-violet-500/10 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'],
            'jpg' => ['JPG', 'Imagem JPG', 'bi bi-file-earmark-image', 'bg-violet-500/10 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'],
            'jpeg' => ['JPG', 'Imagem JPG', 'bi bi-file-earmark-image', 'bg-violet-500/10 text-violet-700 dark:bg-violet-500/15 dark:text-violet-300'],
        ];

        if (! $extension && str_contains($mimeType, 'pdf')) {
            $extension = 'pdf';
        } elseif (! $extension && str_contains($mimeType, 'word')) {
            $extension = 'docx';
        } elseif (! $extension && (str_contains($mimeType, 'excel') || str_contains($mimeType, 'spreadsheet'))) {
            $extension = 'xlsx';
        } elseif (! $extension && str_contains($mimeType, 'image/')) {
            $extension = 'png';
        } elseif (! $extension && str_contains($mimeType, 'xml')) {
            $extension = 'xml';
        } elseif (! $extension && str_contains($mimeType, 'html')) {
            $extension = 'html';
        } elseif (! $extension && str_contains($mimeType, 'text/')) {
            $extension = 'txt';
        }

        [$label, $type, $icon, $class] = $types[$extension] ?? [
            $extension ? Str::upper($extension) : 'ARQ',
            'Arquivo',
            'bi bi-file-earmark',
            'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        ];

        return compact('label', 'type', 'icon', 'class');
    }

    private function documentDetail(Document $document): array
    {
        return [
            ...$this->documentSummary($document),
            'content' => $document->content,
            'external_url' => $document->external_url,
            'file_url' => $document->file_path ? Storage::disk('public')->url($document->file_path) : null,
            'original_filename' => $document->original_filename,
            'mime_type' => $document->mime_type,
            'size_bytes' => $document->size_bytes,
            'requires_read_confirmation' => $document->requires_read_confirmation,
            'published_at' => optional($document->published_at)->format('d/m/Y H:i'),
            'expires_at' => optional($document->expires_at)->format('d/m/Y H:i'),
            'visible_departments' => $document->visibleDepartments->map(fn (Department $department) => $this->departmentOption($department))->values(),
            'versions' => $document->versions->map(fn ($version) => [
                'id' => $version->id,
                'version' => $version->version,
                'title' => $version->title,
                'change_summary' => $version->change_summary,
                'creator' => $version->creator?->name,
                'created_at' => optional($version->created_at)->format('d/m/Y H:i'),
            ])->values(),
        ];
    }

    private function documentFormData(Document $document): array
    {
        return [
            'id' => $document->id,
            'department_id' => $document->department_id,
            'document_category_id' => $document->document_category_id,
            'title' => $document->title,
            'summary' => $document->summary,
            'content' => $document->content,
            'source_type' => $document->source_type,
            'document_type' => $document->document_type,
            'external_url' => $document->external_url,
            'status' => $document->status,
            'visibility' => $document->visibility,
            'version' => $document->version,
            'requires_read_confirmation' => $document->requires_read_confirmation,
            'is_featured' => $document->is_featured,
            'published_at' => optional($document->published_at)->format('Y-m-d\TH:i'),
            'expires_at' => optional($document->expires_at)->format('Y-m-d\TH:i'),
            'visible_department_ids' => $document->visibleDepartments->pluck('id')->values(),
            'original_filename' => $document->original_filename,
        ];
    }

    private function blankDocument(?Department $department): array
    {
        return [
            'department_id' => $department?->id,
            'document_category_id' => null,
            'title' => '',
            'summary' => '',
            'content' => '',
            'source_type' => 'upload',
            'document_type' => 'other',
            'external_url' => '',
            'status' => 'draft',
            'visibility' => 'department',
            'version' => '1.0',
            'requires_read_confirmation' => false,
            'is_featured' => false,
            'published_at' => '',
            'expires_at' => '',
            'visible_department_ids' => [],
            'original_filename' => null,
        ];
    }

    private function departmentOptions()
    {
        return Department::query()
            ->with('area')
            ->whereNotIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Department $department) => $this->departmentOption($department));
    }

    private function categoryOptions()
    {
        return DocumentCategory::query()
            ->with('department')
            ->whereDoesntHave('department', fn ($query) => $query->whereIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (DocumentCategory $category) => $this->categoryOption($category));
    }

    private function departmentOption(Department $department): array
    {
        return [
            'id' => $department->id,
            'name' => $department->name,
            'slug' => $department->slug,
            'area' => $department->area?->name,
        ];
    }

    private function categoryOption(DocumentCategory $category): array
    {
        return [
            'id' => $category->id,
            'department_id' => $category->department_id,
            'name' => $category->name,
            'slug' => $category->slug,
        ];
    }

    private function statusOptions(): array
    {
        return [
            ['value' => 'draft', 'label' => 'Rascunho'],
            ['value' => 'published', 'label' => 'Publicado'],
            ['value' => 'archived', 'label' => 'Arquivado'],
        ];
    }

    private function sourceTypeOptions(): array
    {
        return [
            ['value' => 'upload', 'label' => 'Arquivo'],
            ['value' => 'external', 'label' => 'Link externo'],
            ['value' => 'content', 'label' => 'Conteúdo interno'],
        ];
    }

    private function documentTypeOptions(): array
    {
        return [
            ['value' => 'policy', 'label' => 'Política'],
            ['value' => 'procedure', 'label' => 'Procedimento'],
            ['value' => 'manual', 'label' => 'Manual'],
            ['value' => 'form', 'label' => 'Formulário'],
            ['value' => 'other', 'label' => 'Outro'],
        ];
    }

    private function visibilityOptions(): array
    {
        return [
            ['value' => 'department', 'label' => 'Departamento'],
            ['value' => 'shared', 'label' => 'Compartilhado'],
            ['value' => 'public', 'label' => 'Público'],
        ];
    }

    private function ensureDocumentDepartmentCanUseCrud(StoreDocumentRequest|UpdateDocumentRequest $request): void
    {
        if (! $request->filled('department_id')) {
            return;
        }

        $isFixedContentDepartment = Department::query()
            ->whereKey($request->integer('department_id'))
            ->whereIn('slug', self::FIXED_CONTENT_DEPARTMENT_SLUGS)
            ->exists();

        if ($isFixedContentDepartment) {
            throw ValidationException::withMessages([
                'department_id' => 'O setor de T.I. utiliza conteúdo fixo e não aceita arquivos pelo CRUD.',
            ]);
        }
    }
}
