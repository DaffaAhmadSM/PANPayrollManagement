<?php

namespace App\Http\Controllers\leave;

use App\Models\Employee;
use App\Models\LeaveHistory;
use Illuminate\Http\Request;
use App\Models\LeaveCategory;
use App\Models\NumberSequence;
use App\Models\LeaveAdjustment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeaveAdjustmentController extends Controller
{
    public function list(Request $request){
        $page = $request->page ?? 70;
        $leaveAdjusments = LeaveAdjustment::with('employee:id,no', 'leaveCategory:id,description')->cursorPaginate($page, ['id', 'no', 'employee_id', 'leave_category_id', 'date', 'beginning_balance', 'ending_balance', 'adjust_balance', 'remark', 'posted']);

        return response()->json([
            'message' => 'Leave categories retrieved successfully',
            'data' => $leaveAdjusments,
            'header' => [   
                'Sequence',
                'Employee ID',
                'Leave Category Desc',
                'Date',
                'Beginning Balance',
                'Ending Balance',
                'Adjust Balance',
                'Remark',
                'Posted'
            ]
        ]);
    }

    public function getAll(){
        $leaveAdjusments = LeaveAdjustment::with('employee:id,no', 'leaveCategory:id,description')->get(['id', 'no', 'employee_id', 'leave_category_id', 'date', 'beginning_balance', 'ending_balance', 'adjust_balance', 'remark', 'posted']);

        return response()->json([
            'message' => 'Leave categories retrieved successfully',
            'data' => $leaveAdjusments
        ]);
    }

    public function detail($id){
        $leaveAdjusment = LeaveAdjustment::with('employee:id,no', 'leaveCategory:id,description')->find($id);

        if (!$leaveAdjusment) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Leave category retrieved successfully',
            'data' => $leaveAdjusment
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'required|exists:number_sequences,id',
            'employee_id' => 'required|exists:employees,id',
            'leave_category_id' => 'required|exists:leave_categories,id',
            'date' => 'required|date',
            'beginning_balance' => 'required|numeric',
            'ending_balance' => 'required|numeric',
            'remark' => 'required|string',
            'posted' => 'required|in:yes,no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

       $employee =  Employee::find($request->employee_id);
       $LeaveCategory = LeaveCategory::find($request->leave_category_id);

       $number_sequence = NumberSequence::find($request->number_sequence_id);

       $request->merge([
            'name' => $employee->name,
            'deduct' => $LeaveCategory->deduct,
            'paid' => $LeaveCategory->paid,
            'adjust_balance' => $request->beginning_balance - $request->ending_balance,
            'no' => $number_sequence->code . $number_sequence->current_number
         ]);

        //  if ($request-> posted == 'yes') {
        //     LeaveHistory::create([
        //         'employee_id' => $request->employee_id,
        //         'leave_category_id' => $request->leave_category_id,
        //         'date' => $request->date,
        //         'amount' => $request->adjust_balance,
        //  }

        try {
            DB::beginTransaction();
            LeaveAdjustment::create($request->all());
            $number_sequence->increment('current_number');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create leave category'
            ], 500);
        }

        return response()->json([
            'message' => 'Leave category created successfully'
        ], 201);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'exists:number_sequences,id',
            'employee_id' => 'exists:employees,id',
            'leave_category_id' => 'exists:leave_categories,id',
            'date' => 'date',
            'beginning_balance' => 'numeric',
            'ending_balance' => 'numeric',
            'remark' => 'string',
            'posted' => 'in:yes,no'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        if ($request->has('employee_id')) {
            $employee = Employee::find($request->employee_id);
            if (!$employee) {
                return response()->json([
                    'message' => 'Employee not found'
                ], 404);
            }

            $request->merge([
                'name' => $employee->name,
            ]);
        }

        if ($request->has('leave_category_id')) {
            $LeaveCategory = LeaveCategory::find($request->leave_category_id);
            if (!$LeaveCategory) {
                return response()->json([
                    'message' => 'Leave category not found'
                ], 404);
            }

            $request->merge([
                'deduct' => $LeaveCategory->deduct,
                'paid' => $LeaveCategory->paid,
            ]);
        }



        $leaveAdjusment = LeaveAdjustment::find($id);

        if (!$leaveAdjusment) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        if ($leaveAdjusment->number_sequence_id != $request->number_sequence_id) {
            $number_sequence = NumberSequence::find($request->number_sequence_id);
            if (!$number_sequence) {
                return response()->json([
                    'message' => 'Number sequence not found'
                ], 404);
            }

            $request->merge([
                'no' => $number_sequence->code . $number_sequence->current_number
            ]);

            $number_sequence->increment('current_number');
        }
        $leaveAdjusment->update($request->all());


        if ($request->beginning_balance || $request->ending_balance) {
            $findListAgain = LeaveAdjustment::find($id);

            $findListAgain->update([
                'adjust_balance' => $findListAgain->beginning_balance - $findListAgain->ending_balance
            ]);
        }

        
        return response()->json([
            'message' => 'Leave category updated successfully'
        ]);
    }

    public function delete($id){
        $leaveAdjusment = LeaveAdjustment::find($id);

        if (!$leaveAdjusment) {
            return response()->json([
                'message' => 'Leave category not found'
            ], 404);
        }

        $leaveAdjusment->delete();

        return response()->json([
            'message' => 'Leave category deleted successfully'
        ]);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $leaveAdjusments = LeaveAdjustment::with('employee:id,no', 'leaveCategory:id,description')->whereHas('employee', function ($query) use ($request) {
            $query->where('no', 'like', '%' . $request->search . '%');
        })->orWhereHas('leaveCategory', function ($query) use ($request) {
            $query->where('description', 'like', '%' . $request->search . '%');
        })->orWhere('no', 'like', '%' . $request->search . '%')
        ->cursorPaginate(70, ['id', 'no', 'employee_id', 'leave_category_id', 'date', 'beginning_balance', 'ending_balance', 'adjust_balance', 'remark', 'posted']);

        return response()->json([
            'message' => 'Leave categories retrieved successfully',
            'data' => $leaveAdjusments
        ]);
    }
}
