<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Politicas', 'description' => 'Normas, regras internas e diretrizes corporativas.'],
            ['name' => 'Procedimentos', 'description' => 'Fluxos operacionais e instrucoes de execucao.'],
            ['name' => 'Manuais', 'description' => 'Materiais de referencia, guias e documentacao tecnica.'],
            ['name' => 'Formularios', 'description' => 'Modelos, formularios e documentos padronizados.'],
            ['name' => 'Comunicados', 'description' => 'Avisos, comunicacoes internas e informativos.'],
        ];

        foreach ($categories as $index => $category) {
            DocumentCategory::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'department_id' => null,
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );
        }
    }
}
