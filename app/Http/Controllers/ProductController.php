<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\DeleteProductRequest;

class ProductController extends Controller
{
    public function index(){
        $products = Product::paginate(10);
        return ResponseBuilder::success([
            'products' => $products->items(), 
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
            'products' => $products->items(), 
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
        return ResponseBuilder::success($product);
    }

    public function create(CreateProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'attributes' => $request->attributes,
            'images' => $request->images,
            'status' => $request->status,
        ]);
        return ResponseBuilder::success($product);
    }

    public function update(UpdateProductRequest $request)
    {
        $product = Product::findOrFail($request->id);

        $product->update([
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'stock' => $request->stock,
            'sku' => $request->sku,
            'brand' => $request->brand,
            'category_id' => $request->category_id,
            'attributes' => $request->attributes,
            'images' => $request->images,
            'status' => $request->status,
        ]);
        return ResponseBuilder::success($product);
    }

    public function delete(DeleteProductRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $product->delete();
        return ResponseBuilder::success(null);
    }
}
