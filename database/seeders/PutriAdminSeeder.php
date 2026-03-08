<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PutriAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'putri'],
            [
                'name'     => 'Mbak Putri',
                'password' => Hash::make('ptmid2026'),
                'role'     => 'admin',
            ]
        );
    }
}
