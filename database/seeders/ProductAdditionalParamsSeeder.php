<?php

namespace Database\Seeders;

use App\Models\ProductAdditionalParams;
use Illuminate\Database\Seeder;

class ProductAdditionalParamsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ProductAdditionalParams::factory()->count(100)->create();
    }
}
