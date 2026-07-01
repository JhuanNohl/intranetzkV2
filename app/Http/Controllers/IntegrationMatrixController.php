<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\IntegrationSystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class IntegrationMatrixController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Desenvolvimento/Index', [
            'stats' => $this->stats(),
            'latestEquipment' => $this->equipmentSummaries(
                Equipment::query()->with('systems')->latest()->take(10)->get()
            ),
        ]);
    }

    public function matrix(): Response
    {
        $systems = IntegrationSystem::query()->orderBy('name')->get();
        $equipment = Equipment::query()->with('systems')->orderBy('model')->get();

        return Inertia::render('Desenvolvimento/Matrix', [
            'stats' => $this->stats(),
            'systems' => $systems->map(fn (IntegrationSystem $system) => [
                'id' => $system->id,
                'name' => $system->name,
            ])->values(),
            'equipment' => $equipment->map(fn (Equipment $item) => [
                'id' => $item->id,
                'model' => $item->model,
                'compatibility' => $item->systems->mapWithKeys(fn (IntegrationSystem $system) => [
                    $system->id => $system->pivot->compatible_model,
                ]),
            ])->values(),
        ]);
    }

    public function equipment(): Response
    {
        return Inertia::render('Desenvolvimento/Equipment', [
            'stats' => $this->stats(),
            'equipment' => $this->equipmentSummaries(
                Equipment::query()->with('systems')->orderBy('model')->get()
            ),
        ]);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $rows = $this->parseCsv($request->file('file')->getRealPath());

        if (count($rows) < 2) {
            return back()->withErrors(['file' => 'O arquivo CSV está vazio ou não possui dados suficientes.']);
        }

        DB::transaction(function () use ($rows) {
            $header = array_shift($rows);
            $systemNames = array_slice($header, 1);

            DB::table('equipment_integration_system')->delete();
            DB::table('equipment')->delete();
            DB::table('integration_systems')->delete();

            $systemIds = [];

            foreach ($systemNames as $index => $name) {
                $name = trim((string) $name);

                if ($name === '') {
                    continue;
                }

                $systemIds[$index] = IntegrationSystem::query()->create(['name' => $name])->id;
            }

            foreach ($rows as $row) {
                $model = trim((string) ($row[0] ?? ''));

                if ($model === '') {
                    continue;
                }

                $equipment = Equipment::query()->firstOrCreate(['model' => $model]);

                $compatibility = [];

                foreach ($systemIds as $index => $systemId) {
                    $value = trim((string) ($row[$index + 1] ?? ''));

                    if ($value === '') {
                        continue;
                    }

                    $compatibility[$systemId] = ['compatible_model' => $value];
                }

                if ($compatibility !== []) {
                    $equipment->systems()->sync($compatibility);
                }
            }
        });

        return back()->with('success', 'Matriz de integrações importada com sucesso.');
    }

    private function stats(): array
    {
        return [
            'systems_count' => IntegrationSystem::query()->count(),
            'equipment_count' => Equipment::query()->count(),
            'compatibilities_count' => DB::table('equipment_integration_system')->count(),
        ];
    }

    private function equipmentSummaries(Collection $equipment): array
    {
        return $equipment->map(function (Equipment $item) {
            $systemNames = $item->systems->pluck('name')->values();

            return [
                'id' => $item->id,
                'model' => $item->model,
                'compatible_systems_count' => $systemNames->count(),
                'compatible_systems_preview' => $systemNames->take(3)->all(),
                'compatible_systems_more' => max($systemNames->count() - 3, 0),
                'created_at' => optional($item->created_at)->format('d/m/Y H:i'),
            ];
        })->values()->all();
    }

    private function parseCsv(string $path): array
    {
        $contents = file_get_contents($path);
        $contents = $this->fixMojibake($contents);
        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        $rows = [];

        while (($row = fgetcsv($stream, 0, ';')) !== false) {
            if ($row === [null]) {
                continue;
            }

            $rows[] = $row;
        }

        fclose($stream);

        return $rows;
    }

    private function fixMojibake(string $value): string
    {
        $fixed = @mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');

        return $fixed !== false && mb_check_encoding($fixed, 'UTF-8') ? $fixed : $value;
    }
}
