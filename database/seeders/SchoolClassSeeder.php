<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sortOrder = 0;

        foreach ([
            'X' => ['10A', '10B', 'X.1', 'X.2'],
            'XI' => ['11', 'XI.1', 'XI.2'],
            'XII' => ['12', 'XII.1', 'XII.2'],
        ] as $grade => $names) {
            foreach ($names as $name) {
                SchoolClass::updateOrCreate(
                    ['name' => $name],
                    ['grade' => $grade, 'sort_order' => ++$sortOrder, 'is_active' => true],
                );
            }
        }
    }
}
