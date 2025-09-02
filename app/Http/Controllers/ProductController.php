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
                'page' => $products->currentPAge(),
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
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'stock' => 'required|integer',
            'sku' => 'required|string',
            'brand' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'required|string',
        ]);
        return ResponseBuilder::success($product);
    }

    public function update(UpdateProductRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $product->update([
            'name' => 'required|string|min:3',
            'slug' => 'required|string|unique:products,slug',
            'description' => 'required|string|min:20',
            'price' => 'required|numeric',
            'currency' => 'required|string',
            'stock' => 'required|integer',
            'sku' => 'required|string',
            'brand' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'attributes' => 'nullable|array',
            'images' => 'nullable|array',
            'status' => 'required|string',
        ]);
        return ResponseBuilder::success($product);
    }

    public function delete(DeleteProductRequest $request)
    {
        $product = Product::findOrFail($request->id);
        $product->delete();
        return ResponseBuilder::success($product);
    }
}
