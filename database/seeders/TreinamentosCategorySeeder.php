<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TreinamentosCategorySeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::query()->where('slug', 'treinamentos')->first();

        if (! $department) {
            return;
        }

        $categories = [
            ['name' => 'Video e Playlist', 'description' => 'Playlists e videos de treinamento.'],
            ['name' => 'Licenciamento', 'description' => 'Guias de licenciamento de sistemas.'],
            ['name' => 'Seguranca', 'description' => 'Treinamentos de seguranca biometrica e controle de acesso.'],
            ['name' => 'Metodologia e Processos', 'description' => 'Metodologias e processos de melhoria continua.'],
            ['name' => 'Referencia e Comandos', 'description' => 'Guias de referencia e comandos tecnicos.'],
            ['name' => 'Multimidia e CFTV', 'description' => 'Treinamentos sobre sistemas de circuito fechado de TV.'],
            ['name' => 'Testes e QA', 'description' => 'Metodologias e praticas de testes de software.'],
        ];

        foreach ($categories as $index => $category) {
            DocumentCategory::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'department_id' => $department->id,
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
