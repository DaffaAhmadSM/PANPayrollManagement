<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CustomerCalendarHoliday;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CustomerCalendarHolidayController extends Controller
{

    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = CustomerCalendarHoliday::with('customer:id,no,name')->cursorPaginate($page, ['id', 'date', 'remarks', 'customer_id']);

        return response()->json([
        'message' => 'Success',
            'data' => $list,
            'header' => ["Date", "Remarks", "Customer No", "Customer Name"],
        ], 200);
    }

    public function detail(string $id)
    {
        $holiday = CustomerCalendarHoliday::with('customer')->find($id);
        if (!$holiday) {
            return response()->json([
                'message' => 'Customer calendar holiday not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $holiday,
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'remarks' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $dateCarbon = Carbon::parse($request->date);
        $request->merge(['date' => $dateCarbon]);

        CustomerCalendarHoliday::create($request->all());

        return response()->json([
            'message' => 'Customer calendar holiday created successfully',
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
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

        if ($request->has('date')) {
            $dateCarbon = Carbon::parse($request->date);
            $request->merge(['date' => $dateCarbon]);
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
