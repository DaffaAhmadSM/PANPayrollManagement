<?php

namespace App\Http\Controllers\Competency;

use App\Models\JobTask;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class JobTaskController extends Controller
{
    public function list(Request $request)
    {
       $page = $request->perpage ?? 70;
        $list = JobTask::with('jobResponsibility:id,responsibility')->cursorPaginate($page, ['id', 'task', 'description', 'job_responsibility_id']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Task","Description"],
        ], 200);
    }

    public function detail(string $id)
    {
       $data = JobTask::with('jobResponsibility')->find($id);

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
            'task' => 'required|string',
            'description' => 'required|string',
            'job_responsibility_id' => 'required|integer|exists:job_responsibilities,id',
            'note' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        JobTask::create($request->all(['task', 'description', 'job_responsibility_id', 'note']));

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'task' => 'string',
            'description' => 'string',
            'job_responsibility_id' => 'integer|exists:job_responsibilities,id',
            'note' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = JobTask::find($id);

        if (!$data) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $data->update($request->all(['task', 'description', 'job_responsibility_id', 'note']));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function delete(string $id)
    {
        $data = JobTask::find($id);

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
