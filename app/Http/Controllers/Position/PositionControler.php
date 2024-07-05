<?php

namespace App\Http\Controllers\Position;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PositionControler extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'grade_id' => 'required|integer|exists:grades,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        Position::create($request->all());

        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function getAll(){
        $positions = Position::all(['id', 'name']);
        return response()->json([
            'message' => 'Success',
            'data' => $positions,
        ], 200);
    }

    public function list(Request $request){
        $page = $request->page ?? 70;
        $positions = Position::with('grade:id,name')->paginate($page, ['id', 'name', 'position', 'grade_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $positions,
            'header' => [
                'name',
                'position',
                'grade',
            ],
        ], 200);
    }

    public function detail($id){
        $position = Position::with('grade')->find($id);
        if(!$position){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $position,
        ], 200);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'position' => 'string|max:255',
            'grade_id' => 'integer|exists:grades,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $position = Position::find($request->id);
        if(!$position){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        $position->update($request->all());

        return response()->json([
            'message' => 'Update Success',
        ], 200);
    }

    public function delete($id){
        $position = Position::find($id);
        if(!$position){
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        $position->delete();

        return response()->json([
            'message' => 'Delete Success',
        ], 200);
    }
}
