<?php

namespace App\Http\Controllers\EmployeeCompetencies;

use Illuminate\Http\Request;
use App\Models\EmployeeCertificate;
use App\Http\Controllers\Controller;
use App\Models\CertificateType;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class EmployeeCertificateController extends Controller
{
    public function list(Request $request){
        $page = $request->page ?? 70;
        $data = EmployeeCertificate::with('employee:id,no', 'certificateType:id,type')->cursorPaginate($page, ['id', 'employee_id', 'certificate_type_id', 'description', 'required_renewal', 'certificate_number', 'issued_date', 'issued_by']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Certificate Type ID',
                'Description',
                'Required Renewal',
                'Certificate Number',
                'Issued Date',
                'Issued By'
            ]
        ]);
    }

    public function getAll(){
        $data = EmployeeCertificate::get(['id', 'employee_id', 'certificate_type_id', 'description', 'required_renewal', 'certificate_number', 'issued_date', 'issued_by']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);

    }

    public function detail($id){
        $data = EmployeeCertificate::with('employee', 'certificateType')->find($id);
        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'certificate_type_id' => 'required|exists:certificate_types,id',
            'certificate_number' => 'required',
            'issued_date' => 'required|date',
            'issued_by' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $certificateType = CertificateType::find($request->certificate_type_id);

        $request->merge([
            'issued_date' => Carbon::parse($request->issued_date)->format('Y-m-d'),
            'description' => $certificateType->type,
            'required_renewal' => $certificateType->required_renewal,
        ]);

        $data = EmployeeCertificate::create($request->all());

        return response()->json([
            'status' => 'success'
        ]);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'employee_id' => 'exists:employees,id',
            'certificate_type_id' => 'exists:certificate_types,id',
            'certificate_number' => 'required',
            'issued_date' => 'date',
            'issued_by' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first(),
            ], 400);
        }

        if ($request->certificate_type_id) {
            $certificateType = CertificateType::find($request->certificate_type_id);
            $request->merge([
                'description' => $certificateType->type,
                'required_renewal' => $certificateType->required_renewal,
            ]);
        }

        if ($request->issued_date) {
            $request->merge([
                'issued_date' => Carbon::parse($request->issued_date)->format('Y-m-d'),
            ]);
        }

        $data = EmployeeCertificate::find($request->id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->update($request->all());

        return response()->json([
            'status' => 'Update Success'
        ]);
    }

    public function delete($id){
        $data = EmployeeCertificate::find($id);

        if (!$data) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data not found',
            ], 404);
        }

        $data->delete();

        return response()->json([
            'status' => 'Delete Success'
        ]);
    }

    public function search(Request $request){
        $data = EmployeeCertificate::with(['employee' => function($query) use ($request){
            $query->where('no', 'like', "%$request->search%");
        }])->with(['certificateType' => function($query) use ($request){
            $query->where('type', 'like', "%$request->search%");
        }])->cursorPaginate(70, ['id', 'employee_id', 'certificate_type_id', 'description', 'required_renewal', 'certificate_number', 'issued_date', 'issued_by']);

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'header' => [
                'Employee ID',
                'Certificate Type ID',
                'Description',
                'Required Renewal',
                'Certificate Number',
                'Issued Date',
                'Issued By'
            ]
        ]);
    }
}
