<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
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
        // Rules for create method
        $rules = [
            'code' => 'required|integer|unique:categories',
            'name' => 'required',
            'slug' => 'required',
        ];

        // Rules for update method
        if (in_array($this->method(), ['PUT'])) {
            $category = Category::find($this->route()->parameter('category'));
            $rules['code'] = [
                'required',
                Rule::unique('categories')->ignore($category),
            ];
        }

        return $rules;
    }
}
