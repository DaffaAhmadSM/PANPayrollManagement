<?php

namespace App\Http\Controllers;

use App\Models\TempMcd;
use App\Models\TempPns;
use App\Imports\McdImport;
use App\Imports\PnsImport;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Validator;

class ImportTimeSheetController extends Controller
{

    public function createTempTimesheet(Request $request) {
        $validator = Validator::make(request()->all(), [
            // 'random_string' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'required|string',
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        }

        $temptimesheet = TempTimeSheet::create([
            'random_string' => Str::random(5) . Carbon::now()->timestamp,
            'from_date' => Carbon::parse($request->from_date),
            'to_date' => Carbon::parse($request->to_date),
            'description' => $request->description,
            'filename' => $request->filename,
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Success',
            'data' => $temptimesheet
        ]);
    }

    public function importToTempMcd(Request $request) {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'temptimesheet_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        }
        

        $excel = (new McdImport)->toCollection($request->file('file'));
        // $data = (new HeadingRowImport)->toArray($request->file('file'));
        $collect = collect($excel->first());
        $totals = $collect->pop();
        $headers = $collect->first()->toArray();
        $rows = $collect->except(0)->values()->toArray();

        $flattenedData = [];
        $dateHeaders = array_slice($headers, 7, -1);  // Extract date headers
        foreach ($rows as $row) {
            foreach ($dateHeaders as $index => $date) {
                $value = $row[$index + 7] !== null ? $row[$index + 7] : 0;  // Replace null with 0
                $flattenedData[] = [
                    "temp_time_sheet_id" => $request->temptimesheet_id,
                    "kronos_job_number" => $row[0] ?? "N/A",
                    "parent_id" => $row[1] ?? "N/A",
                    "oracle_job_number" => $row[2] ?? 'N/A',
                    "employee_name" => $row[3] ?? 'N/A',
                    "leg_id" => $row[4] ?? 'N/A',
                    "job_dissipline" => $row[5] ?? 'N/A',
                    "slo_no" => $row[6] ?? 'N/A',
                    "date" => Carbon::createFromFormat('d/m/Y', $date),
                    "value" => $value
                ];
            }
        }
        $chunk = array_chunk($flattenedData, 1000);
        foreach ($chunk as $data) {
           TempMcd::insert($data);
        }

        return response()->json(['message' => 'Data imported successfully.'], 200);
    }

    public function importToTempPns(Request $request) {

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'temptimesheet_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        }
        

        $excel = (new PnsImport)->toCollection($request->file('file'));
        // $data = (new HeadingRowImport)->toArray($request->file('file'));
        $collect = collect($excel->first());
        $totals = $collect->pop();
        $headers = $collect->first()->toArray();
        $rows = $collect->except(0)->values()->toArray();

        $flattenedData = [];
        $dateHeaders = array_slice($headers, 7, -1);  // Extract date headers
        foreach ($rows as $row) {
            foreach ($dateHeaders as $index => $date) {
                $value = $row[$index + 7] !== null ? $row[$index + 7] : 0;  // Replace null with 0
                $flattenedData[] = [
                    "temp_time_sheet_id" => $request->temptimesheet_id,
                    "kronos_job_number" => $row[0] ?? "N/A",
                    "parent_id" => $row[1] ?? "N/A",
                    "oracle_job_number" => $row[2] ?? 'N/A',
                    "employee_name" => $row[3] ?? 'N/A',
                    "leg_id" => $row[4] ?? 'N/A',
                    "job_dissipline" => $row[5] ?? 'N/A',
                    "slo_no" => $row[6] ?? 'N/A',
                    "date" => Carbon::createFromFormat('d/m/Y', $date),
                    "value" => $value
                ];
            }
        }
        $chunk = array_chunk($flattenedData, 1000);
        foreach ($chunk as $data) {
           TempPns::insert($data);
        }

        return response()->json(['message' => 'Data imported successfully.'], 200);
    }


    public function comparePnsMcd(Request $request) {
        $validator = Validator::make($request->all(), [
            "random_string" => "required|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ]);
        }

        $temptimesheet = TempTimeSheet::where('random_string', $request->random_string)->first();
        
        if (!$temptimesheet) {
            return response()->json([
                'status' => 400,
                'message' => 'Data not found'
            ]);
        }

        $pns = TempPns::where('temp_time_sheet_id', $temptimesheet->id)->get();
        $mcd = TempMcd::where('temp_time_sheet_id', $temptimesheet->id)->get();

        $sumPNS = $pns->groupBy(function($item) {
            return $item['employee_name'] . '_' . $item['date'];
        })->map(function($items) {
            return [
                'ids' => $items->pluck('id'),
                'value' => $items->sum('value')
            ];;
        });

        $sumMCD = $mcd->groupBy(function($item) {
            return $item['employee_name'] . '_' . $item['date'];
        })->map(function($items) {
            return [
                'ids' => $items->pluck('id'),
                'value' => $items->sum('value')
            ];;
        });

        $differeces = [];

        foreach ($sumPNS as $key => $item1) {
            if($sumMCD->has($key)) {
               $item2 = $sumMCD[$key];
               if ($item1['value'] != $item2['value']) {
                    $differeces[] = [
                        'pns' => $item1,
                        'mcd' => $item2
                    ];
                }
            }
        }

        return response()->json([
            'status' => 200,
            'data' => $differeces
        ]);
    }

}
