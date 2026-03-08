<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Mbak Mey',
                'username' => 'mey',
                'role'     => 'purchase',
                'password' => Hash::make('ptmid2026'),
            ],
            [
                'name'     => 'Pak Hadi',
                'username' => 'hadi',
                'role'     => 'gudang',
                'password' => Hash::make('ptmid2026'),
            ],
            [
                'name'     => 'Pak Yudi',
                'username' => 'yudi',
                'role'     => 'super_admin',
                'password' => Hash::make('ptmid2026'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
