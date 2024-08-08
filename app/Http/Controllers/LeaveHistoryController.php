<?php

namespace App\Http\Controllers;
use App\Models\Employee;
use App\Models\LeaveHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaveHistoryController extends Controller
{

    public function list(Request $request) {
        $page = $request->perpage ?? 70;

        $leaveHistory = LeaveHistory::with('employee:id,name,no')->orderBy('id', 'DESC')->cursorPaginate($page, ['id','employee_id', 'date', 'trans_type', 'deduct', 'paid', 'amount', 'from_date_time', 'to_date_time']);

        return response()->json([
            'status' => 'success',
            'data' => $leaveHistory,
            'header' => [
                "employee.name" => "Name",
                "employee.no" => "Employee No",
                "date" => "Date",
                "trans_type" => "Transaction Type",
                "deduct" => "Deduct",
                "paid" => "Paid",
                "amount" => "Amount",
                "from_date_time" => "From Date",
                "to_date_time" => "To Date",
            ]
        ], 200);
    }

    public function detail($id) {
        $leaveHistory = LeaveHistory::with('employee:id,name,no')->find($id);

        if(!$leaveHistory){
            return response()->json([
                'status' => 'error',
                'message' => 'Leave history not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Leave history detail',
            'data' => $leaveHistory
        ], 200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'trans_type' => 'required|in:Leave,Entitle',
            'amount' => 'numeric',
            'deduct' => 'required|in:yes,no',
            'paid' => 'required|in:yes,no',
            'from_date_time' => 'required|date',
            'to_date_time' => 'required|date',
            'remarks' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }
        $request->merge([
            'date' => Carbon::parse($request->date)->format('Y-m-d'),
            'from_date_time' => Carbon::parse($request->from_date_time),
            'to_date_time' => Carbon::parse($request->to_date_time),
        ]);

        if($request->trans_type == "Leave"){
            $request->merge([
                'amount' => $request->from_date_time->diffInDays($request->to_date_time) + 1,
            ]);
        }

        $request->merge([
            'name' => Employee::find($request->employee_id)->name,
        ]);

        LeaveHistory::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Leave history created',
        ], 200);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'date' => 'date',
            'trans_type' => 'in:Leave,Entitle',
            'amount' => 'numeric',
            'deduct' => 'in:yes,no',
            'paid' => 'in:yes,no',
            'from_date_time' => 'date',
            'to_date_time' => 'date',
            'remarks' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 400);
        }

        $leaveHistory = LeaveHistory::find($id);

        if(!$leaveHistory){
            return response()->json([
                'status' => 'error',
                'message' => 'Leave history not found'
            ], 404);
        }

        if ($request->has('employee_id')) {
            $request->merge([
                'name' => Employee::find($request->employee_id)->name,
            ]);
        }

        if ($request->has('date')) {
            $request->merge([
                'date' => Carbon::parse($request->date)->format('Y-m-d'),
            ]);
        }

        if ($request->has('from_date_time')) {
            $request->merge([
                'from_date_time' => Carbon::parse($request->from_date_time),
            ]);
        }

        if ($request->has('to_date_time')) {
            $request->merge([
                'to_date_time' => Carbon::parse($request->to_date_time),
            ]);
        }

        try {
            DB::beginTransaction();
            $leaveHistory->update($request->all());
            if($request->has('trans_type')){
                if($request->trans_type == 'Leave'){
                    $getLeaveHistory = LeaveHistory::find($id);
                    $request->merge([
                        'amount' => Carbon::parse($getLeaveHistory->from_date_time)->diffInDays($getLeaveHistory->to_date_time) + 1,
                    ]);
                    $leaveHistory->update($request->all());
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Leave history updated'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update leave history'
            ], 500);
        }
    }

    public function delete($id) {
        $leaveHistory = LeaveHistory::find($id);

        if(!$leaveHistory){
            return response()->json([
                'status' => 'error',
                'message' => 'Leave history not found'
            ], 404);
        }

        try {
            $leaveHistory->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Leave history deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete leave history'
            ], 500);
        }
    }
}
