<?php

namespace App\Http\Controllers;

use App\Jobs\CalculateDailyRate;
use App\Jobs\OvertimeCalcJob;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\TempMcd;
use App\Models\Customer;
use App\Models\EmployeeRate;
use App\Models\PositionRate;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\DailyRate;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeRateDetail;
use App\Models\WorkingHoursDetail;
use Illuminate\Support\Facades\DB;
use App\Models\tempTimeSheetOvertime;
use Illuminate\Support\Facades\Validator;
use App\Models\OvertimeMultiplicationSetup;

class TempTimesheetLineController extends Controller
{

    public function calculateOvertime(Request $request, $temp_timesheet_str)
    {
        ini_set('max_execution_time', 300);

        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();

        if (!$temptimesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ], 404);
        }

        if (!$request->has('recalculate')) {
            $calculated_history = tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->first();

            if ($calculated_history) {
                return response()->json([
                    'status' => 201,
                    'message' => 'Data already calculated'
                ], 201);
            }
        } else {
            tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->delete();
            tempTimeSheetOvertime::where("random_string", $temp_timesheet_str)->delete();
        }

        $mcd = TempMcd::where('temp_time_sheet_id', $temptimesheet->id)->lazy();
        // return $mcd;
        // $mcd Group by employee name and date
        $mcd = $mcd->groupBy(['leg_id', 'date'])->collapse();
        // return $mcd;
        $customer = Customer::find($temptimesheet->customer_id);
        $working_hour_detail = WorkingHoursDetail::where('working_hours_id', $customer->working_hour_id)->get();
        $calendar_holiday = CalendarHoliday::whereBetween('date', [$temptimesheet->from_date, $temptimesheet->to_date])->get();
        $overtime_multiplication_all = OvertimeMultiplicationSetup::with('calculation')->get();
        // $position_rate = PositionRate::all();

        try {
            DB::beginTransaction();
            $processedMcd = [];
            $timesheet_overtime = [];
            $create_position_rate = [];
            $count = 0;
            foreach ($mcd as $datemcd) {
                $date = Carbon::parse($datemcd->first()->date);
                $day = $date->dayName;
                $working_day = $working_hour_detail->firstWhere("day", $day);
                $working_day_hours = $working_day ? $working_day->hours : 0;
                $is_holiday = $calendar_holiday->firstWhere("date", $date->format('Y-m-d'));
                if ($date->dayOfWeek == 0) {
                    $is_holiday = true;
                }
                $holiday = $is_holiday ? true : false;
                $deduction_hour = 0;
                if (!$working_day || $holiday) {
                    $working_day_hours = 0;
                }

                if ($holiday) {
                    $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Holiday')->where('day', $day)
                        ->all();
                    if (!$overtime_multiplication) {
                        $overtime_multiplication = $overtime_multiplication_all
                            ->where('day_type', 'Holiday')
                            ->where('day', "all")->all();
                    }
                } else {
                    $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Normal')
                        ->where('day', $day)->all();
                    if (!$overtime_multiplication) {
                        $overtime_multiplication = $overtime_multiplication_all
                            ->where('day_type', 'Normal')
                            ->where('day', 'all')->all();
                    }
                }

                $Xremains = collect([]);
                foreach ($overtime_multiplication as $data) {
                    $Xremains->push([
                        'code' => $data->calculation->code,
                        'remaining' => $data->to_hours - $data->from_hours,
                    ]);
                }

                foreach ($datemcd as $item) {
                    $deduction_hour = $item->value < $working_day_hours ? $working_day_hours - $item->value : 0;
                    if ($working_day_hours < 0) {
                        $overtime_hour = $item->value;
                    } else {
                        $overtime_hour = $item->value > $working_day_hours ? $item->value - $working_day_hours : 0;
                    }
                    $total_overtime_hours = 0;
                    // return $total_actual_hour;


                    if ($overtime_hour > 0) {
                        $remaining = $overtime_hour;
                        if ($overtime_multiplication) {
                            foreach ($overtime_multiplication as $multiplication) {
                                $xrem = $Xremains->where('code', $multiplication->calculation->code)->first();

                                // $fromHour = $multiplication->from_hours;
                                // $toHour = $multiplication->to_hours;
                                $to = $xrem['remaining'];
                                $multiplier = $multiplication->calculation->multiplier;

                                if ($remaining <= 0) {
                                    break;
                                }
                                $range = min($to, $remaining);
                                $result = $range * $multiplier;
                                $remaining -= $range;
                                // update xremains
                                $Xremains = $Xremains->map(function ($x) use ($range, $multiplication) {
                                    if ($x['code'] == $multiplication->calculation->code) {
                                        $x['remaining'] -= $range;
                                    }
                                    return $x;
                                });
                                // $Xremains->whereIn('code', $multiplication->calculation->code)->first()['remaining'] -= $range;

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
                        } else {
                            $total_overtime_hours = $overtime_hour;
                        }
                    }
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
                        'basic_hours' => $working_day_hours <= 0 ? 0 : $working_day_hours,
                        'actual_hours' => $item->value,
                        'deduction_hours' => $deduction_hour,
                        'overtime_hours' => $overtime_hour,
                        'total_overtime_hours' => $total_overtime_hours,
                        'paid_hours' => $item->value + $total_overtime_hours - $overtime_hour,
                        'rate' => $item->rate,
                        'custom_id' => $temp_timesheet_str . '-' . $count++
                    ];

                    if ($item->value > 0) {
                        $working_day_hours -= $item->value;
                    }
                }
            }


            $chunk = array_chunk($processedMcd, 1000);
            foreach ($chunk as $key => $value) {
                tempTimesheetLine::insert($value);
            }
            $timehseet_overtime_chunk = array_chunk($timesheet_overtime, 1000);
            foreach ($timehseet_overtime_chunk as $key => $value) {
                tempTimeSheetOvertime::insert($value);
            }

            PositionRate::insert($create_position_rate);

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
        ]);
    }

    public function CalculateOvertimeQueue(Request $request, $temp_timesheet_str)
    {

        $validator = Validator::make($request->all(), [
            'recalculate' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()
            ], 400);
        }

        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();

        if (!$temptimesheet) {
            return response()->json([
                'status' => 404,
                'message' => 'Data not found'
            ], 404);
        }

        OvertimeCalcJob::dispatch($temptimesheet, $request->calculate);

        return response()->json([
            'status' => 200,
            'message' => 'Overtime calculation started'
        ], 200);
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

    public function overtimeDetail($id)
    {
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

    public function overtimeVerify($temp_timesheet_str)
    {
        $temptimesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();
        $temptimesheet->update(['status' => 'verified']);
        return response()->json([
            'status' => 200,
            'message' => 'Data verified',
            'random_string' => $temp_timesheet_str
        ]);
    }

    public function generateExcel(Request $request, $temp_timesheet_id)
    {
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
        $employee_rates = EmployeeRate::where('random_string', $temptimesheet->rate_id)->first();
        $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $employee_rates->id)->get();
        unset($employee_rates);

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
        $output = $data->groupBy(['employee_name', 'Kronos_job_number', 'oracle_job_numbers'])
            ->map(function ($byKronos) use (&$holiday, &$employee_rate_details) {

                $total = [
                    'paid_hours_total' => 0,
                    'actual_hours_total' => 0,
                    'total_overtime_perdate' => [],
                ];
                $data = $byKronos->map(function ($byOracle) use (&$holiday, &$total, &$employee_rate_details) {
                    return $byOracle->map(function ($byEmployee) use (&$holiday, &$total, &$employee_rate_details) {
                        $emp = $byEmployee->first();
                        $emp_rates = $employee_rate_details->where('emp_id', $emp['no'])->first();
                        $result = [
                            'emp' => $emp['no'],
                            'classification' => $emp['job_dissipline'],
                            'Kronos_job_number' => $emp["Kronos_job_number"],
                            'parent_id' => $emp["parent_id"],
                            'employee_name' => $emp["employee_name"],
                            'slo_no' => $emp["slo_no"],
                            'oracle_job_number' => $emp["oracle_job_number"],
                            'rate' => $emp_rates->rate ?? 1,
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
                                'basic_hours' => (double) $employeeData['basic_hours'] - (double) $employeeData['deduction_hours'],
                            ];
                            //sum total overtime hours per date
                            $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                                return $overtime;
                            }) + (double) $employeeData["basic_hours"] - (double) $employeeData["deduction_hours"];
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

    public function calculateDailyRate($temp_timesheet_str)
    {
        $daily_rate_data = DailyRate::where('temptimesheet_string', $temp_timesheet_str)->with("DailyDetails")->get(["id", "temptimesheet_string", "string_id", "employee_name", "kronos_job_number", "oracle_job_number", "parent_id", "leg_id", "rate", "work_hours_total", "invoice_hours_total", "amount_total", "eti_bonus_total", "grand_total"]);
        $temp_timesheet = TempTimeSheet::where('random_string', $temp_timesheet_str)->first();
        $rate = EmployeeRate::where('random_string', $temp_timesheet->rate_id)->first();
        $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $rate->id)->get();

        $daily_rate_data->map(function ($daily_rate) use (&$temp_timesheet, &$employee_rate_details) {
            $rate = $employee_rate_details->where('emp_id', $daily_rate->leg_id)->first();
            $val = $daily_rate->DailyDetails->where('value', '>', 0);
            $sum = $val->sum('value');
            $daily_rate->work_hours_total = $sum;
            $daily_rate->invoice_hours_total = $val->count();
            $daily_rate->amount_total = $daily_rate->invoice_hours_total * $daily_rate->rate ?? $rate->rate;
            $daily_rate->eti_bonus_total = $daily_rate->amount_total * $temp_timesheet->eti_bonus_percentage / 100;
            $daily_rate->grand_total = $daily_rate->amount_total + $daily_rate->eti_bonus_total;

            $daily_rate->save();
        });
        return response()->json([
            'status' => 200,
            'message' => 'Daily rate calculated successfully'
        ]);
    }
}
