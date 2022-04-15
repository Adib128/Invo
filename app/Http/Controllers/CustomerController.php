<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;

class CustomerController extends BaseController
{
    /**
     * Display a paginated list of customers.
     */
    public function index()
    {
        $customers = Customer::paginate(10);
        return $this->handleResponse(CustomerResource::collection($customers));
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->all());
        return $this->handleResponse(new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    /**
     * Display the specified customer.
     */
    public function show($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->handleError('Customer not found');
        }
        return $this->handleResponse(new CustomerResource($customer));
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->handleError('Customer not found');
        }
        $input = $request->all();
        $customer->fill($input)->save();
        return $this->handleResponse(new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    /**
     * Remove the specified customer from storage.
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->handleError('Customer not found');
        }
        $customer->delete();
        return $this->handleResponse([],'Customer deleted successfully');
    }
}
