<?php

namespace Database\Seeders;

use App\Models\Uom;
use Illuminate\Database\Seeder;

class UomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $satuans = [
            'Rol',
            'Pack',
            'PCS',
            'Lembar',
            'M2',
            'SAK',
            'Set',
            'RIM',
            'Box',
            'Pail',
            'Unit',
            'Liter',
        ];

        foreach ($satuans as $satuan) {
            Uom::create(['nama_satuan' => $satuan]);
        }
    }
}
