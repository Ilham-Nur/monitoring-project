<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@monitoring.test'], [
            'name' => 'Administrator',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'company_name' => null,
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'pic@monitoring.test'], [
            'name' => 'PIC Project',
            'password' => Hash::make('password'),
            'role' => 'pic',
            'company_name' => null,
            'is_active' => true,
        ]);

        User::updateOrCreate(['email' => 'client@monitoring.test'], [
            'name' => 'Perwakilan Klien',
            'password' => Hash::make('password'),
            'role' => 'client',
            'company_name' => 'PT Contoh Klien',
            'is_active' => true,
        ]);

        $this->call(ProjectTypeSeeder::class);
    }
}
