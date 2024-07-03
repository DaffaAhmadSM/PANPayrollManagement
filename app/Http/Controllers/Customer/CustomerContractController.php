<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Models\NumberSequence;
use App\Models\CustomerContract;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerContractController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CustomerContract::with('customer:id,no,name')->orderBy('id', 'desc')->cursorPaginate($page, ['id', 'code', 'contract_no', 'description', 'customer_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ["Code", "Contract No", "Description","Customer No", "Customer Name"],
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'required|integer|exists:number_sequences,id',
            'code' => 'required|string',
            'contract_no' => 'required|string',
            'description' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        DB::beginTransaction();
        try {
            NumberSequence::find($request->number_sequence_id)->increment('current_number');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create customer',
            ], 500);
        }

        CustomerContract::create($request->all());

        return response()->json([
            'message' => 'Customer contract created successfully',
        ], 201);

    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'number_sequence_id' => 'exists:number_sequences,id',
            'code' => 'string',
            'contract_no' => 'string',
            'description' => 'string',
            'customer_id' => 'integer|exists:customers,id',
        ]);

        $contract = CustomerContract::find($id);
        if (!$contract) {
            return response()->json([
                'message' => 'Customer contract not found'
            ], 404);
        }

        if ($request->number_sequence_id && $request->number_sequence_id != $contract->number_sequence_id) {
            DB::beginTransaction();
        try {
            NumberSequence::find($request->number_sequence_id)->increment('current_number');
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create customer',
            ], 500);
        }
        }

        $contract->update($request->all(['code', 'contract_no', 'description', 'customer_id']));

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

    public function detail($id){
        $contract = CustomerContract::with('customer:id,no,name', 'numberSequence:id,code')->find($id);
        if (!$contract) {
            return response()->json([
                'message' => 'Customer contract not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Customer contract found',
            'data' => $contract,
        ], 200);
    }
}
