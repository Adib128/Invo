<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends BaseController
{
    /**
     * Display a listing of the products.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/products",
     *     tags={"Product"},
     *     description="Display a list products",
     *     operationId="products",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     )
     * )
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
    /**
     * @OA\Post(
     *     path="/products",
     *     tags={"Product"},
     *     description="Store a newly created product in storage.",
     *     operationId="storeProduct",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     oneOf={
     *                     	   @OA\Schema(type="string"),
     *                     	   @OA\Schema(type="integer"),
     *                     }
     *                 ),
     *                 example={"id": "a3fb6", "name": "Jessica Smith", "phone": 12345678}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid ID supplied"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Order not found"
     *     )
     * )
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
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     tags={"Product"},
     *     description="Display the specified product",
     *     operationId="product",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     )
     * )
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
        return $this->handleResponse(
            new ProductResource($product),
            'Product updated successfully'
        );
    }

    /**
     * Remove the specified product from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     tags={"Product"},
     *     description="Remove the specified product from storage.",
     *     operationId="deleteProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation"
     *     )
     * )
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
