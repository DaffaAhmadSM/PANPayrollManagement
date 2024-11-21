<?php

namespace App\Http\Controllers;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\TempMcd;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\WorkingHoursDetail;
use Illuminate\Support\Facades\DB;
use App\Models\tempTimeSheetOvertime;
use Illuminate\Support\Facades\Validator;
use App\Models\OvertimeMultiplicationSetup;

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
            $working_day_hours = $working_day ? $working_day->hours : 0;
            $is_holiday = $calendar_holiday->firstWhere("date", $date->format('Y-m-d'));
            if ($date->dayOfWeek == 0) {
                $is_holiday = true;
            }
            $holiday = $is_holiday ? true : false;
            if(!$working_day || $holiday) {
                $working_day_hours = 0;
                $deduction_hour = 0;
                $overtime_hour = $item->value;
            }else{
                $deduction_hour = $item->value < $working_day_hours ? $working_day_hours - $item->value : 0;
                $overtime_hour = $item->value > $working_day_hours ? $item->value - $working_day_hours : 0;    
            }
           $total_overtime_hours = 0;
            
            if ($overtime_hour > 0) {
                if ($holiday) {
                    $overtime_multiplication = $overtime_multiplication_all
                    ->where('day_type', 'Holiday')->where('day', $day)
                    ->where('from_hours', '<=', $overtime_hour)
                    ->all();
                    if (!$overtime_multiplication) {
                        $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Holiday')
                        ->where('day', "all")
                        ->where('from_hours', '<=', $overtime_hour)->all();
                    }
                }else{
                    $overtime_multiplication = $overtime_multiplication_all
                    ->where('day_type', 'Normal')
                    ->where('day', $day)
                    ->where('from_hours', '<=', $overtime_hour)->all();
                    if (!$overtime_multiplication){
                        $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Normal')
                        ->where('day', 'all')
                        ->where('from_hours', '<=', $overtime_hour)->all();
                    }
                }
                $remaining = $overtime_hour;
                if ($overtime_multiplication) {
                    foreach ($overtime_multiplication as $multiplication) {
                        $fromHour = $multiplication->from_hours;
                        $toHour = $multiplication->to_hours;
                        $to = $toHour - $fromHour;
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
                'employee_name' => $item->employee_name,
                'job_dissipline' => $item->job_dissipline,
                'leg_id' => $item->leg_id,
                'slo_no' => $item->slo_no,
                'date' => $item->date,
                'basic_hours' => $working_day_hours,
                'actual_hours' => $item->value,
                'deduction_hours' => $deduction_hour,
                'overtime_hours' => $overtime_hour,
                'total_overtime_hours' => $total_overtime_hours,
                'paid_hours' => $item->value + $total_overtime_hours,
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

    public function generateExcel(Request $request, $temp_timesheet_id) {
        // add maximum execution time
        set_time_limit(1200);
        $temptimesheet = TempTimeSheet::find($temp_timesheet_id);
        $startDate = Carbon::parse($temptimesheet->from_date);
        $endDate = Carbon::parse($temptimesheet->to_date);
        $tempTimesheetId = $temp_timesheet_id; // Replace 'x' with the actual temp_timesheet_id
        $holiday = CalendarHoliday::whereBetween('date', [$startDate, $endDate])->get();
        $period = new DatePeriod(
            new DateTime($startDate),
            new DateInterval('P1D'),
            (new DateTime($endDate))->modify('+1 day')
        );

        $days = [];
        foreach ($period as $date) {
            $isholiday = false;
            // check if day is sunday
            if ($date->format('w') == 0) {
                $isholiday = true;
            }
            // check if day is holiday
            $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
            if ($holidayCheck) {
                $isholiday = true;
            }


            $days[] = [
                'date' => $date->format('M d'),
                'is_holiday' => $isholiday
            ];
        }
        // return $days;

        // Build the query
        $data = tempTimesheetLine::where('temp_timesheet_id', $tempTimesheetId)
            ->with('overtimeTimesheet')
            ->get(['id', 'no', 'job_dissipline', 'date', 'actual_hours', 'total_overtime_hours', 'paid_hours', 'custom_id', 'basic_hours', 'slo_no', 'oracle_job_number', 'Kronos_job_number', 'parent_id', 'rate', 'employee_name', 'deduction_hours']);
        $output = $data->groupBy(['employee_name', 'oracle_job_number', 'Kronos_job_number'])
        ->map(function ($byKronos) use (&$holiday) {
            $total = [
                'paid_hours_total' => 0,
                'actual_hours_total' => 0,
                'total_overtime_perdate' => [],
            ];

            $data = $byKronos->map(function ($byOracle) use (&$holiday, &$total) {
                return $byOracle->map(function ($byEmployee) use (&$holiday, &$total) {
                        $emp = $byEmployee->first();

                        $result = [
                            'emp' => $emp['no'],
                            'classification' => $emp['job_dissipline'],
                            'Kronos_job_number' => $emp["Kronos_job_number"],
                            'parent_id' => $emp["parent_id"],
                            'employee_name' => $emp["employee_name"],
                            'slo_no' => $emp["slo_no"],
                            'oracle_job_number' => $emp["oracle_job_number"],
                            'rate' => $emp["rate"],
                            'dates' => [],
                            'paid_hours_total' => 0,
                            'actual_hours_total' => 0,
                            'overtime_hours_total' => 0
                        ];
                        $byEmployee->each(function ($employeeData) use (&$result, &$holiday, &$total) {
                            $is_holiday = false;
                            // if day is sunday then is_holiday = true
                            $date = Carbon::parse($employeeData["date"]);
                            $result['paid_hours_total'] += (double) $employeeData["paid_hours"];
                            $result['actual_hours_total'] += (double) $employeeData["actual_hours"];
                            if ($date->dayOfWeek == 0) {
                                $is_holiday = true;
                            }
                            // check if day is holiday
                            $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
                            if ($holidayCheck) {
                                $is_holiday = true;
                            }
                            // filter $employeeData->OvertimeTimesheet and get only hours value
                            $employeeData->overtime_timesheet = $employeeData->OvertimeTimesheet->map(function ($overtime) use (&$result) {
                                $result['overtime_hours_total'] += (double) $overtime->total_hours;
                                return $overtime->hours;
                                
                            });

                            // $result['total_overtime_hours_total'] += (double) $employeeData["total_overtime_hours"];
                            $date = $date->format('m-d-Y');
                                $result['dates'][$date] = [
                                    'overtime_timesheet' => $employeeData->overtime_timesheet,
                                    'is_holiday' => $is_holiday,
                                    'basic_hours' => (double)$employeeData['basic_hours']- (double)$employeeData['deduction_hours'],
                                ];
                                //sum total overtime hours per date
                                $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                                    return $overtime;
                                }) + (double)$employeeData["basic_hours"];
                                if (isset($total['total_overtime_perdate'][$date])) {
                                    $total['total_overtime_perdate'][$date] += $sum;
                                } else {
                                    $total['total_overtime_perdate'][$date] = $sum;
                                }

                            });
                        $total['paid_hours_total'] += (double) $result['paid_hours_total'];
                        $total['actual_hours_total'] += (double) $result['actual_hours_total'];

                        return $result;
                    })->collapse();
                });
            return [
                "data" => $data,
                // total overtime hours from data
                "total_overtime_hours" => $total['total_overtime_perdate'],
                "paid_hours_total" => $total['paid_hours_total'],
                "actual_hours_total" => $total['actual_hours_total'],
            ];
        })->sortKeys();

        // return view('excel.timesheet-export', compact('output', 'days'));

        return response()->json([
            'status' => 200,
            'data' => $output,
        ]);
    }
}
