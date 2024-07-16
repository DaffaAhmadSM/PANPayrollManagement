<?php

namespace App\Http\Controllers\Competency;

use Illuminate\Http\Request;
use App\Models\CertificateType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CertificateTypeController extends Controller
{
    public function list(Request $request)
    {
       $page = $request->perpage ?? 70;
        $list = CertificateType::with('classification')->cursorPaginate($page, ['id', 'type', 'description', 'certificate_classification_id']);
        return response()->json([
            'data' => $list,
            'message' => 'Success',
            'header' => ["Type", "Description", "Classification"],
        ], 200);
    }

    public function getAll()
    {
        $list = CertificateType::all(['id', 'type']);
        return response()->json([
            'data' => $list,
            'message' => 'Success'
        ], 200);
    }

    public function detail(string $id)
    {
       $list = CertificateType::with('classification')->find($id);

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
            'type' => 'required|string',
            'description' => 'required|string',
            'certificate_classification_id' => 'required|integer|exists:certificate_classifications,id',
            'required_renewal' => 'required|in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        CertificateType::create($request->all());

        return response()->json([
            'message' => 'Success'
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'string',
            'description' => 'string',
            'certificate_classification_id' => 'integer|exists:certificate_classifications,id',
            'required_renewal' => 'in:yes,no',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $list = CertificateType::find($id);

        if (!$list) {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }

        $list->update($request->all(['type', 'description', 'certificate_classification_id']));
        return response()->json([
            'message' => 'Success'
        ], 200);
    }

    public function delete(string $id)
    {
        $data = CertificateType::find($id);

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
