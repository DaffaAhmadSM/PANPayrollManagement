<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\EmploymentType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmploymentTypeController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $data = EmploymentType::cursorPaginate($page, ['id', 'code', 'description', 'permanent']);

        return response()->json([
            'message' => 'Success',
            'data' => $data,
            'header' => ['Code', 'Description', 'Permanent']
        ], 200);
    }

    public function getAll()
    {
        $data = EmploymentType::get(['id', 'code']);

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'permanent' => 'required|in:yes,no',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EmploymentType::create($request->only('code', 'description', 'permanent'));

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 201);
    }

    public function detail(string $id)
    {
        $data = EmploymentType::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Type not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'string|max:255',
            'description' => 'string|max:255',
            'permanent' => 'in:yes,no',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EmploymentType::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Type not found'
            ], 404);
        }

        $data->update($request->only('code', 'description', 'permanent'));

        return response()->json([
            'message' => 'Employee Type updated successfully',
        ], 200);
    }

    public function delete(string $id)
    {
        $data = EmploymentType::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Type not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Employee Type deleted successfully'
        ], 200);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'required|string'
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EmploymentType::where('code', 'like', '%'.$request->search.'%')
            ->orWhere('description', 'like', '%'.$request->search.'%')
            ->cursorPaginate(70, ['id', 'code', 'description', 'permanent']);

        return response()->json([
            'message' => 'Success',
            'data' => $data,
            'header' => ['Code', 'Description', 'Permanent']
        ], 200);
    }
}
