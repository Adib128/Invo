<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code' => 'required|unique:products',
            'name' => 'required',
            'price' => 'required|numeric',
            'brand' =>  'required',
            'unit' => 'required'
        ];

        if (in_array($this->method(), ['PUT', 'PATCH'])) {
            $product = Product::find($this->route()->parameter('product'));
            $rules['code'] = ['required',Rule::unique('products')->ignore($product)];
        } 

        return $rules;
    }
}
