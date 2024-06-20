<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\CustomerContract;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerContractController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CustomerContract::with('customer:id,no,name')->cursorPaginate($page, ['id', 'code', 'contract_number', 'description', 'customer_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ["Code", "Contract No", "Description"],
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'contract_number' => 'required|string',
            'description' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        CustomerContract::create($request->all());

        return response()->json([
            'message' => 'Customer contract created successfully',
        ], 201);

    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'string',
            'contract_number' => 'string',
            'description' => 'string',
            'customer_id' => 'integer|exists:customers,id',
        ]);

        $contract = CustomerContract::find($id);
        if (!$contract) {
            return response()->json([
                'message' => 'Customer contract not found'
            ], 404);
        }

        $contract->update($request->all(['code', 'contract_number', 'description', 'customer_id']));

        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function delete(string $id)
    {
        $contract = CustomerContract::find($id);
        if (!$contract) {
            return response()->json([
                'message' => 'Customer contract not found'
            ], 404);
        }

        $contract->delete();

        return response()->json([
            'message' => 'Customer contract deleted'
        ], 200);
    }
}
