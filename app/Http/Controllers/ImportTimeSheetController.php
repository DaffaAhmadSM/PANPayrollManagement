<?php

namespace App\Http\Controllers;

use App\Models\TempMcd;
use App\Models\TempPns;
use App\Imports\McdImport;
use App\Imports\PnsImport;
use App\Models\CalendarHoliday;
use App\Models\Customer;
use App\Models\PnsMcdDiff;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use App\Models\TimeSheet;
use App\Models\WorkingHour;
use App\Models\WorkingHoursDetail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Validator;
use App\Models\OvertimeMultiplicationSetup;
use App\Models\TimeSheetLine;

class ImportTimeSheetController extends Controller
{

    public function createTempTimesheet(Request $request) {
        $validator = Validator::make(request()->all(), [
            // 'random_string' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'description' => 'required|string',
            'filename' => 'required|string',
            'customer_id' => 'required|integer|exists:customers,id',
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
            'user_id' => auth()->user()->id,
            'status' => 'draft',
            'customer_id' => $request->customer_id,
            'customer_file_name' => 'N/A',
            'employee_file_name' => 'N/A',
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
        
        $temptimesheet = TempTimeSheet::find($request->temptimesheet_id);

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
        try {
            $chunk = array_chunk($flattenedData, 1000);
            foreach ($chunk as $data) {
               TempMcd::insert($data);
            }
            $temptimesheet->update([
                'customer_file_name' => $request->file('csv')->getClientOriginalName(),
                'customer_total_imported' => count($flattenedData)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "error inserting data"
            ], 500);
        }

        return response()->json([
        'message' => 'Data imported successfully.',
        "count" => count($flattenedData)
    ], 200);
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
        

        $temptimesheet = TempTimeSheet::find($request->temptimesheet_id);
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
        try {
            $chunk = array_chunk($flattenedData, 1000);
            foreach ($chunk as $data) {
               TempPns::insert($data);
            }
            $temptimesheet->update([
                'employee_file_name' => $request->file('csv')->getClientOriginalName(),
                'employee_total_imported' => count($flattenedData)
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "error inserting data"
            ], 500);
        }

        return response()->json([
            'message' => 'Data imported successfully.',
            "count" => count($flattenedData)
    ], 200);
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
        $list = TempTimeSheet::orderBy('id', 'desc')->with('user')->withCOunt('pnsMcdDiff')->cursorPaginate($page, ['id', 'user_id', 'from_date', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name',  'Creator','Code', 'From Date', 'To Date', 'Description','Status']
        ]);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make(request()->all(), [
            // 'random_string' => 'required|string',
            'from_date' => 'date',
            'to_date' => 'date',
            'description' => 'string',
            'filename' => 'string',
            'customer_id' => 'integer|exists:customers,id',
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
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        $data->delete();

        return response()->json(['message' => 'Data deleted successfully.'], 200);

    }

    public function detailTempTimeSheet($slug) {
        $data = TempTimeSheet::where('random_string', $slug)->with('user')->first();

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

        $mcd_data = TempMcd::where('temp_time_sheet_id', $temp_timesheet_id)->cursorPaginate($page, ['id', 'kronos_job_number', 'oracle_job_number', 'parent_id', 'employee_name', 'leg_id', 'job_dissipline', 'slo_no', 'value', 'date']);

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

    public function resolveConflict($id) {

        $data = PnsMcdDiff::find($id);
        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        $data->delete();

        return response()->json(['message' => 'Data resolved.'], 200);
    }

    public function editConflictValue(Request $request) {
        $validator = Validator::make($request->all(), [
            'pns_mcd_diff_id' => 'required|integer',
            'side' => 'required|in:pns,mcd',
            'id' => 'required|integer',
            'value' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = PnsMcdDiff::find($request->pns_mcd_diff_id);

        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        if ($request->side == 'pns') {
            try {
                DB::beginTransaction();
                TempPNS::where('id', $request->id)->update(['value' => $request->value]);
                $sumPNS = TempPNS::whereIn('id', json_decode($data->pns_ids))->sum('value');
                $data->update([
                    'pns_value' => $sumPNS
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => "server error"
                ], 500);
            }
        }else{
            try {
                DB::beginTransaction();
                TempMCD::where('id', $request->id)->update(['value' => $request->value]);
                $sumMCD = TempMCD::whereIn('id', json_decode($data->mcd_ids))->sum('value');
                $data->update([
                    'mcd_value' => $sumMCD
                ]);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollBack();
                return response()->json([
                    'status' => 500,
                    'message' => "server error"
                ], 500);
            }
        }


        return response()->json(['message' => 'Data updated.'], 200);

    }

    public function getDetailDiff($id) {

        $data = PnsMcdDiff::find($id);
        if (!$data) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        $data->pns_ids = TempPNS::whereIn('id', json_decode($data->pns_ids))->get();
        $data->mcd_ids = TempMcd::whereIn('id', json_decode($data->mcd_ids))->get();

        return response()->json([
            'status' => 200,
            'data' =>  $data
        ]);
    }

    public function searchMCD(Request $request, $temp_timesheet_id) {

        $validator = Validator::make($request->all(), [
            'search' => 'string',
        ]);

        $page = $request->perpage ?? 75;

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = TempMcd::where('temp_time_sheet_id', $temp_timesheet_id)->where('employee_name', 'like', '%'.$request->search.'%')->orWhere('date', 'like', '%'.$request->search.'%')->paginate($page, ['id', 'kronos_job_number', 'oracle_job_number', 'parent_id', 'employee_name', 'leg_id', 'job_dissipline', 'slo_no', 'value', 'date']);

        return response()->json([
            'status' => 200,
            'data' =>  $data,
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

    public function searchPNS(Request $request, $temp_timesheet_id) {

        $validator = Validator::make($request->all(), [
            'search' => 'string',
        ]);

        $page = $request->perpage ?? 75;

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $data = TempPns::where('temp_time_sheet_id', $temp_timesheet_id)->where('employee_name', 'like', '%'.$request->search.'%')->orWhere('date', 'like', '%'.$request->search.'%')->paginate($page, ['id', 'kronos_job_number', 'oracle_job_number', 'parent_id', 'employee_name', 'leg_id', 'job_dissipline', 'slo_no', 'value', 'date']);

        return response()->json([
            'status' => 200,
            'data' =>  $data,
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

    public function moveToTimesheet($temp_timesheet_id){

        $temptimesheet = TempTimeSheet::find($temp_timesheet_id);

        if (!$temptimesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        $mcd = TempMcd::where('temp_time_sheet_id', $temp_timesheet_id)->get();
        $customer = Customer::find($temptimesheet->customer_id);
        $working_hour_detail = WorkingHoursDetail::where('working_hours_id', $customer->working_hour_id)->get();
        $calendar_holiday = CalendarHoliday::whereBetween('date', [$temptimesheet->from_date, $temptimesheet->to_date])->get();
        $overtime_multiplication_all = OvertimeMultiplicationSetup::all();

        try {
        DB::beginTransaction();
        $timesheet = TimeSheet::create([
            'user_id' => $temptimesheet->user_id,
            'filename' => $temptimesheet->filename,
            'description' => $temptimesheet->description,
            'from_date' => $temptimesheet->from_date,
            'to_date' => $temptimesheet->to_date,
            'status' => 'completed',
            'customer_id' => $temptimesheet->customer_id,
            'random_string' => $temptimesheet->random_string
        ]);
        $processedMcd = [];
        foreach ($mcd as $item) {
            $date = Carbon::parse($item->date);
            $day = $date->dayName;
            $working_day = $working_hour_detail->firstWhere("day", $day);
            if(!$working_day) {
                $is_holiday = $calendar_holiday->firstWhere("date", $date->format('Y-m-d'));
                $holiday = $is_holiday ? true : false;
                $working_day['hours'] = 0;
                $deduction_hour = 0;
                $overtime_hour = $item->value;    
            }else{
                $is_holiday = $calendar_holiday->firstWhere("date", $date->format('Y-m-d'));
                $holiday = $is_holiday ? true : false;
                $deduction_hour = $item->value < $working_day->hours ? $working_day->hours - $item->value : 0;
                $overtime_hour = $item->value > $working_day->hours ? $item->value - $working_day->hours : 0;    
            }
           $total_overtime_hours = $overtime_hour;
            
            if ($overtime_hour > 0) {
                if ($holiday) {
                    $overtime_multiplication = $overtime_multiplication_all->where('day_type', 'Holiday')->where('day', $day)->where('to_hours', '<=', $overtime_hour)->all();
                    // return $overtime_multiplication;
                }else{
                    $overtime_multiplication = $overtime_multiplication_all->where('day_type', 'Normal')->where('day', $day)->where('to_hours', '<=', $overtime_hour)->all();
                }
                if ($overtime_multiplication) {
                    foreach ($overtime_multiplication as $multiplication) {
                        $total_overtime_hours = $total_overtime_hours * $multiplication->calculation->multiplier;
                    }
                }else{
                    $total_overtime_hours = $overtime_hour;
                }
            }

            $processedMcd[] = [
                'timesheet_id' => $timesheet->id,
                'no' => $item->leg_id,
                'working_hours_id' => $customer->working_hour_id,
                'date' => $item->date,
                'basic_hours' => $working_day['hours'],
                'actual_hours' => $item->value,
                'deduction_hours' => $deduction_hour,
                'overtime_hours' => $overtime_hour,
                'total_overtime_hours' => $total_overtime_hours,
                'paid_hours' => $working_day['hours'] + $total_overtime_hours,
            ];
        }

        TimeSheetLine::insert($processedMcd);

        $temptimesheet->update(['status' => 'completed']);
        DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => "server error"
            ], 500);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Data moved successfully',
            'data' => $processedMcd
        ]);

    }

}
