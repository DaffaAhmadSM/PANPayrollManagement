<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\EmployeeRate;
use Illuminate\Http\Request;
use App\Imports\ImportEmployeeRate;
use App\Models\EmployeeRateDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EmployeeRateController extends Controller
{
    public function importRatesFromExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xlsx,xls,txt',
            'name' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $randomString = Str::random(5) . strtotime(now());
        $create_employee_rates = EmployeeRate::create([
            'random_string' => $randomString,
            'name' => $request->name,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
        ]);

        // store file to storage
        $path = Storage::disk('local')->putFileAs('rates', $request->file('file'), $randomString . '.csv');
        $data = (new ImportEmployeeRate)->toCollection($path, null, \Maatwebsite\Excel\Excel::CSV)->collapse();

        $data->map(function ($item) use ($create_employee_rates) {
            $item['employee_rate_id'] = $create_employee_rates->id;
            return $item;
        });

        EmployeeRateDetail::insert($data->toArray());

        return response()->json([
            'message' => 'Rates imported successfully',
            'data' => $data
        ], 200);
    }

    public function all()
    {
        return response()->json([
            'message' => 'Success',
            'data' => EmployeeRate::all()
        ], 200);
    }

    public function importRatesFromExcelNew(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,xlsx,xls,txt',
            'name' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $randomString = Str::random(5) . strtotime(now());
        $create_employee_rates = EmployeeRate::create([
            'random_string' => $randomString,
            'name' => $request->name,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
        ]);

        // store file to storage
        $path = Storage::disk('local')->putFileAs('rates', $request->file('file'), $randomString . '.csv');
        $data = (new ImportEmployeeRate)->toCollection($path, null, \Maatwebsite\Excel\Excel::CSV)->collapse();

        $data->map(function ($item) use ($create_employee_rates) {
            $item['employee_rate_id'] = $create_employee_rates->id;
            return $item;
        });

        EmployeeRateDetail::insert($data->toArray());

        return response()->json([
            'message' => 'Rates imported successfully',
            'data' => $data
        ], 200);
    }
}
