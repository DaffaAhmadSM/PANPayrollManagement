<?php

namespace App\Http\Controllers\EmployeeCompetencies;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeeProjectExperience;
use Illuminate\Support\Facades\Validator;

class EmployeeProjectExperienceController extends Controller
{
    public function list(Request $request){
        $page = $request->page ?? 70;
        $data = EmployeeProjectExperience::with('employee:id,no')->cursorPaginate($page, ['id', 'employee_id', 'project_name', 'role', 'start_date', 'end_date']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Project Name',
                'Role',
                'Start Date',
                'End Date',
            ]
        ]);
    }
    
    public function getAll(){
        $data = EmployeeProjectExperience::get(['id', 'employee_id', 'project_name', 'role', 'start_date', 'end_date']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function detail($id){
        $data = EmployeeProjectExperience::with('employee')->find($id);
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
            'project_name' => 'required|string',
            'role' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        $data = EmployeeProjectExperience::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been created',
            'data' => $data,
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'project_name' => 'string',
            'role' => 'string',
            'start_date' => 'date',
            'end_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        $data = EmployeeProjectExperience::find($request->id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been updated',
            'data' => $data,
        ]);
    }

    public function delete($id){
        $data = EmployeeProjectExperience::find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been deleted',
        ]);
    }

    public function search(Request $request){
        $data = EmployeeProjectExperience::with(['employee'=>function($query) use ($request){
            $query->where('no', 'like', "%$request->no%");
        }])->where('project_name', 'like', "%$request->project_name%")
            ->where('role', 'like', "%$request->role%")
            ->cursorPaginate(['id', 'employee_id', 'project_name', 'role', 'start_date', 'end_date']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Project Name',
                'Role',
                'Start Date',
                'End Date',
            ]
        ]);
    }
}
