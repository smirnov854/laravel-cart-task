<?php
namespace Database\Seeders;
use App\Models\ProductAdditionalParams;
use App\Models\ProductCategory;
use App\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Foundation\Testing\WithFaker;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    use WithFaker;
    public function run()
    {
        $categories = ProductCategory::whereHas('parentCategory.parentCategory')->pluck('id');
        $add_params = ProductAdditionalParams::all()->pluck('id');

        for($i = 1; $i <= 200; $i++) {
            $product = Product::factory()->create();
            $product->categories()->sync($categories->random(mt_rand(1,3)));
            for($z=0;$z<rand(3,6);$z++){
                $product->add_params()->sync($add_params->random());
            }
        }
    }
}
