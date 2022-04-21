<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * @group Invoices
 */
class InvoiceController extends BaseController
{
    /**
     * List invoices
     *
     * Get a paginated list of invoices.
     *
     * queryParam page int
     * 
     * @authenticated
     */
    public function index()
    {
        $invoices = Invoice::paginate(10);
        return response()->json([
            'success' => true,
            'current_page' => $invoices->currentPage(),
            'data' => InvoiceResource::collection($invoices->items())
        ]);
    }

    /**
     * Store invoice
     *
     * Store a newly created invoice in storage.
     *
     * @bodyParam reference int required
     * @bodyParam dueDate date required
     * @bodyParam subTotal int required
     * @bodyParam tax int required
     * @bodyParam discount int required
     * @bodyParam total int required
     * @bodyParam customer_id int required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\InvoiceRequest  $request
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
     * Show invoice
     *
     * Display the specified invoice.
     *
     * @pathParam invoice integer required
     *
     * @authenticated
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
     * Update invoice
     *
     * Update the specified invoice in storage.
     *
     * @pathParam invoice integer required
     *
     * @bodyParam reference int required
     * @bodyParam dueDate date required
     * @bodyParam subTotal int required
     * @bodyParam tax int required
     * @bodyParam discount int required
     * @bodyParam total int required
     * @bodyParam customer_id int required
     *
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InvoiceRequest $request, $id)
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
     * Remove product
     *
     * Remove the specified invoice from storage.
     *
     * @pathParam invoice integer required
     *
     * @authenticated
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
     * Add invoice products
     *
     * Store invoice products in storage.
     *
     * @bodyParam products [int] required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\InvoiceRequest  $request
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
     * Remove invoice products
     *
     * Remove invoice products in storage.
     *
     * @bodyParam products array required
     *
     * @authenticated
     *
     * @param  App\Http\Requests\InvoiceRequest  $request
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
