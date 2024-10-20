<?php

namespace App\Http\Controllers;

use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceLine;
use Illuminate\Http\Request;

class CustomerInvoiceController extends Controller
{
    public function list (Request $request) {
        $page = $request->perpage ?? 75;
        $list = CustomerInvoice::orderBy('id', 'desc')->cursorPaginate($page, ['id', 'customer_id', 'invoice_number', 'document_number', 'from_date', 'to_date', 'po_number', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Customer', 'Invoice Number', 'Document Number', 'From Date', 'To Date', 'PO Number', 'Status']
        ]);
    }

    public function all()
    {

    }

    public function detail($invoice_number)
    {
        $invoice = CustomerInvoice::where('invoice_number', $invoice_number)->first();
        $page = $request->perpage ?? 70;
        $invoiceLine = CustomerInvoiceLine::where('customer_invoice_id', $invoice->id)->orderBy('id', 'asc')->cursorPaginate($page, ['id', 'description', 'type', 'amount', 'item']);
        return response()->json([
            'status' => 200,
            'data' => $invoiceLine,
            'header' => ['Description', 'Type', 'Amount', 'Item']
        ]);
    }

    public function update(Request $request, $id)
    {

    }

    public function delete($id)
    {

    }

    public function create(Request $request)
    {

    }
}
