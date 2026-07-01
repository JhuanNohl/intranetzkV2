<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TreinamentosController extends Controller
{
    public function index(Request $request): Response
    {
        $department = Department::query()->where('slug', 'treinamentos')->firstOrFail();

        $documents = Document::query()
            ->where('department_id', $department->id)
            ->with('category')
            ->where(function ($query) use ($request) {
                $query->where('visibility', 'public')
                    ->orWhere(function ($query) use ($request) {
                        $query->where('visibility', 'department')
                            ->where('department_id', $request->user()?->department_id);
                    });
            })
            ->orderBy('title')
            ->get();

        return Inertia::render('Treinamentos/Index', [
            'department' => [
                'id' => $department->id,
                'name' => $department->name,
            ],
            'cards' => $this->groupIntoCards($documents),
        ]);
    }

    private function groupIntoCards(Collection $documents): array
    {
        $groups = [];

        foreach ($documents as $document) {
            [$baseTitle, $partLabel] = $this->splitTitle($document->title);
            $key = Str::slug($baseTitle);
            $url = $this->documentUrl($document);

            $groups[$key] ??= [
                'title' => $baseTitle,
                'summary' => $document->summary,
                'category' => $document->category?->slug,
                'links' => [],
            ];

            $groups[$key]['links'][] = [
                'label' => $partLabel ?? 'Assistir',
                'url' => $url,
                'available' => $document->status === 'published' && $url !== null,
            ];
        }

        return array_values($groups);
    }

    private function splitTitle(string $title): array
    {
        if (preg_match('/^(.*)\s-\s(Parte\s?\d+)$/i', $title, $matches)) {
            return [trim($matches[1]), trim($matches[2])];
        }

        return [$title, null];
    }

    private function documentUrl(Document $document): ?string
    {
        if ($document->source_type === 'external') {
            return $document->external_url;
        }

        if ($document->source_type === 'upload' && $document->file_path) {
            return Storage::disk('public')->url($document->file_path);
        }

        return null;
    }
}
