<?php

namespace App\Http\Controllers;

use App\Imports\ImportEmployeeRate;
use App\Models\EmployeeRate;
use App\Models\EmployeeRateDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Excel;

class EmployeeRateController extends Controller
{
    public function importRatesFromExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt',
            'name' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $randomString = Str::random(5).strtotime(now());
        $create_employee_rates = EmployeeRate::create([
            'random_string' => $randomString,
            'name' => $request->name,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
        ]);

        // store file to storage
        $path = Storage::disk('local')->putFileAs('rates', $request->file('file'), $randomString.'.csv');
        $data = (new ImportEmployeeRate)->toCollection($path, null, Excel::CSV)->collapse();

        $data->map(function ($item) use ($create_employee_rates) {
            $item['employee_rate_id'] = $create_employee_rates->id;

            return $item;
        });

        EmployeeRateDetail::insert($data->toArray());

        return response()->json([
            'message' => 'Rates imported successfully',
            'data' => $data,
        ], 200);
    }

    public function list(Request $request)
    {
        $page = $request->perpage ?? 70;
        $list = EmployeeRate::cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ['Name', 'From Date', 'To Date'],
        ], 200);
    }

    public function all()
    {
        return response()->json([
            'message' => 'Success',
            'data' => EmployeeRate::all(['id', 'name', 'random_string']),
        ], 200);
    }

    public function detail(string $id)
    {
        $rate = EmployeeRate::with('details')->find($id);

        if (! $rate) {
            return response()->json([
                'message' => 'Employee rate not found',
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
            'name' => 'required|string|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $rate = EmployeeRate::create([
            'random_string' => Str::random(5).strtotime(now()),
            'name' => $request->name,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
        ]);

        return response()->json([
            'message' => 'Employee rate created successfully',
            'data' => $rate,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $rate = EmployeeRate::find($id);

        if (! $rate) {
            return response()->json([
                'message' => 'Employee rate not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'from_date' => 'date',
            'to_date' => 'date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
            ], 400);
        }

        $rate->update($request->only(['name', 'from_date', 'to_date']));

        return response()->json([
            'message' => 'Employee rate updated successfully',
        ], 200);
    }

    public function delete(string $id)
    {
        $rate = EmployeeRate::find($id);

        if (! $rate) {
            return response()->json([
                'message' => 'Employee rate not found',
            ], 404);
        }

        $rate->delete();

        return response()->json([
            'message' => 'Employee rate deleted successfully',
        ], 200);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $page = $request->perpage ?? 70;

        $list = EmployeeRate::where('name', 'like', "%{$search}%")
            ->cursorPaginate($page);

        return response()->json([
            'message' => 'Success',
            'data' => $list,
            'header' => ['Name', 'From Date', 'To Date'],
        ], 200);
    }
}
