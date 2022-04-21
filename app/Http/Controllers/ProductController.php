<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

/**
 * @group Products
 */
class ProductController extends BaseController
{
    /**
     * List products
     *
     * Get a paginated list of products.
     *
     * @authenticated
     */
    public function index()
    {
        $products = Product::paginate(10);
        return response()->json([
            'success' => true,
            'current_page' => $products->currentPage(),
            'data' => ProductResource::collection($products->items())
        ]);
    }

    /**
     * Store product
     *
     * Store a newly created product in storage.
     *
     * @bodyParam code int required
     * @bodyParam name string required
     * @bodyParam price int required
     * @bodyParam brand string required
     * @bodyParam unit string required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        return $this->handleResponse(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    /**
     * Show product
     *
     * Display the specified product.
     *
     * @pathParam product integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->handleError('Product not found');
        }
        return $this->handleResponse(new ProductResource($product));
    }

    /**
     * Update product
     *
     * Update the specified product in storage.
     *
     * @pathParam product integer required
     *
     * @bodyParam code int required
     * @bodyParam name string required
     * @bodyParam price int required
     * @bodyParam brand string required
     * @bodyParam unit string required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->handleError('Product not found');
        }
        $input = $request->all();
        $product->fill($input)->save();
        return $this->handleResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove product
     *
     * Remove the specified product from storage.
     *
     * @pathParam product integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->handleError('Product not found');
        }
        $product->delete();
        return $this->handleResponse([], 'Product deleted successfully');
    }
}
