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
            'email' => 's+Uprv1s0r@m55sg1s.E5G1S',
            'mot_de_passe' => Hash::make('S^u=pe(rV!s0r#2@25'),
            'role' => 'supermod'
        ]);
    }
}
