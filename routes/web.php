<?php

use App\Exports\ExportInvoice;
use Illuminate\Support\Str;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeDepartment;
use App\Models\EmployeeRateDetail;
use App\Models\InvoiceTotalAmount;
use App\Exports\TempTimesheetExport;
use Illuminate\Support\Facades\Route;
use App\Exports\tempTimesheetExportMI;
use App\Models\DailyRate;
use Illuminate\Support\Facades\View;

Route::get('invoice', function () {

    $dataKronos = InvoiceTotalAmount::where('random_string', 'P9PX11737713339')
    ->where('parent_id','not regexp', '^NK')
    ->get(10)->groupBy(['oracle_job_number']);

    return $dataKronos;
    $dataNonKronos = InvoiceTotalAmount::where('random_string', 'P9PX11737713339')
    ->where('parent_id','regexp', '^NK')
    ->get()->groupBy(['oracle_job_number']);



    return (new ExportInvoice)->download('invoice.xlsx');

});


Route::get('test', function () {
    try {
        $temptimesheet = TempTimeSheet::where('random_string', "cVBWz1737706275")->first();
        $startDate = Carbon::parse($temptimesheet->from_date);
        $endDate = Carbon::parse($temptimesheet->to_date);
        $tempTimesheetId = $temptimesheet->id; // Replace 'x' with the actual temp_timesheet_id
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

        $dailyRates = DailyRate::where('temptimesheet_string', $temptimesheet->random_string)->with('DailyDetails:daily_rate_string,value,date')->get();

        if($dailyRates->isEmpty()){
            $dailyRates = [];
        }

        $data_kronos = tempTimesheetLine::where('temp_timesheet_id', $tempTimesheetId)
        ->where('parent_id','not regexp', '^NK')
        ->with('overtimeTimesheet')
        ->lazy()->sortBy(['parent_id', 'oracle_job_number', 'employee_name']);

        // return $data_kronos->groupBy(['oracle_job_number', 'parent_id', 'no', 'Kronos_job_number']);

        $data_nk = tempTimesheetLine::where('temp_timesheet_id', $tempTimesheetId)
        ->where('parent_id','regexp', '^NK')
        ->with('overtimeTimesheet')
        // ->get(['id', 'no', 'job_dissipline', 'date', 'actual_hours', 'total_overtime_hours', 'paid_hours', 'custom_id', 'basic_hours', 'slo_no', 'oracle_job_number', 'Kronos_job_number', 'parent_id', 'rate', 'employee_name', 'deduction_hours'])->sortBy(['parent_id', 'oracle_job_number', 'employee_name']);
        ->lazy()->sortBy(['parent_id', 'oracle_job_number', 'employee_name']);

        $invoice_total_amounts = [];

        $data_kronos = $data_kronos->groupBy(['oracle_job_number', 'parent_id', 'no', 'Kronos_job_number'])
        ->map(function ($byOracle, $oracleID) use (&$holiday, &$employee_rate_details, &$invoice_total_amounts, &$temptimesheet) {
            return $byOracle->map(function ($byParentID, $parentID) use (&$holiday, &$total, &$employee_rate_details, &$invoice_total_amounts, &$temptimesheet, $oracleID) {
                $total = [
                    'paid_hours_total' => 0,
                    'actual_hours_total' => 0,
                    'total_overtime_perdate' => [],
                    'total_amount' => 0,
                ];
                $data = $byParentID->map(function ($byKronos) use (&$holiday, &$total, &$employee_rate_details, &$temptimesheet) {
                    return $byKronos->map(function ($byNo) use (&$holiday, &$total, &$employee_rate_details, &$temptimesheet) {
                            $emp = $byNo->first();
                            $emp_rates = $employee_rate_details->where('emp_id', $emp['no'])->first();
                            $result = [
                                'emp' => $emp['no'],
                                'classification' => $emp_rates->classification ?? $emp['job_dissipline'],
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
                            $byNo->each(function ($employeeData) use (&$result, &$holiday, &$total) {
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
                                    'basic_hours' => (double)$employeeData['basic_hours'] - (double)$employeeData['deduction_hours'],
                                ];
                                //sum total overtime hours per date
                                $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                                    return $overtime;
                                }) + (double)$employeeData["basic_hours"] - (double)$employeeData["deduction_hours"];
                                if (isset($total['total_overtime_perdate'][$date])) {
                                    $total['total_overtime_perdate'][$date] += $sum;
                                } else {
                                    $total['total_overtime_perdate'][$date] = $sum;
                                }


                            });

                            $total['paid_hours_total'] += (double) $result['paid_hours_total'];
                            $total['actual_hours_total'] += (double) $result['actual_hours_total'];

                            $amount = bcmul($result['paid_hours_total'], $result['rate'], 6);
                            $eti_bonus = bcdiv(bcmul($amount, $temptimesheet["eti_bonus_percentage"], 6), 100, 6);
                            $amount_total = bcadd($amount, $eti_bonus, 6);

                            $total['total_amount'] = bcadd($total['total_amount'], $amount_total, 6);


                            return $result;
                    })->values();
                });

                if(!isset($invoice_total_amounts[$oracleID . "_" . $parentID])){
                    $invoice_total_amounts[$oracleID . "_" . $parentID] = [
                        'random_string' => $temptimesheet->random_string,
                        'oracle_job_number' => $oracleID,
                        'parent_id' => $parentID,
                        'total_amount' => $total['total_amount'],
                        'total_hours' => $total['actual_hours_total'],
                    ];
                }
                return [
                    "data" => $data,
                    // total overtime hours from data
                    "total_overtime_hours" => $total['total_overtime_perdate'],
                    "paid_hours_total" => $total['paid_hours_total'],
                    "actual_hours_total" => $total['actual_hours_total'],
                ];
            });
        });

        $data_nk = $data_nk->groupBy(['oracle_job_number', 'parent_id', 'no', 'Kronos_job_number'])
        ->map(function ($byOracle, $oracleID) use (&$holiday, &$employee_rate_details, &$invoice_total_amounts, &$temptimesheet) {
            return $byOracle->map(function ($byParentID, $parentID) use (&$holiday, &$total, &$employee_rate_details, &$invoice_total_amounts, &$temptimesheet, $oracleID) {
                $total = [
                    'paid_hours_total' => 0,
                    'actual_hours_total' => 0,
                    'total_overtime_perdate' => [],
                    'total_amount' => 0,
                ];
                $data = $byParentID->map(function ($byKronos) use (&$holiday, &$total, &$employee_rate_details, &$temptimesheet) {
                    return $byKronos->map(function ($byNo) use (&$holiday, &$total, &$employee_rate_details, &$temptimesheet) {
                            $emp = $byNo->first();
                            $emp_rates = $employee_rate_details->where('emp_id', $emp['no'])->first();
                            $result = [
                                'emp' => $emp['no'],
                                'classification' => $emp_rates->classification ?? $emp['job_dissipline'],
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
                            $byNo->each(function ($employeeData) use (&$result, &$holiday, &$total) {
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
                                    'basic_hours' => (double)$employeeData['basic_hours'] - (double)$employeeData['deduction_hours'],
                                ];
                                //sum total overtime hours per date
                                $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                                    return $overtime;
                                }) + (double)$employeeData["basic_hours"] - (double)$employeeData["deduction_hours"];
                                if (isset($total['total_overtime_perdate'][$date])) {
                                    $total['total_overtime_perdate'][$date] += $sum;
                                } else {
                                    $total['total_overtime_perdate'][$date] = $sum;
                                }


                            });

                            $total['paid_hours_total'] += (double) $result['paid_hours_total'];
                            $total['actual_hours_total'] += (double) $result['actual_hours_total'];

                            $amount = bcmul($result['paid_hours_total'], $result['rate'], 6);
                            $eti_bonus = bcdiv(bcmul($amount, $temptimesheet["eti_bonus_percentage"], 6), 100, 6);
                            $amount_total = bcadd($amount, $eti_bonus, 6);

                            $total['total_amount'] = bcadd($total['total_amount'], $amount_total, 6);


                            return $result;
                    })->values();
                });

                if(!isset($invoice_total_amounts[$oracleID . "_" . $parentID])){
                    $invoice_total_amounts[$oracleID . "_" . $parentID] = [
                        'random_string' => $temptimesheet->random_string,
                        'oracle_job_number' => $oracleID,
                        'parent_id' => $parentID,
                        'total_amount' => $total['total_amount'],
                        'total_hours' => $total['actual_hours_total'],
                    ];
                }
                return [
                    "data" => $data,
                    // total overtime hours from data
                    "total_overtime_hours" => $total['total_overtime_perdate'],
                    "paid_hours_total" => $total['paid_hours_total'],
                    "actual_hours_total" => $total['actual_hours_total'],
                ];
            });
        });

        $data_kronos = $data_kronos->collect();
        $data_nk = $data_nk->collect();

        $chunk_invoice = array_chunk($invoice_total_amounts, 500);
        foreach ($chunk_invoice as $key => $chunk) {
            InvoiceTotalAmount::insert($chunk);
        }

    } catch (\Throwable $th) {
        return $th;
    }
});
