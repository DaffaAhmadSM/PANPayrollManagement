<?php

namespace App\Http\Controllers\EmployeeCompetencies;

use App\Models\JobSkill;
use Illuminate\Http\Request;
use App\Models\EmployeeSkill;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeSkillController extends Controller
{
    public function list(Request $request){
        $page = $request->page ?? 70;
        $data = EmployeeSkill::with('employee:id,no', 'jobSkill:id,skill')->cursorPaginate($page, ['id', 'employee_id','job_skill_id', 'description', 'type']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Skill ID',
                'Description',
                'Type'
            ]
        ]);
    }

    public function getAll(){
        $data = EmployeeSkill::all(['id', 'employee_id', 'job_skill_id', 'description', 'type']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function detail($id){
        $data = EmployeeSkill::with('employee', 'jobSkill')->find($id);
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
            'job_skill_id' => 'required|exists:job_skills,id',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $jobSkil = JobSkill::find($request->job_skill_id);

        $request->merge([
            'description' => $jobSkil->description,
            'type' => $jobSkil->type,
        ]);

        $data = EmployeeSkill::create($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'job_skill_id' => 'exists:job_skills,id',
            'notes' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }


        if ($request->job_skill_id) {
            $jobSkil = JobSkill::find($request->job_skill_id);
            $request->merge([
                'description' => $jobSkil->description,
                'type' => $jobSkil->type,
            ]);
        }

        $data = EmployeeSkill::find($request->id);
        $data->update($request->all());

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function delete($id){
        $data = EmployeeSkill::find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data deleted',
        ]);
    }

    public function search(Request $request){
        $data = EmployeeSkill::with('employee:id,no', 'jobSkill:id,skill')->whereHas('employee', function($query) use ($request){
            $query->where('no', 'like', '%'.$request->search.'%');
        })->orWhereHas('jobSkill', function($query) use ($request){
            $query->where('skill', 'like', '%'.$request->search.'%');
        })->cursorPaginate(70, ['id', 'employee_id','job_skill_id', 'description', 'type']); 

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Skill ID',
                'Description',
                'Type'
            ]
        ]);
    }
}
