<?php

namespace App\Http\Controllers\leave;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\LeaveCategory;
use App\Models\NumberSequence;
use App\Http\Controllers\Controller;
use App\Models\LeaveHistory;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class LeaveRequestController extends Controller
{
    public function createDraft(Request $request){
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'required|exists:number_sequences,id',
            'employee_id' => 'required|exists:employees,id',
            'leave_category_id' => 'required|exists:leave_categories,id',
            'date_request' => 'required',
            'from_date_time' => 'required|date|after_or_equal:today',
            'to_date_time' => 'required|date|after_or_equal:from_date_time',
            'adress_during_leave' => 'string',
            'contact_no' => 'string',
            'remark' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
        

        $employee = Employee::findOrFail($request->employee_id);
        $number_sequence = NumberSequence::findOrFail($request->number_sequence_id);
        $leave_category = LeaveCategory::findOrFail($request->leave_category_id);
        $fromDate = Carbon::parse($request->from_date_time);
        $toDate = Carbon::parse($request->to_date_time);
        $difDays = $fromDate->diffInDays($toDate) + 1;

        $request->merge([
            'no' => $number_sequence->code . $number_sequence->current_number,
            'name' => $employee->name,
            'deduct' => $leave_category->deduct,
            'paid' => $leave_category->paid,
            'amount' => $difDays,
            'posted' => 'no',
            'date_request' => Carbon::parse($request->date_request),
            'from_date_time' => $fromDate,
            'to_date_time' => $toDate,
        ]);

        $leave_request_posted_no = LeaveRequest::where('posted', 'no')->where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->whereMonth('from_date_time', $fromDate->month)->get();
        $totalAmount = $difDays;
        if($leave_request_posted_no->count() > 0){
            // sum all amount of leave_request_posted_no

            $totalAmount += $leave_request_posted_no->sum('amount');
        }

        $leaveHistory = LeaveHistory::where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->whereMonth('from_date_time', $fromDate->month)->where('trans_type', 'Leave')->where('deduct', 'yes')->get();

        if($leaveHistory->count() > 0){
            $totalAmount += $leaveHistory->sum('amount');
        }

        if($totalAmount > $employee->entitle_leaved_per_month){
            return response()->json([
                'status' => 'error',
                'message' => 'Leave request amount already exceed the leave balance'
            ], 400);
        }
        try {
            $number_sequence->increment('current_number');
            LeaveRequest::create($request->all());
    
            return response()->json([
                'status' => 'success',
                'message' => 'Leave request created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'required|exists:number_sequences,id',
            'employee_id' => 'required|exists:employees,id',
            'leave_category_id' => 'required|exists:leave_categories,id',
            'date_request' => 'required|date',
            'from_date_time' => 'required|date|after_or_equal:today',
            'to_date_time' => 'required|date|after_or_equal:from_date_time',
            'adress_during_leave' => 'string',
            'contact_no' => 'string',
            'remark' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
        

        $employee = Employee::findOrFail($request->employee_id);
        $number_sequence = NumberSequence::findOrFail($request->number_sequence_id);
        $leave_category = LeaveCategory::findOrFail($request->leave_category_id);
        $fromDate = Carbon::parse($request->from_date_time);
        $toDate = Carbon::parse($request->to_date_time);
        $difDays = $fromDate->diffInDays($toDate) + 1;

        $request->merge([
            'no' => $number_sequence->code . $number_sequence->current_number,
            'name' => $employee->name,
            'deduct' => $leave_category->deduct,
            'paid' => $leave_category->paid,
            'amount' => $difDays,
            'posted' => 'no',
            'date_request' => Carbon::parse($request->date_request),
            'from_date_time' => $fromDate,
            'to_date_time' => $toDate,
        ]);

        $leave_request_posted_no = LeaveRequest::where('posted', 'no')->where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->get();
        $totalAmount = $difDays;
        if($leave_request_posted_no->count() > 0){
            // sum all amount of leave_request_posted_no

            $totalAmount += $leave_request_posted_no->sum('amount');
        }

        $leaveHistory = LeaveHistory::where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->where('trans_type', 'Leave')->where('deduct', 'yes')->get();
        $entitleHistory = LeaveHistory::where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->where('trans_type', 'Entitle')->get();
        if($leaveHistory->count() > 0){
            $totalAmount += $leaveHistory->sum('amount');
        }

        if($totalAmount > $entitleHistory->sum('amount')){
            return response()->json([
                'status' => 'error',
                'message' => 'Leave request amount already exceed the leave balance'
            ], 400);
        }
        try {
            $number_sequence->increment('current_number');
            LeaveRequest::create($request->all());
    
            return response()->json([
                'status' => 'success',
                'message' => 'Leave request created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function list(Request $request){
        $page = $request->page ?? 70;
        $leave_requests = LeaveRequest::with(['employee', 'leave_category'])->paginate($page, ['no', 'name', 'from_date_time', 'to_date_time', 'amount', 'remark', 'posted']);
        return response()->json([
            'status' => 'success',
            'data' => $leave_requests,
            'headers' => [
                'No',
                'Name',
                'From Date',
                'To Date',
                'Amount',
                'Remark',
                'Posted'
            ]
        ]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'exists:number_sequences,id',
            'employee_id' => 'exists:employees,id',
            'leave_category_id' => 'exists:leave_categories,id',
            'date_request' => 'date',
            'from_date_time' => 'date|after_or_equal:today',
            'to_date_time' => 'date|after_or_equal:from_date_time',
            'adress_during_leave' => 'string',
            'contact_no' => 'string',
            'remark' => 'string',
            'posted' => 'in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 400);
        }
        
        if($request->has('employee_id')){
            $employee = Employee::findOrFail($request->employee_id);
            $request->merge(['name' => $employee->name]);
        }
        if($request->number_sequence_id){
            $number_sequence = NumberSequence::findOrFail($request->number_sequence_id);
            $request->merge(['no' => $number_sequence->code . $number_sequence->current_number]);
        }
        if($request->leave_category_id){
            $leave_category = LeaveCategory::findOrFail($request->leave_category_id);
            $request->merge(['deduct' => $leave_category->deduct, 'paid' => $leave_category->paid]);
        }
        if($request->from_date_time){
            $fromDate = Carbon::parse($request->from_date_time);
            $request->merge(['from_date_time' => $fromDate]);
        }
        if($request->to_date_time){
            $toDate = Carbon::parse($request->to_date_time);
            $request->merge(['to_date_time' => $toDate]);
        }
        if($request->from_date_time || $request->to_date_time){
            $LeaveReq = LeaveRequest::findOrFail($id);
            $leave_from_date = $request->from_date_time ?? $LeaveReq->from_date_time;
            $leave_to_date = $request->to_date_time ?? $LeaveReq->to_date_time;
            $difDays = $leave_from_date->diffInDays($leave_to_date) + 1;


            $leave_request_posted_no = LeaveRequest::where('posted', 'no')->where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->get();
            $totalAmount = $difDays;
            if($leave_request_posted_no->count() > 0){
                // sum all amount of leave_request_posted_no

                $totalAmount += $leave_request_posted_no->sum('amount');
            }

            $leaveHistory = LeaveHistory::where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->where('trans_type', 'Leave')->where('deduct', 'yes')->get();
            $entitleHistory = LeaveHistory::where('employee_id', $request->employee_id)->whereYear('from_date_time', $fromDate->year)->where('trans_type', 'Entitle')->get();
            if($leaveHistory->count() > 0){
                $totalAmount += $leaveHistory->sum('amount');
            }

            if($totalAmount > $entitleHistory->sum('amount')){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Leave request amount already exceed the leave balance'
                ], 400);
            }
        }

        if($request->posted == 'yes'){
            // do logic to copy data to leave history
        }

        try {
            $leave_request = LeaveRequest::findOrFail($id);
            $leave_request->update($request->all());
    
            return response()->json([
                'status' => 'success',
                'message' => 'Leave request updated successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }
}
