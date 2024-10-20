<?php

namespace App\Http\Controllers;

use App\Models\CustomerContract;
use App\Models\CustomerInvoice;
use App\Models\CustomerInvoiceLine;
use App\Models\CustomerTimesheet;
use App\Models\CustomerTimesheetLine;
use App\Models\GeneralSetup;
use App\Models\NumberSequence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerTimesheetController extends Controller
{

    public function generateInvoice ($id){
        $timesheet = CustomerTimesheet::find($id);
        $timesheetLine = CustomerTimesheetLine::where('customer_timesheet_id', $timesheet->id)->get();
        $sequence = GeneralSetup::first();
        $customerContract = CustomerContract::where('customer_id', $timesheet->customer_id)->first();
        if (!$customerContract) {
            return response()->json([
                'status' => 500,
                'message' => 'Customer contract not found'
            ], 500);
        }
        $generated_number = NumberSequence::generateNumber($sequence->customer_invoice);

        $customerInvoiceLine = [];
        // group timesheetline by kronos_job_number
        $grouped = $timesheetLine->groupBy('Kronos_job_number');

        $customerInvoice = CustomerInvoice::create([
            'invoice_number' => $generated_number,
            'customer_id' => $timesheet->customer_id,
            'document_number' => $timesheet->random_string,
            'customer_contract_id' => $customerContract->id,
            'from_date' => $timesheet->from_date,
            'to_date' => $timesheet->to_date,
            'po_number' => "N/A",
            'status' => 'open'
        ]);

        foreach ($grouped as $key => $value) {
           $customerInvoiceLine[] = [
                'customer_invoice_id' => $customerInvoice->id,
                'description' => $key,
                'type' => 'timesheet',
                'amount' => $value->sum('amount'),
                'item' => "N/A",
            ];
        }

        // chunk the array to avoid memory limit
        $customerInvoiceLine = array_chunk($customerInvoiceLine, 1000);

        try {
            DB::beginTransaction();
            foreach ($customerInvoiceLine as $key => $value) {
                CustomerInvoiceLine::insert($value);
            }
            DB::commit();
        } catch (\Throwable $th) {
            //throw $th;
            $customerInvoice->delete();
            return response()->json([
                'status' => 500,
                'message' => 'Error generating invoice'
            ]);
            DB::rollBack();
        }
        
        $timesheet->status = 'posted';
        $timesheet->save();
        return response()->json([
            'status' => 200,
            'message' => 'Invoice generated successfully',
            'data' => $customerInvoiceLine
        ]);
    }

    public function list (Request $request) {
        $page = $request->perpage ?? 75;
        $list = CustomerTimesheet::orderBy('id', 'desc')->with('customer')->cursorPaginate($page, ['id','from_date','customer_id', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name', 'Customer', 'Code', 'From Date', 'To Date', 'Description','Status']
        ]);
    }

    public function detail($customer_timesheet_str){
        $timesheet = CustomerTimesheet::where('random_string', $customer_timesheet_str)->first();
        $page = $request->perpage ?? 70;
        $timesheetLine = CustomerTimesheetLine::where('customer_timesheet_id', $timesheet->id)->with("overtimeCustomerTimesheet", "overtimeCustomerTimesheet.multiplicationSetup")->cursorPaginate($page, ['id', 'date', 'basic_hours', 'actual_hours', 'deduction_hours','total_overtime_hours', 'paid_hours', 'custom_id', "Kronos_job_number", 'amount']);
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

    public function search(Request $request) {
        $page = $request->perpage ?? 75;
        $search = $request->search;
        $list = CustomerTimesheet::where('description', 'like', "%$search%")
            ->orWhere('random_string', 'like', "%$search%")
            ->orWhere('status', 'like', "%$search%")
            ->orderBy('id', 'desc')
            ->with('customer')
            ->cursorPaginate($page, ['id','from_date','customer_id', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name','Customer', 'Creator', 'Code', 'From Date', 'To Date', 'Description','Status']
        ]);
    }
}
