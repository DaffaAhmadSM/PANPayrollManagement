<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerCalendarHoliday;
use Illuminate\Support\Facades\Validator;

class CustomerCalendarHolidayController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CustomerCalendarHoliday::with('customer')->cursorPaginate($page, ['id', 'code', 'date', 'remarks']);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ["Code", "Date", "Remarks"],
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'date' => 'required|date',
            'remarks' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        CustomerCalendarHoliday::create($request->all());

        return response()->json([
            'message' => 'Customer calendar holiday created successfully',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'string',
            'date' => 'date',
            'remarks' => 'string',
            'customer_id' => 'integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $holiday = CustomerCalendarHoliday::find($id);

        if (!$holiday) {
            return response()->json([
                'message' => 'Customer calendar holiday not found'
            ], 404);
        }

        $holiday->update($request->all(['code', 'date', 'remarks', 'customer_id']));

        return response()->json([
            'message' => 'Success',
        ], 200);
    }

    public function delete(string $id)
    {
        $holiday = CustomerCalendarHoliday::find($id);
        if (!$holiday) {
            return response()->json([
                'message' => 'Customer calendar holiday not found'
            ], 404);
        }

        $holiday->delete();

        return response()->json([
            'message' => 'Customer calendar holiday deleted successfully'
        ], 200);
    }
}
