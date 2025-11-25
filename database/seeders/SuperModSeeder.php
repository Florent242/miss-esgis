<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class SuperModSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'nom' => 'Super Moderateur',
            'email' => 'supervisor@missesgis.local',
            'mot_de_passe' => Hash::make('SuperV!s0r#2025'),
            'role' => 'supermod'
        ]);
    }
}
