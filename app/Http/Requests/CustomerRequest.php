<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
/**
 * @bodyParam user_id int required The id of the user. Example: 9
 * @bodyParam room_id string The id of the room.
 * @bodyParam forever boolean Whether to ban the user forever. Example: false
 */
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
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:customers',
            'phoneNumber' => 'required|unique:customers',
            'address' => 'required',
        ];

        // Rules for update method
        if (in_array($this->method(), ['PUT'])) {
            $customer = Customer::find($this->route()->parameter('customer'));
            $rules['email'] = [
                'required',
                Rule::unique('customers')->ignore($customer),
            ];
            $rules['phoneNumber'] = [
                'required',
                Rule::unique('customers')->ignore($customer),
            ];
        }

        return $rules;
    }
}
