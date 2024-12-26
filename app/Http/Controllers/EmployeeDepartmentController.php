<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Imports\EmployeeDepartmentImport;
use Illuminate\Support\Facades\Validator;

class EmployeeDepartmentController extends Controller
{
    public function importFromCsv(Request $request){
        $validate = Validator($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validate->fails()) {
            return response()->json(['message' => $validate->errors()->first()], 400);
        }

        // store file to storage
        $file = $request->file('file');
        $str = Str::random(10);
        $fileName = $str . time() . '_' . $file->getClientOriginalName();
        $data = Storage::disk('local')->putFileAs('department/employee_department', $file, $fileName);

        // import csv data to storage
        Excel::import(new EmployeeDepartmentImport, $data, 'local', \Maatwebsite\Excel\Excel::CSV);
    }
}
