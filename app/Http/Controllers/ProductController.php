<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Http\Requests\ProductPriceRequest;
use App\Http\Requests\ProductSlugRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductAdditionalParams;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ProductCollection(Product::select('products.*')->paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    /*
     *         'min_price'=>'required|numeric|min:0|max:9999999999',
            'max_price'=>'required|numeric|min:1|max:9999999999',
     */

    public function getByFilter(ProductFilterRequest $request){

        $query =    Product::select('products.*')->
                            leftJoin(
                                'product_product_category',
                                'product_product_category.product_id',
                                '=',
                                'products.id')->
                            leftJoin(
                                'product_categories',
                                'product_categories.id',
                                '=',
                                'product_product_category.product_category_id'
                            )->leftJoin(
                                'product_product_additional_params',
                                'products.id',
                                '=',
                                'product_product_additional_params.product_id'
                            )->leftJoin(
                                'product_additional_params',
                                'product_additional_params.id',
                                '=',
                                'product_product_additional_params.product_additional_params_id'
                            )
            ->groupBy('products.id')
        ;

        if(!empty($request->post('add_params'))){
            foreach($request->post('add_params') as $row){
                if(!empty(ProductAdditionalParams::where('name','=',$row['name'])->first())){
                    $query->where('product_additional_params.name','=',$row['name']);
                    $query->where('product_additional_params.value','=',$row['value']);
                }
            }
        }


        if(!empty($request->post('category_id'))){
            $query->where('product_product_category.product_category_id','=',$request->post('category_id'));
        }

        if(!empty($request->post('min_price'))){
            $query->where('products.price','>=',$request->post('min_price'));
        }

        if(!empty($request->post('max_price'))){
            $query->where('products.price','<=',$request->post('max_price'));
        }

        $json_collection = new ProductCollection(
            $query->paginate(10)
        );

        return $json_collection;
    }

    public function getBySlug(ProductSlugRequest $request){

        $query = Product::select('products.*')->
                            join('product_product_category','product_product_category.product_id','=','products.id')->
                            join('product_categories','product_categories.id','=','product_product_category.product_category_id')->
                            where('products.slug','=',$request->post('slug'))->groupBy('products.id')->first();

        $json_collection = new ProductResource(
            $query
        );


        return $json_collection;
    }


}
