<?php

namespace App\Http\Controllers\EmployeeCompetencies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\EmployeeProfessionalExperience;

class EmployeeProfessionalExperienceController extends Controller
{
    public function list(Request $request){
        $data = EmployeeProfessionalExperience::with('employee:id,no')->cursorPaginate($request->page ?? 70, ['id', 'employee_id', 'employer', 'position']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                "employee.no" => 'Employee ID',
                "employer" => 'Employer',
                "position" => 'Position',
            ]
        ]);
    }

    public function getAll(){
        $data = EmployeeProfessionalExperience::get(['id', 'employee_id', 'employer', 'position']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function detail($id){
        $data = EmployeeProfessionalExperience::with('employee')->find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'employer' => 'required|string',
            'position' => 'required|string',
            'homepage' => 'required|string',
            'phone' => 'required|string',
            'location' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $data = EmployeeProfessionalExperience::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been created',
            'data' => $data,
        ], 201);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'employer' => 'string',
            'position' => 'string',
            'homepage' => 'string',
            'phone' => 'string',
            'location' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $data = EmployeeProfessionalExperience::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been updated'
        ]);
    }

    public function delete($id){
        $data = EmployeeProfessionalExperience::find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been deleted'
        ]);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'search' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $data = EmployeeProfessionalExperience::whereHas('employee', function($query) use ($request){
            $query->where('no', 'like', "%$request->search%");
        })->orWhere('employer', 'like', "%$request->search%")
            ->orWhere('position', 'like', "%$request->search%")
            ->with('employee:id,no')
            ->cursorPaginate($request->page ?? 70, ['id', 'employee_id', 'employer', 'position']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                "employee.no" => 'Employee ID',
                "employer" => 'Employer',
                "position" => 'Position',
            ]
        ]);
    }
}
