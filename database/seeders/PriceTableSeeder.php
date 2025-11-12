<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceTable;

class PriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PriceTable::create([
            'name' => 'Plano',
            'status' => 'active'
        ]);

        PriceTable::create([
            'name' => 'Particular',
            'status' => 'active'
        ]);
    }
}
