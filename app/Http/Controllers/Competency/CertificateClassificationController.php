<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CertificateClassification;
use Illuminate\Support\Facades\Validator;

class CertificateClassificationController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CertificateClassification::cursorPaginate($page, ['id', 'classification', 'description']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Classification", "Description"],
        ], 200);
    }

    public function detail(string $id)
    {
       $list = CertificateClassification::find($id);

        if (!$list) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'data' => $list,
            'message' => 'Success'
        ], 200);
    }

    public function create(Request $request)
    {
       $validator = Validator::make($request->all(), [
            'classification' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $list = CertificateClassification::create($request->all());

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'classification' => 'string',
            'description' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $list = CertificateClassification::find($id);

        if (!$list) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $list->update($request->all(['classification', 'description']));

        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function delete(string $id)
    {
        $data = CertificateClassification::find($id);

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
        $search = $request->search;
        $page = $request->perpage ?? 70;
        $list = CertificateClassification::where('classification', 'like', "%$search%")->orWhere('description', 'like', "%$search%")->cursorPaginate($page, ['id', 'classification', 'description']);

        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Classification", "Description"],
        ], 200);
    }
}
