<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Requests\DeleteCategoryRequest;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return ResponseBuilder::success($categories);
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return ResponseBuilder::success($category);
    }

    public function create(CreateCategoryRequest $request)
    {

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return ResponseBuilder::success($category);
    }
    public function update(UpdateCategoryRequest $request){
        $category = Category::where('id',$request->id)->firstOrFail();

        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return ResponseBuilder::success($category);
    }
    public function delete(DeleteCategoryRequest $request){
        $category = Category::where('id',$request->id)->firstOrFail();

        $category->delete();

        return ResponseBuilder::success(null);
    }
}

