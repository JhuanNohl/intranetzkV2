<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AreaDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $structure = [
            'Corporativo' => [
                'Comercial',
                'Departamento Pessoal',
                'Financeiro',
            ],
            'Área Técnica' => [
                'Desenvolvimento',
                'Suporte',
                'T.I.',
                'Treinamentos',
            ],
            'Operacional' => [
                'Fábrica',
                'Manutenção',
                'Produtos',
            ],
        ];

        $areaOrder = 1;

        foreach ($structure as $areaName => $departments) {
            $area = Area::updateOrCreate(
                ['slug' => Str::slug($areaName)],
                [
                    'name' => $areaName,
                    'sort_order' => $areaOrder++,
                    'is_active' => true,
                ]
            );

            $departmentOrder = 1;

            foreach ($departments as $departmentName) {
                Department::updateOrCreate(
                    ['slug' => Str::slug($departmentName)],
                    [
                        'area_id' => $area->id,
                        'name' => $departmentName,
                        'sort_order' => $departmentOrder++,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
