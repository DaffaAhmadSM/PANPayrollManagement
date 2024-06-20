<?php

namespace App\Http\Controllers\Competency;

use App\Models\JobSkill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JobSkillController extends Controller
{
    public function list(Request $request)
    {
       $page = $request->perpage ?? 70;
        $list = JobSkill::cursorPaginate($page, ['id', 'skill', 'type', 'description']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Skill","Type", "Description"],
        ], 200);
    }

    public function detail(string $id)
    {
       $data = JobSkill::find($id);

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
            'skill' => 'required|string',
            'type' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        JobSkill::create($request->all(['skill', 'type', 'description']));

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'skill' => 'string',
            'type' => 'string',
            'description' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = JobSkill::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->update($request->all(['skill', 'type', 'description']));

        return response()->json([
            'message' => 'Success'
        ], 200);

    }

    public function delete(string $id)
    {
        $data = JobSkill::find($id);

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
