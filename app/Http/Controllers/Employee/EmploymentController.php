<?php

namespace App\Http\Controllers\Employee;

use App\Models\Employee;
use App\Models\Employment;
use Illuminate\Http\Request;
use App\Models\EmploymentType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmploymentController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $data = Employment::with('employee:id,name,no', 'employmentType:id,code')->cursorPaginate($page, ['id', 'employee_id', 'from_date', 'to_date', 'employment_type_id', 'status', 'permanent']);

        return response()->json([
            'message' => 'Success',
            'data' => $data,
            'header' => ['No', 'Employee Name', 'Start Date', 'End Date', 'Type', 'Status', 'Permanent']
        ], 200);

    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'status' => 'required|in:contract,permanent', // replace 'active,inactive' with actual enum values if different
            'employment_type_id' => 'required|exists:employment_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'duration' => 'required|integer|min:0',
            'last_date_worked' => 'required|date|after_or_equal:from_date',
            'terminated' => 'in:yes,no', // replace 'yes,no' with actual enum values if different
            'termination_date' => 'date',
            'termination_reason' => 'string|max:1000'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $employee = Employee::find($request->employee_id);

        $request->merge([
            'name' => $employee->name
        ]);

        $employmentType = EmploymentType::find($request->employment_type_id);

        $request->merge([
            'description' => $employmentType->description,
            'permanent' => $employmentType->permanent
        ]); 

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
            'status' => 'required|in:contract,permanent', // replace 'active,inactive' with actual enum values if different
            'employment_type_id' => 'exists:employment_types,id',
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

        if ($request->has('employee_id')) {
            $employee = Employee::find($request->employee_id);
            $request->merge([
                'name' => $employee->name
            ]);
        }

        if ($request->has('employment_type_id')) {
            $employmentType = EmploymentType::find($request->employment_type_id);
            $request->merge([
                'description' => $employmentType->description,
                'permanent' => $employmentType->permanent
            ]);
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
