<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;

/**
 * @group Customers
 */
class CustomerController extends BaseController
{
    /**
     * List customers
     *
     * Get a paginated list of customers.
     *
     * @authenticated
     */
    public function index()
    {
        $customers = Customer::paginate(10);
        return $this->handleResponse($customers);
    }

    /**
     * Store customer
     *
     * Store a newly created customer in storage.
     *
     * @bodyParam name string required
     * @bodyParam email string required
     * @bodyParam phoneNumber int required
     * @bodyParam address string required
     * @bodyParam city string
     *
     * @authenticated
     *
     * @param  App\Http\Requests\CustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerRequest $request)
    {
        $customer = Customer::create($request->all());
        return $this->handleResponse(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    /**
     * Show customer
     *
     * Display the specified customer.
     *
     * @pathParam customer integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
     * Update customer
     *
     * Update the specified customer in storage.
     *
     * @pathParam customer integer required
     *
     * @bodyParam name string required
     * @bodyParam email string required
     * @bodyParam phoneNumber int required
     * @bodyParam address string required
     * @bodyParam city string
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerRequest $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->handleError('Customer not found');
        }
        $input = $request->all();
        $customer->fill($input)->save();
        return $this->handleResponse(
            new CustomerResource($customer),
            'Customer updated successfully'
        );
    }

    /**
     * Remove customer
     *
     * Remove the specified customer from storage.
     *
     * @pathParam customer integer required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return $this->handleError('Customer not found');
        }
        $customer->delete();
        return $this->handleResponse([], 'Customer deleted successfully');
    }
}
