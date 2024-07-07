<?php

namespace App\Http\Controllers\EmployeeCompetencies;

use Illuminate\Http\Request;
use App\Models\EmployeeEducation;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeEducationController extends Controller
{
    public function list(Request $request){
        $page = $request->page ?? 70;
        $data = EmployeeEducation::with('employee', 'educationLevel')->paginate($page, ['id, employee_id, education_level_id, institution']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'id',
                'Employee ID',
                'Education Level',
                'Institution',
            ]
        ], 200);
    }

    public function getAll(){
        $data = EmployeeEducation::get(['id, employee_id, education_level_id, institution']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function detail($id){
        $data = EmployeeEducation::with('employee', 'educationLevel')->find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'education_level_id' => 'required|exists:education_levels,id',
            'institution' => 'required|string',
            'grade_point_avg' => 'required|numeric',
            'grade_point_scale' => 'required|numeric',
            'from_year' => 'required|integer',
            'to_year' => 'required|integer',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        $data = EmployeeEducation::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Data has been created',
        ], 201);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'education_level_id' => 'exists:education_levels,id',
            'institution' => 'string',
            'grade_point_avg' => 'numeric',
            'grade_point_scale' => 'numeric',
            'from_year' => 'integer',
            'to_year' => 'integer',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors(),
            ], 400);
        }

        $data = EmployeeEducation::find($request->id);
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
        ], 200);
    }

    public function delete($id){
        $data = EmployeeEducation::find($id);
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
        ], 200);
    }

    public function search(Request $request){
        $page = $request->page ?? 70;
        $data = EmployeeEducation::with(['employee' => function($query) use ($request){
            $query->where('name', 'like', "%$request->search%");
        }])
            -> with(['educationLevel' => function($query) use ($request){
                $query->where('level', 'like', "%$request->search%");
            }])
            ->where('institution', 'like', "%$request->search%")
            ->cursorPaginate($page, ['id, employee_id, education_level_id, institution']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'id',
                'Employee ID',
                'Education Level',
                'Institution',
            ]
        ], 200);
    }
}
