<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Models\JobResponsibility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JobResponsibilityController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
       $list = JobResponsibility::cursorPaginate($page, ['id', 'responsibility', 'description']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Responsibility", "Description"],
        ], 200);
    }

    public function detail(string $id)
    {
       $data = JobResponsibility::find($id);

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
            'responsibility' => 'required|exists:job_responsibilities,id',
            'description' => 'required|string',
            'note' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

         JobResponsibility::create($request->all(['responsibility', 'description', 'note']));

        return response()->json([
            'message' => 'Success'
        ], 201);

    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'responsibility' => 'exists:job_responsibilities,id',
            'description' => 'string',
            'note' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = JobResponsibility::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->update($request->all(['responsibility', 'description', 'note']));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function delete(string $id)
    {
        $data = JobResponsibility::find($id);

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

    public function search()
    {
       
    }
}
