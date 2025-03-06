<?php

namespace App\Http\Controllers;

use App\Models\InvoiceExportPath;
use Throwable;
use Illuminate\Bus\Batch;
use App\Jobs\InvoiceQueue;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Jobs\UpdateQueueStatus;
use App\Models\CustomerInvoice;
use App\Jobs\PNSInvoiceJobBatch;
use App\Models\CustomerContract;
use App\Models\CustomerTimesheet;
use App\Models\InvoiceTotalAmount;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\CustomerTimesheetLine;

class CustomerTimesheetController extends Controller
{

    public function generateInvoice(Request $request, $string_id)
    {

        // $validator = Validator::make($request->all(), [
        //     "penanda_tangan" => "required|string",
        //     "ttd_image" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        //     "kop_surat_image" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $validator->errors()->first()
        //     ], 400);
        // }

        // $ttdpath = Storage::disk('public')->put('images/ttd/', $request->ttd_image);
        // $koppath = Storage::disk('public')->put('images/kop_surat/', $request->kop_surat_image);




        // set timeout to 360
        ini_set('max_execution_time', 520);
        $tempTimesheet = TempTimeSheet::where('random_string', $string_id)->first();
        $CustomerTimesheet = CustomerTimesheet::where('random_string', $string_id)->first();
        $dateTime = Carbon::now();
        $filename = "INVOICE_" . Carbon::parse($tempTimesheet->from_date)->format("Md") . "-" . Carbon::parse($tempTimesheet->to_date)->format("Md") . "_" . $dateTime->format('YmdHis');
        $invoice_number = "INV/" . Carbon::parse($tempTimesheet->to_date)->format('m/y');
        $customerContract = CustomerContract::where('customer_id', $tempTimesheet->customer_id)->first();

        try {
            CustomerInvoice::create([
                "invoice_number" => $invoice_number,
                "customer_id" => $tempTimesheet->customer_id,
                "document_number" => "INV/",
                "customer_contract_id" => $customerContract->id,
                "from_date" => $tempTimesheet->from_date,
                "to_date" => $tempTimesheet->to_date,
                "po_number" => "",
                "status" => "open",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "internal server error",
            ], 500);
        }


        $batch = Bus::batch(
            [
                new InvoiceQueue($string_id, $filename),
            ]
        )
            ->then(
                function (Batch $batch) use ($CustomerTimesheet) {
                    UpdateQueueStatus::dispatch($CustomerTimesheet, 'exported');
                }

            )
            ->catch(
                function (Batch $batch, Throwable $e) use ($CustomerTimesheet) {
                    UpdateQueueStatus::dispatch($CustomerTimesheet, 'failed');
                }
            )
            ->name('inv_' . $string_id)->dispatch();
        return response()->json([
            "status" => 200,
            "message" => "Invoice export started"
        ]);
    }

    public function generateInvoicePart(Request $request, $string_id)
    {

        // $dataKronos = InvoiceTotalAmount::where('random_string', $string_id)
        //     ->where('parent_id', 'not regexp', '^NK')
        //     ->lazy()->groupBy(['parent_id']);

        // $dataKronos = $dataKronos->map(function ($item) {
        //     return $item->chunk(15);
        // });

        // return $dataKronos;
        // set timeout to 360
        ini_set('max_execution_time', 520);
        $tempTimesheet = TempTimeSheet::where('random_string', $string_id)->first();
        $CustomerTimesheet = CustomerTimesheet::where('random_string', $string_id)->first();
        $dateTime = Carbon::now();
        $filename = "INVOICE_" . Carbon::parse($tempTimesheet->from_date)->format("Md") . "-" . Carbon::parse($tempTimesheet->to_date)->format("Md") . "_" . $dateTime->format('YmdHis');
        $invoice_number = "INV/" . Carbon::parse($tempTimesheet->to_date)->format('m/y');
        $customerContract = CustomerContract::where('customer_id', $tempTimesheet->customer_id)->first();

        try {
            CustomerInvoice::create([
                "invoice_number" => $invoice_number,
                "customer_id" => $tempTimesheet->customer_id,
                "document_number" => "INV/",
                "customer_contract_id" => $customerContract->id,
                "from_date" => $tempTimesheet->from_date,
                "to_date" => $tempTimesheet->to_date,
                "po_number" => "",
                "status" => "open",
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                "message" => "internal server error",
            ], 500);
        }


        $batch = Bus::batch(
            [
                new PNSInvoiceJobBatch($string_id, $filename),
            ]
        )
            ->then(
                function (Batch $batch) use ($CustomerTimesheet) {
                    UpdateQueueStatus::dispatch($CustomerTimesheet, 'exported');
                }

            )
            ->catch(
                function (Batch $batch, Throwable $e) use ($CustomerTimesheet) {
                    UpdateQueueStatus::dispatch($CustomerTimesheet, 'failed');
                }
            )
            ->name('inv_' . $string_id)->dispatch();
        return response()->json([
            "status" => 200,
            "message" => "Invoice export started"
        ]);
    }

    public function list(Request $request)
    {
        $page = $request->perpage ?? 75;
        $list = CustomerTimesheet::orderBy('id', 'desc')->with('customer')->cursorPaginate($page, ['id', 'from_date', 'customer_id', 'to_date', 'description', 'filename', 'random_string', 'status', 'file_path']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name', 'Customer', 'Code', 'From Date', 'To Date', 'Description', 'Status']
        ]);
    }

    public function detail($customer_timesheet_str)
    {
        $page = $request->perpage ?? 70;
        $invoicePath = InvoiceExportPath::where('invoice_string_id', $customer_timesheet_str)->cursorPaginate($page, ['id', 'filename', 'file_path', 'invoice_string_id']);
        return response()->json([
            'status' => 200,
            'data' => $invoicePath,
            'header' => [
                'Name',
            ]
        ]);
    }

    public function search(Request $request)
    {
        $page = $request->perpage ?? 75;
        $search = $request->search;
        $list = CustomerTimesheet::where('description', 'like', "%$search%")
            ->orWhere('random_string', 'like', "%$search%")
            ->orWhere('status', 'like', "%$search%")
            ->orderBy('id', 'desc')
            ->with('customer')
            ->cursorPaginate($page, ['id', 'from_date', 'customer_id', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name', 'Customer', 'Creator', 'Code', 'From Date', 'To Date', 'Description', 'Status']
        ]);
    }
}
