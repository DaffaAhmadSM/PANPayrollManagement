<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployementController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $data = Employment::with('employee:No')->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $data,
            'header' => ['No', 'Employee No', 'Start Date', 'End Date', 'Type', 'Position', 'Department']
        ], 200);

    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive', // replace 'active,inactive' with actual enum values if different
            'employment_type_id' => 'required|exists:employment_types,id',
            'description' => 'required|string|max:1000',
            'permanent' => 'required|in:yes,no', // replace 'yes,no' with actual enum values if different
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'duration' => 'required|integer|min:0',
            'last_date_worked' => 'required|date|after_or_equal:from_date',
            'terminated' => 'required|in:yes,no', // replace 'yes,no' with actual enum values if different
            'termination_date' => 'required|date|after_or_equal:last_date_worked',
            'termination_reason' => 'required|string|max:1000'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = Employment::create($request->only(
            'employee_id',
            'name',
            'status',
            'employment_type_id',
            'description',
            'permanent',
            'from_date',
            'to_date',
            'duration',
            'last_date_worked',
            'terminated',
            'termination_date',
            'termination_reason'
        ));

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 201);
    }

    public function detail(string $id)
    {
        $data = Employment::with('employee')->find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employment not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'name' => 'string|max:255',
            'status' => 'in:active,inactive', // replace 'active,inactive' with actual enum values if different
            'employment_type_id' => 'exists:employment_types,id',
            'description' => 'string|max:1000',
            'permanent' => 'in:yes,no', // replace 'yes,no' with actual enum values if different
            'from_date' => 'date',
            'to_date' => 'date|after_or_equal:from_date',
            'duration' => 'integer|min:0',
            'last_date_worked' => 'date|after_or_equal:from_date',
            'terminated' => 'in:yes,no', // replace 'yes,no' with actual enum values if different
            'termination_date' => 'date|after_or_equal:last_date_worked',
            'termination_reason' => 'string|max:1000'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = Employment::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employment not found'
            ], 404);
        }

        $data->update($request->only(
            'employee_id',
            'name',
            'status',
            'employment_type_id',
            'description',
            'permanent',
            'from_date',
            'to_date',
            'duration',
            'last_date_worked',
            'terminated',
            'termination_date',
            'termination_reason'
        ));

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function delete(string $id)
    {
        $data = Employment::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employment not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Employment deleted successfully'
        ], 200);
    }

    public function search(Request $request)
    {
        
    }
}
