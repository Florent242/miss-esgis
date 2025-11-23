<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            'nom' => 'Super',
            'email' => 'r3In3AdmIn@r3in3E5gI5.C0m',
            'mot_de_passe' => Hash::make("A@.2dmIn2347@R3Ine#VI@nn3y.67.@"), // change le mot de passe après
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
