<?php

namespace App\Http\Controllers\Position;

use App\Models\Grade;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class GradeControler extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'grade' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        Grade::create($request->all());
        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function getAll(){
        $grades = Grade::all(['id', 'name']);
        return response()->json([
            'message' => 'Success',
            'data' => $grades,
        ], 200);
    }

    public function list(Request $request){
        $page = $request->page ?? 70;
        $grades = Grade::paginate($page,['id', 'name', 'code', 'grade']);

        return response()->json([
            'message' => 'Success',
            'data' => $grades,
            'header' => [
                'name',
                'code',
                'grade',
            ],
        ], 200);
    }

    public function detail($id){
        $grade = Grade::find($id);
        if(!$grade){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $grade,
        ], 200);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'code' => 'string|max:255',
            'grade' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $grade = Grade::find($request->id);
        if(!$grade){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        $grade->update($request->all());
        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function delete($id){
        $grade = Grade::find($id);
        if(!$grade){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        $grade->delete();
        return response()->json([
            'message' => 'Success',
        ], 200);
    }
}
