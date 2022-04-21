<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
            'reference' => 'required|unique:invoices',
            'dueDate' => 'required|date',
            'subTotal' => 'required|numeric',
            'tax' => 'required|numeric',
            'discount' => 'required|integer',
            'total' => 'required|numeric',
            'customer_id' => 'required|exists:customers,id',
        ];

        if (in_array($this->method(), ['PUT'])) {
            $invoice = Invoice::find($this->route()->parameter('invoice'));
            $rules['reference'] = [
                'required',
                Rule::unique('invoices')->ignore($invoice),
            ];
        }
        
        return $rules;
    }
}
