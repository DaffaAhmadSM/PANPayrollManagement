<?php

namespace App\Http\Controllers;

use App\Imports\EmployeeDepartmentImport;
use App\Models\EmployeeDepartment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeDepartmentController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->perpage ?? 300;
        $list = EmployeeDepartment::orderBy('emp_id')->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ['Employee ID', 'Department'],
        ], 200);
    }

    public function all()
    {
        return response()->json([
            'message' => 'Success',
            'data' => EmployeeDepartment::orderBy('emp_id')->all(['id', 'emp_id', 'department']),
        ], 200);
    }

    public function detail(string $id)
    {
        $department = EmployeeDepartment::find($id);

        if (! $department) {
            return response()->json([
                'message' => 'Employee department not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'data' => $department,
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_id' => 'required|string|max:255',
            'department' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $department = EmployeeDepartment::create($request->only(['emp_id', 'department']));

        return response()->json([
            'message' => 'Employee department created successfully',
            'data' => $department,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $department = EmployeeDepartment::find($id);

        if (! $department) {
            return response()->json([
                'message' => 'Employee department not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'emp_id' => 'string|max:255',
            'department' => 'string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $department->update($request->only(['emp_id', 'department']));

        return response()->json([
            'message' => 'Employee department updated successfully',
        ], 200);
    }

    public function delete(string $id)
    {
        $department = EmployeeDepartment::find($id);

        if (! $department) {
            return response()->json([
                'message' => 'Employee department not found',
            ], 404);
        }

        $department->delete();

        return response()->json([
            'message' => 'Employee department deleted successfully',
        ], 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $page = $request->perpage ?? 70;

        $list = EmployeeDepartment::where('emp_id', 'like', "%{$search}%")
            ->orWhere('department', 'like', "%{$search}%")
            ->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ['Employee ID', 'Department'],
        ], 200);
    }

    public function importFromCsv(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        // store file to storage
        $file = $request->file('file');
        $str = Str::random(10);
        $fileName = $str.time().'_'.$file->getClientOriginalName();
        $data = Storage::disk('local')->putFileAs('department/employee_department', $file, $fileName);

        // import csv data to storage
        Excel::import(new EmployeeDepartmentImport, $data, 'local', \Maatwebsite\Excel\Excel::CSV);

        return response()->json([
            'message' => 'Employee departments imported successfully',
        ], 200);
    }
}
