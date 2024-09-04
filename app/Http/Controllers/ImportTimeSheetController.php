<?php

namespace App\Http\Controllers;

use App\Models\TempMcd;
use App\Models\TempPns;
use App\Imports\McdImport;
use App\Imports\PnsImport;
use App\Models\PnsMcdDiff;
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
            ],400);
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
            'csv' => 'required|mimes:xlsx,xls,csv',
            'temptimesheet_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }
        

        $excel = (new McdImport)->toCollection($request->file('csv'));
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
            'csv' => 'required|mimes:xlsx,xls,csv',
            'temptimesheet_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }
        

        $excel = (new PnsImport)->toCollection($request->file('csv'));
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
            ], 400);
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
                'employee_name' => $items->first()->employee_name,
                'date' => $items->first()->date,
                'ids' => $items->pluck('id'),               
                'value' => $items->sum('value')
            ];;
        });

        $sumMCD = $mcd->groupBy(function($item) {
            return $item['employee_name'] . '_' . $item['date'];
        })->map(function($items) use($temptimesheet) {
            return [
                'temp_time_sheet_id' => $temptimesheet->id,
                'employee_name' => $items->first()->employee_name,
                'date' => $items->first()->date,
                'ids' => $items->pluck('id'),
                'value' => $items->sum('value')
            ];;
        });

        $differeces = [];
        // PnsMcdDiff::create([
        //     'temp_time_sheet_id' => $temptimesheet->id,
        //     'employee_name' => $item1['employee_name'],
        //     'date' => $item1['date'],
        //     'mcd_ids' => $item2['ids'],
        //     'pns_ids' => $item1['ids'],
        //     'mcd_value' => $item2['value'],
        //     'pns_value' => $item1['value']
        // ]);

        foreach ($sumPNS as $key => $item1) {
            if($sumMCD->has($key)) {
               $item2 = $sumMCD[$key];
               if ($item1['value'] != $item2['value']) {
                    $differeces[] = [
                        'temp_time_sheet_id' => $temptimesheet->id,
                        'employee_name' => $item1['employee_name'],
                        'date' => $item1['date'],
                        'mcd_ids' => $item2['ids'],
                        'pns_ids' => $item1['ids'],
                        'mcd_value' => $item2['value'],
                        'pns_value' => $item1['value']
                    ];
                }
            }else{
                $differeces[] = [
                    'temp_time_sheet_id' => $temptimesheet->id,
                    'employee_name' => $item1['employee_name'],
                    'date' => $item1['date'],
                    'mcd_ids' => "[]",
                    'pns_ids' => $item1['ids'],
                    'mcd_value' => 0,
                    'pns_value' => $item1['value']
                ];
            }
        }

        PnsMcdDiff::insert($differeces);

        return response()->json([
            'status' => 200,
            'data' => $differeces
        ]);
    }

    public function list (Request $request) {
        $page = $request->perpage ?? 75;
        $list = TempTimeSheet::orderBy('id', 'desc')->cursorPaginate($page);
        return response()->json([
            'status' => 200,
            'data' => $list
        ]);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make(request()->all(), [
            // 'random_string' => 'required|string',
            'from_date' => 'date',
            'to_date' => 'date',
            'description' => 'string',
            'filename' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ],400);
        }

        $data = TempTimeSheet::find($id);

        if (!$data) {
            return response()->json([
                'status' => 400,
                'message' => 'Data not found'
            ]);
        }

        $data->update($request->all());

        return response()->json(['message' => 'Data updated successfully.'], 200);
    }

    public function delete($id) {
        $data = TempTimeSheet::find($id);

        if (!$data) {
            return response()->json([
                'status' => 400,
                'message' => 'Data not found'
            ]);
        }

        $data->delete();

        return response()->json(['message' => 'Data deleted successfully.'], 200);

    }

    public function detailTempTimeSheet($slug) {
        $data = TempTimeSheet::where('random_string', $slug)->first();

        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        return response()->json([
            'status' => 200,
            'data' => $data
        ]);
    }

    public function listPnsTemp(Request $request, $temp_timesheet_id) {
        $page = $request->perpage ?? 75;

        $pns_data = TempPns::where('temp_time_sheet_id', $temp_timesheet_id)->cursorPaginate($page, ['id', 'kronos_job_number', 'oracle_job_number', 'parent_id', 'employee_name', 'leg_id', 'job_dissipline', 'slo_no', 'value', 'date']);

        return response()->json([
            'status' => 200,
            'data' =>  $pns_data,
            'header' => [
                'Kronos Job Number',
                'Oracle Job Number',
                'Parent ID',
                'Employee Name',
                'Leg ID',
                'Job Dissipline',
                'SLO No',
                'Value',
                'Date'
            ],
        ]);

    }

    public function listMcdTemp($temp_timesheet_id) {

        $page = $request->perpage ?? 75;

        $mcd_data = TempMcd::where('temp_time_sheet_id', $temp_timesheet_id)->cursorPaginate($page, ['kronos_job_number', 'oracle_job_number', 'parent_id', 'employee_name', 'leg_id', 'job_dissipline', 'slo_no', 'value', 'date']);

        return response()->json([
            'status' => 200,
            'data' =>  $mcd_data,
            'header' => [
                'Kronos Job Number',
                'Oracle Job Number',
                'Parent ID',
                'Employee Name',
                'Leg ID',
                'Job Dissipline',
                'SLO No',
                'Value',
                'Date'
            ],
        ]);

    }

    public function diffList ($temp_timesheet_id) {

       

        $diff_data = PnsMcdDiff::where('temp_time_sheet_id', $temp_timesheet_id)->get();
        $diff_data->map(function ($item) {
            if($item->mcd_ids != "[]") {
                $item->mcd_ids = TempMcd::whereIn('id', json_decode($item->mcd_ids))->get();
            }else{
                $item->mcd_ids = [];
            }
            $item->pns_ids = TempPNS::whereIn('id', json_decode($item->pns_ids))->get();
        });
        return response()->json([
            'status' => 200,
            'data' =>  $diff_data
        ]);

    }


}
