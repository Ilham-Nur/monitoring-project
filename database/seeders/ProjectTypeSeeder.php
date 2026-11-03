<?php

namespace Database\Seeders;

use App\Models\ProjectType;
use Illuminate\Database\Seeder;

class ProjectTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $type = ProjectType::updateOrCreate(['name' => 'Project Standar'], [
            'description' => 'Tahapan awal yang dapat disesuaikan oleh Admin.',
            'is_active' => true,
        ]);

        foreach ([
            ['name' => 'Persiapan Dokumen', 'duration_days' => 3],
            ['name' => 'Pemeriksaan Dokumen', 'duration_days' => 5],
            ['name' => 'Pelaksanaan', 'duration_days' => 7],
            ['name' => 'Penyusunan Laporan', 'duration_days' => 4],
            ['name' => 'Penyelesaian', 'duration_days' => 2],
        ] as $index => $stage) {
            $type->stages()->updateOrCreate(
                ['sort_order' => $index + 1],
                $stage,
            );
        }
    }
}
