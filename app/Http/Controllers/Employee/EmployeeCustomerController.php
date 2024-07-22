<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\EmployeeCustomerRelation;
use Illuminate\Support\Facades\Validator;

class EmployeeCustomerController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $data = EmployeeCustomerRelation::with('employee:id,name,no', 'customer:id,no,name')->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $data,
            'header' => ['No', 'Employee Name', 'Employee ID', 'Customer Name', 'Customer ID']
        ], 200);

    }


    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'customer_id' => 'required|exists:customers,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EmployeeCustomerRelation::create($request->only('employee_id', 'customer_id'));

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 201);
    }

    public function detail(string $id)
    {
        $data = EmployeeCustomerRelation::with('employee', 'customer')->find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Customer Relation not found'
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
            'employee_id' => 'exists:employees,id',
            'customer_id' => 'exists:customers,id',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = EmployeeCustomerRelation::find($id);
        $data->update($request->only('employee_id', 'customer_id'));

        return response()->json([
            'message' => 'Success',
            'data' => $data
        ], 200);
    }

    public function delete(string $id)
    {
        $data = EmployeeCustomerRelation::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Customer Relation not found'
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
