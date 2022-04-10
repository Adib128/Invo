<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends BaseController
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(10);
        return $this->handleResponse(ProductResource::collection($products));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->all());
        return $this->handleResponse(new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
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
     * Update the specified resource in product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
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
        return $this->handleResponse(new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->handleError('Product not found');
        }
        $product->delete();
        return $this->handleResponse([],'Product deleted successfully');
    }
}
