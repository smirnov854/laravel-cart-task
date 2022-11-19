<?php

use Database\Seeders\CartItemSeeder;
use Database\Seeders\CartSeeder;
use Database\Seeders\ProductAdditionalParamsSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ProductCategoriesSeeder;
use Database\Seeders\ProductsSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DatabaseSeeder extends Seeder
{
    use RefreshDatabase;
    public function run()
    {
        $this->call([
            UserSeeder::class,
            ProductCategoriesSeeder::class,
            ProductAdditionalParamsSeeder::class,
            ProductsSeeder::class,
            CartSeeder::class,
            CartItemSeeder::class
        ]);

    }
}
