<?php

namespace App\Http\Controllers;

use App\Models\TempMcd;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\WorkingHoursDetail;
use Illuminate\Support\Facades\DB;
use App\Models\OvertimeMultiplicationSetup;
use App\Models\tempTimesheetLine;
use App\Models\tempTimeSheetOvertime;

class TempTimesheetLineController extends Controller
{
    public function calculateOvertime(Request $request, $temp_timesheet_str)
    {
        set_time_limit(200);
        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();

        if (!$temptimesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ],404);
        }

        if (!$request->has('recalculate')){
            $calculated_history = tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->first();

            if ($calculated_history) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Data already calculated'
                ], 201);
            }
        }else{
            tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->delete();
            tempTimeSheetOvertime::where("random_string", $temp_timesheet_str)->delete();
        }

        $mcd = TempMcd::where('temp_time_sheet_id', $temptimesheet->id)->get();
        $customer = Customer::find($temptimesheet->customer_id);
        $working_hour_detail = WorkingHoursDetail::where('working_hours_id', $customer->working_hour_id)->get();
        $calendar_holiday = CalendarHoliday::whereBetween('date', [$temptimesheet->from_date, $temptimesheet->to_date])->get();
        $overtime_multiplication_all = OvertimeMultiplicationSetup::all();

        try {
        DB::beginTransaction();
        $processedMcd = [];
        $timesheet_overtime = [];
        $count = 0;
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
           $total_overtime_hours = 0;
            
            if ($overtime_hour > 0) {
                if ($holiday) {
                    $overtime_multiplication = $overtime_multiplication_all->where('day_type', 'Holiday')->where('day', $day)->where('to_hours', '<=', $overtime_hour)->where('from_hours', '<=', $overtime_hour)->all();
                    // return $overtime_multiplication;
                }else{
                    $overtime_multiplication = $overtime_multiplication_all->where('day_type', 'Normal')->where('day', $day)->where('to_hours', '<=', $overtime_hour)->where('from_hours', '<=', $overtime_hour)->all();
                }
                $remaining = $overtime_hour;
                if ($overtime_multiplication) {
                    foreach ($overtime_multiplication as $multiplication) {
                        $from = $multiplication->from_hours;
                        $to = $multiplication->to_hours;
                        $multiplier = $multiplication->calculation->multiplier;

                        if ($remaining <= 0){
                            break;
                        }
                        $range = min($to, $remaining);
                        $result = $range * $multiplier;
                        $remaining -= $range;
                        // return $range. ' - ' . $remaining. ' - ' . $result . '-'. $from . ' - ' . $to;
                        $timesheet_overtime[] = [
                            "random_string" => $temp_timesheet_str,
                            "multiplication_id" => $multiplication->id,
                            "multiplication_code" => $multiplication->calculation->code,
                            "hours" => $range,
                            "total_hours" => $result,
                            "custom_id" => $temp_timesheet_str . '-' . $count
                        ];

                        $total_overtime_hours += $result;
                    }
                }else{
                    $total_overtime_hours = $overtime_hour;
                }
            }

            // return $timesheet_overtime;

            $processedMcd[] = [
                'temp_timesheet_id' => $temptimesheet->id,
                'no' => $item->leg_id,
                'working_hours_id' => $customer->working_hour_id,
                'kronos_job_number' => $item->kronos_job_number,
                'parent_id' => $item->parent_id,
                'oracle_job_number' => $item->oracle_job_number,
                'date' => $item->date,
                'basic_hours' => $working_day['hours'],
                'actual_hours' => $item->value,
                'deduction_hours' => $deduction_hour,
                'overtime_hours' => $overtime_hour,
                'total_overtime_hours' => $total_overtime_hours,
                'paid_hours' => $working_day['hours'] + $total_overtime_hours,
                'custom_id' => $temp_timesheet_str . '-' . $count++
            ];
        }

        $chunk = array_chunk($processedMcd, 1000);
        foreach ($chunk as $key => $value) {
            tempTimesheetLine::insert($value);
        }
        $timehseet_overtime_chunk = array_chunk($timesheet_overtime, 1000);
        foreach ($timehseet_overtime_chunk as $key => $value) {
            tempTimeSheetOvertime::insert($value);
        }
        $temptimesheet->update(['status' => 'calculated']);
        DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $th->getMessage()
            ], 500);
        }
        return response()->json([
            'status' => 200,
            'message' => 'Data calculated successfully',
            'data' => $processedMcd,
            'timesheet_overtime' => $timesheet_overtime
        ]);
    }

    public function overtimelist($temp_timesheet_str, Request $request)
    {

        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();
        $page = $request->perpage ?? 70;
        $tempTimesheetLine = tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->with("overtimeTimesheet", "overtimeTimesheet.multiplicationSetup")->orderBy('no', 'asc')->cursorPaginate($page, ['id', 'no', 'date', 'basic_hours', 'actual_hours', 'deduction_hours', 'overtime_hours', 'total_overtime_hours', 'paid_hours', 'custom_id']);
        return response()->json([
            'status' => 200,
            'data' => $tempTimesheetLine,
            'header' => [
                'No_Leg',
                'Date',
                'Basic Hours',
                'Actual Hours',
                'Deduction Hours',
                'Overtime Hours',
                'Total Overtime Hours',
                'Paid Hours'
            ]
        ]);
    }

    public function overtimeDetail($id) {
        $tempTimesheetLine = tempTimesheetLine::with("overtimeTimesheet")->find($id);
        if (!$tempTimesheetLine) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ], 404);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Data detail',
            'data' => $tempTimesheetLine
        ]);
        
    }

    public function overtimeVerify($temp_timesheet_str){
        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();
        $temptimesheet->update(['status' => 'verified']);
        return response()->json([
            'status' => 200,
            'message' => 'Data verified',
            'random_string' => $temp_timesheet_str
        ]);
    }
}
