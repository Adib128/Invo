<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

/**
 * @group Catgories
 */
class CategoryController extends BaseController
{
    /**
     * List categories
     *
     * Get a paginated list of categories.
     *
     * @authenticated
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json([
            'success' => true,
            'current_page' => $categories->currentPage(),
            'data' => CategoryResource::collection($categories->items())
        ]);
    }

    /**
     * Store category
     *
     * Store a newly created category in storage.
     *
     * @bodyParam code int required
     * @bodyParam name string required
     * @bodyParam slug string required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->all());
        return $this->handleResponse(
            new CategoryResource($category),
            'Category created successfully',
            201
        );
    }

    /**
     * Show category
     *
     * Display the specified category with associated products.
     *
     * @pathParam category integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->handleError('Category not found');
        }
        return $this->handleResponse(new CategoryResource($category));
    }

    /**
     * Update category
     *
     * Update the specified customer in storage.
     * 
     * @pathParam category integer required
     *
     * @bodyParam code int required
     * @bodyParam name string required
     * @bodyParam slug string required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\CategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->handleError('Customer not found');
        }
        $input = $request->all();
        $category->fill($input)->save();
        return $this->handleResponse(
            new CategoryResource($category),
            'Category updated successfully'
        );
    }

    /**
     * Remove category
     *
     * Remove the specified category from storage.
     *
     * @pathParam category integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return $this->handleError('Category not found');
        }
        $category->delete();
        return $this->handleResponse([], 'Category deleted successfully');
    }
}
