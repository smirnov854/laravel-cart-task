<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductAdditionalParams;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiProductTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use WithFaker;

    public function test_product_get_all()
    {
        $response = $this->get('/api/products');
        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[[
                'name',
                'slug',
                'price',
                'description'
                ],
            ]
        ]);
    }

    public function test_get_product_by_slug_error(){
        $response = $this->get('/api/get-product-by-slug');
        $response->assertStatus(404);
    }

    public function test_get_product_by_slug_empty_post(){
        $response = $this->post('/api/get-product-by-slug');
        $response->assertStatus(422);
    }

    public function test_get_product_by_slug(){
        $product = Product::create([
            'name'=>'test-test-test',
            'description'=>$this->faker->paragraph,
            'price'=> mt_rand(99, 4999) / 100
        ]);
        $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        $response = $this->post('/api/get-product-by-slug',[
            'slug'=>$product->slug
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[
                'name',
                'slug',
                'price',
                'description'
            ]
        ]);
    }

    public function test_get_product_by_filters_error(){
        $response = $this->get('/api/get-product-by-filters');
        $response->assertStatus(404);
    }

    public function test_get_product_by_filters_min_price_error(){
        $response = $this->post('/api/get-product-by-filters',[
            'min_price'=>-1,
        ]);
        $response->assertStatus(422);
    }

    public function test_get_product_by_filters_max_price_error(){
        $response = $this->post('/api/get-product-by-filters',[
            'max_price'=>-1,
        ]);
        $response->assertStatus(422);
    }

    public function test_get_product_by_filters_man_price_error(){
        $response = $this->post('/api/get-product-by-filters',[
            'category_id'=>'test',
        ]);
        $response->assertStatus(422);
    }

    public function test_get_product_by_filters_by_cat(){
        $product = Product::create([
            'name'        => 'test-test-test',
            'description' => $this->faker->paragraph,
            'price'       => mt_rand(99, 4999) / 100,
        ]);

        $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        $response = $this->post('/api/get-product-by-filters',[
            'category_id'=>1,
        ]);

        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[[
                'name',
                'slug',
                'price',
                'description'
                ]
            ]
        ]);
    }

    public function test_get_product_by_filters_by_price(){
        $product = Product::create([
            'name'        => 'test-test-test',
            'description' => $this->faker->paragraph,
            'price'       => 150,
        ]);
        $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        $product->categories()->attach(ProductCategory::where('id','=',2)->first());
        $count = Product::where('price','>=',149)->where('price','<=',155)->count();

        $response = $this->post('/api/get-product-by-filters',[
            'min_price'=>149,
            'max_price'=>155
        ]);
        $value = json_decode($response->content());

        $this->assertEquals($count,$value->meta->total);
        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[[
                'name',
                'slug',
                'price',
                'description'
            ]
            ]
        ]);
    }


    public function test_get_product_by_filters_by_add_params(){


        $add_params = ProductAdditionalParams::create([
            'name'=>'test',
            'value'=>123
        ]);

        $response = $this->post('/api/get-product-by-filters',[
            'add_params'=>[[
                'name'=>$add_params->name,
                'value'=>$add_params->value
            ]
            ]
        ]);

        $tmp_data = json_decode($response->content());
        $before_add = $tmp_data->meta->total;

        $product = Product::create([
            'name'        => 'test-test-test',
            'description' => $this->faker->paragraph,
            'price'       => 150,
        ]);
        $product->categories()->attach(ProductCategory::where('id','=',1)->first());
        $product->categories()->attach(ProductCategory::where('id','=',2)->first());
        $product->add_params()->attach($add_params);



        $response = $this->post('/api/get-product-by-filters',[
            'add_params'=>[[
                'name'=>$add_params->name,
                'value'=>$add_params->value
                ]
            ]
        ]);

        $content = json_decode($response->content());

        $this->assertEquals($before_add+1,$content->meta->total);

        $response->assertStatus(200)->assertJsonStructure([
            'data'=>[[
                'name',
                'slug',
                'price',
                'description'
            ]
            ]
        ]);
    }




}
