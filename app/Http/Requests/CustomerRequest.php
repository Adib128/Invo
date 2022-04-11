<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
        $rules =  [
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'phoneNumber' => 'required|unique:customers',
            'address' => 'required'
        ];

        // Rules for update method
        if (in_array($this->method(), ['PUT'])) {
            $customer = Customer::find($this->route()->parameter('customer'));
            $rules['email'] = ['required',Rule::unique('customers')->ignore($customer)];
            $rules['phoneNumber'] = ['required',Rule::unique('customers')->ignore($customer)];
        } 

        return $rules;
    }
}
