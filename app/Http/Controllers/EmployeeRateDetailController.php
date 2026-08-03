<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRate;
use App\Models\EmployeeRateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeRateDetailController extends Controller
{
    public function list(Request $request, string $employeeRateId)
    {
        $rate = EmployeeRate::find($employeeRateId);

        if (! $rate) {
            return response()->json([
                'message' => 'Employee rate not found',
            ], 404);
        }

        $page = $request->perpage ?? 70;
        $list = EmployeeRateDetail::where('employee_rate_id', $employeeRateId)
            ->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
        ], 200);
    }

    public function detail(string $id)
    {
        $detail = EmployeeRateDetail::with('employeeRate')->find($id);

        if (! $detail) {
            return response()->json([
                'message' => 'Employee rate detail not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $detail,
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_rate_id' => 'required|integer|exists:employee_rates,id',
            'emp_id' => 'required|string|max:255',
            'classification' => 'nullable|string|max:255',
            'rate' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $detail = EmployeeRateDetail::create($request->all());

        return response()->json([
            'message' => 'Employee rate detail created successfully',
            'data' => $detail,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $detail = EmployeeRateDetail::find($id);

        if (! $detail) {
            return response()->json([
                'message' => 'Employee rate detail not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'employee_rate_id' => 'integer|exists:employee_rates,id',
            'emp_id' => 'string|max:255',
            'classification' => 'nullable|string|max:255',
            'rate' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $detail->update($request->only(['employee_rate_id', 'emp_id', 'classification', 'rate']));

        return response()->json([
            'message' => 'Employee rate detail updated successfully',
        ], 200);
    }

    public function delete(string $id)
    {
        $detail = EmployeeRateDetail::find($id);

        if (! $detail) {
            return response()->json([
                'message' => 'Employee rate detail not found',
            ], 404);
        }

        $detail->delete();

        return response()->json([
            'message' => 'Employee rate detail deleted successfully',
        ], 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $page = $request->perpage ?? 70;

        $list = EmployeeRateDetail::with('employeeRate:id,name')
            ->where('emp_id', 'like', "%{$search}%")
            ->orWhere('classification', 'like', "%{$search}%")
            ->orWhereHas('employeeRate', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
        ], 200);
    }
}
