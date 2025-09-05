<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\DeleteCategoryRequest;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return ResponseBuilder::success(CategoryResource::collection($categories));
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return ResponseBuilder::success(new CategoryResource($category));
    }

    public function create(CreateCategoryRequest $request)
    {

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return ResponseBuilder::success(new CategoryResource($category));
    }
    public function update(UpdateCategoryRequest $request){
        $category = Category::where('id',$request->id)->firstOrFail();

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return ResponseBuilder::success(new CategoryResource($category));
    }
    public function delete(DeleteCategoryRequest $request)
    {
        $category = Category::findOrFail($request->id);
        if($category->products()->count() > 0)
        {
            return ResponseBuilder::error(
                [],
                'Bu kategoriyle ilişkili ürünler mevcut. Lütfen tüm ürünleri silin veya başka kategori ile ilişkilendirin.',
                400
            );
        }

        $category->delete();

        return ResponseBuilder::success(null);
    }
}

