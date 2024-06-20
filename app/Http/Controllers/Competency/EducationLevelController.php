<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Models\EducationLevel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EducationLevelController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = EducationLevel::cursorPaginate($page, ['id', 'level', 'description']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Level", "Description"],
        ], 200);
    }

    public function detail(string $id)
    {
       $data = EducationLevel::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'data' => $data,
            'message' => 'Success'
        ], 200);
    }

    public function create(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'level' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

       EducationLevel::create($request->all());

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'level' => 'string',
            'description' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EducationLevel::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->update($request->all(['level', 'description']));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function delete(string $id)
    {
        $data = EducationLevel::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function search(Request $request)
    {
       
    }
}
