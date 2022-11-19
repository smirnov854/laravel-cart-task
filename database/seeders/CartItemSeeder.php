<?php

namespace Database\Seeders;

use App\Models\CartItem;
use Illuminate\Database\Seeder;

class CartItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CartItem::factory()->count(100)->create();
    }
}
