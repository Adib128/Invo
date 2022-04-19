<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends BaseController
{
    /**
     * Display a listing of the invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::paginate(10);
        return $this->handleResponse(InvoiceResource::collection($invoices));
    }

    /**
     * Store a newly created invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InvoiceRequest $request)
    {
        $invoice = Invoice::create($request->all());
        $invoice->products()->attach($request->products);
        return $this->handleResponse(
            new InvoiceResource($invoice),
            'Invoice created successfully',
            201
        );
    }

    /**
     * Display the specified invoice.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $invoice = Invoice::find($id);
         if (!$invoice) {
            return $this->handleError('Invoice not found');
        }
        return $this->handleResponse(new InvoiceResource($invoice));
    }

    /**
     * Update the specified invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->handleError('Invoice not found');
        }
        $invoice->fill($request->all())->save();
        return $this->handleResponse(
            new InvoiceResource($invoice),
            'Invoice updated successfully'
        );
    }

    /**
     * Remove the specified invoice from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invoice = Invoice::find($id);
        if (!$invoice) {
            return $this->handleError('Invoice not found');
        }
        $invoice->delete();
        return $this->handleResponse([], 'Invoice deleted successfully');
    }

    /**
     * add products to invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function addProducts(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        // Select the list of sent and non existing products
        $attachedProducts = $invoice
            ->products()
            ->whereIn('products.id', $request->products)
            ->pluck('products.id')
            ->toArray();
        // Compare the tow arrays to get just the list of non existing products
        $newProducts = array_diff($request->products, $attachedProducts);
        // Attach new and non existing products
        $invoice->products()->attach($newProducts);
        return $this->handleResponse(new InvoiceResource($invoice));
    }

    /**
     * Remove products from invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function removeProducts(Request $request, $id)
    {
        $invoice = Invoice::find($id);
        $products = $request->products;
        $invoice->products()->detach($products);
        return $this->handleResponse(new InvoiceResource($invoice));
    }
}
