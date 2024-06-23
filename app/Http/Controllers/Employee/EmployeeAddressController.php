<?php

namespace App\Http\Controllers\Employee;

use Illuminate\Http\Request;
use App\Models\EmployeeAddress;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class EmployeeAddressController extends Controller
{

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'address' => 'required|string',
            'type' => 'required|in:ID,Domicile',
            'rt' => 'required|string',
            'rw' => 'required|string',
            'kelurahan' => 'required|string',
            'kab/kota' => 'required|string',
            'provinsi' => 'required|string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $employeeAddress = EmployeeAddress::create($request->only(
            'employee_id',
            'address',
            'type',
            'rt',
            'rw',
            'kelurahan',
            'kecamatan',
            'kab/kota',
            'provinsi'
        ));

        return response()->json([
            'message' => 'Success',
            'data' => $employeeAddress
        ], 201);
        
    }

    public function detail(string $id)
    {
        $data = EmployeeAddress::with('employee')->find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Address not found'
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
            'address' => 'string',
            'type' => 'in:ID,Domicile',
            'rt' => 'string',
            'rw' => 'string',
            'kelurahan' => 'string',
            'kab/kota' => 'string',
            'provinsi' => 'string',
        ]);

        if($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $employeeAddress = EmployeeAddress::find($id);

        if(!$employeeAddress) {
            return response()->json([
                'message' => 'Employee Address not found'
            ], 404);
        }

        $employeeAddress->update($request->only(
            'employee_id',
            'address',
            'type',
            'rt',
            'rw',
            'kelurahan',
            'kecamatan',
            'kab/kota',
            'provinsi'
        ));
    }

    public function delete(string $id)
    {
        $data = EmployeeAddress::find($id);

        if(!$data) {
            return response()->json([
                'message' => 'Employee Address not found'
            ], 404);
        }

        $data->delete();

        return response()->json([
            'message' => 'Employee Address deleted successfully'
        ], 200);
    }

    public function search(Request $request)
    {
        
    }
}
