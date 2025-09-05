<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\DeleteProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(){
        $products = Product::paginate(10);
        return ResponseBuilder::success([
            'products' => ProductResource::collection($products), 
            'pagination' => [
                'page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function byCategory($categoryId){
        $products = Product::where('category_id', $categoryId)->paginate(10);
        return ResponseBuilder::success([
            'products' => ProductResource::collection($products),
            'pagination' => [
                'page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return ResponseBuilder::success(new ProductResource($product));
    }

    public function create(CreateProductRequest $request)
    {
        $product = Product::create($request->only([
            'name',
            'slug',
            'description',
            'price',
            'currency' ,
            'stock',
            'sku' ,
            'brand',
            'category_id',
            'attributes',
            'images',
            'status',
        ]));
        return ResponseBuilder::success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request)
    {
        $product = Product::findOrFail($request->id);

        $product->update($request->only([
            'name',
            'slug',
            'description',
            'price',
            'currency' ,
            'stock',
            'sku' ,
            'brand',
            'category_id',
            'attributes',
            'images',
            'status',
        ]));

        return ResponseBuilder::success(new ProductResource($product));
    }

    public function delete(DeleteProductRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $product->delete();
        return ResponseBuilder::success(null);
    }
}
