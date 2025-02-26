<?php

namespace App\Http\Controllers;

use App\Models\CustomerContract;
use App\Models\CustomerInvoice;
use Throwable;
use Illuminate\Bus\Batch;
use App\Jobs\InvoiceQueue;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Jobs\UpdateQueueStatus;
use App\Models\CustomerTimesheet;
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

    public function list(Request $request)
    {
        $page = $request->perpage ?? 75;
        $list = CustomerTimesheet::orderBy('id', 'desc')->with('customer')->cursorPaginate($page, ['id', 'from_date', 'customer_id', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name', 'Customer', 'Code', 'From Date', 'To Date', 'Description', 'Status']
        ]);
    }

    public function detail($customer_timesheet_str)
    {
        $timesheet = CustomerTimesheet::where('random_string', $customer_timesheet_str)->first();
        $page = $request->perpage ?? 70;
        $timesheetLine = CustomerTimesheetLine::where('customer_timesheet_id', $timesheet->id)->with("overtimeCustomerTimesheet", "overtimeCustomerTimesheet.multiplicationSetup")->cursorPaginate($page, ['id', 'date', 'basic_hours', 'actual_hours', 'deduction_hours', 'total_overtime_hours', 'paid_hours', 'custom_id', "Kronos_job_number", 'amount']);
        return response()->json([
            'status' => 200,
            'data' => $timesheetLine,
            'header' => [
                'Date',
                'Basic Hours',
                'Actual Hours',
                'Deduction Hours',
                'Overtime Hours',
                'Total Overtime Hours',
                'Paid Hours'
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
