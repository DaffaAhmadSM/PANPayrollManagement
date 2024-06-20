<?php

namespace App\Http\Controllers\Customer;

use App\Models\CustomerRate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerRateController extends Controller
{
    
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CustomerRate::with('customer:id,name')->cursorPaginate($page, ['id', 'type', 'rate', 'customer_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ["Type", "Rate"],
        ], 200);
    }

    public function detail(string $id)
    {
        $rate = CustomerRate::with('customer:id,name')->with('position:id,position,name')->with('customerContract:id,code')->find($id);
        if (!$rate) {
            return response()->json([
                'message' => 'Customer rate not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $rate,
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|enum:hourly,daily,monthly,yearly',
            'rate' => 'required|numeric',
            'customer_id' => 'required|integer|exists:customers,id',
            'position_id' => 'required|integer|exists:positions,id',
            'customer_contract_id' => 'required|integer|exists:customer_contracts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        CustomerRate::create($request->all());

        return response()->json([
            'message' => 'Customer rate created successfully',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'enum:hourly,daily,monthly,yearly',
            'rate' => 'numeric',
            'customer_id' => 'integer|exists:customers,id',
            'position_id' => 'integer|exists:positions,id',
            'customer_contract_id' => 'integer|exists:customer_contracts,id',
        ]);

        $rate = CustomerRate::find($id);
        if (!$rate) {
            return response()->json([
                'message' => 'Customer rate not found'
            ], 404);
        }

        $rate->update($request->all(['type', 'rate', 'customer_id', 'position_id', 'customer_contract_id']));

        return response()->json([
            'message' => 'Customer rate updated successfully',
        ], 200);
    }

    public function delete(string $id)
    {
        $rate = CustomerRate::find($id);
        if (!$rate) {
            return response()->json([
                'message' => 'Customer rate not found'
            ], 404);
        }

        $rate->delete();

        return response()->json([
            'message' => 'Customer rate deleted successfully',
        ], 200);
    }

    public function search(Request $request){
        $search = $request->search;
        $page = $request->perpage ?? 70;
        $list = CustomerRate::with('customer:id,name')->where('type', 'like', "%$search%")->orWhere('rate', 'like', "%$search%")->cursorPaginate($page, ['id', 'type', 'rate', 'customer_id']);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ["Type", "Rate"],
        ], 200);
    }

}
