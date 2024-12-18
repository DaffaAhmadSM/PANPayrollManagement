<?php

use Illuminate\Support\Str;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeRateDetail;
use App\Exports\TempTimesheetExport;
use App\Exports\tempTimesheetExportMI;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $dateTime = Carbon::now();
    $dateTime = $dateTime->format('YmdHis');
    (new TempTimesheetExport(5))->store("TSPNS" . $dateTime . '.xlsx', 'local');
});

// Route::get('test', function () {
//     ini_set('max_execution_time', 300);
//     $temptimesheet = TempTimeSheet::find(8);
//         $startDate = Carbon::parse($temptimesheet->from_date);
//         $endDate = Carbon::parse($temptimesheet->to_date);
//         $tempTimesheetId = 8; // Replace 'x' with the actual temp_timesheet_id
//         $holiday = CalendarHoliday::whereBetween('date', [$startDate, $endDate])->get();
//         $period = new DatePeriod(
//             new DateTime($startDate),
//             new DateInterval('P1D'),
//             (new DateTime($endDate))->modify('+1 day')
//         );

//         $employee_rates = EmployeeRate::where('random_string', $temptimesheet->rate_id)->first();
//         $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $employee_rates->id)->get();
//         unset($employee_rates);

//         $days = [];
//         foreach ($period as $date) {
//             $isholiday = false;
//             // check if day is sunday
//             if ($date->format('w') == 0) {
//                 $isholiday = true;
//             }
//             // check if day is holiday
//             $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
//             if ($holidayCheck) {
//                 $isholiday = true;
//             }


//             $days[] = [
//                 'date' => $date->format('M d'),
//                 'is_holiday' => $isholiday
//             ];
//         }
//         // return $days;

//         // Build the query
//         $data = tempTimesheetLine::where('temp_timesheet_id', $tempTimesheetId)
//             ->with('overtimeTimesheet')
//             ->get(['id', 'no', 'job_dissipline', 'date', 'actual_hours', 'total_overtime_hours', 'paid_hours', 'custom_id', 'basic_hours', 'slo_no', 'oracle_job_number', 'Kronos_job_number', 'parent_id', 'rate', 'employee_name', 'deduction_hours'])->sortBy(['parent_id', 'oracle_job_number']);

//         // foreach ($data->groupBy(['employee_name', 'Kronos_job_number', 'oracle_job_number']) as $byKronos){
//         //     foreach ($byKronos as $byOracle){
//         //         foreach($byOracle as $byEmployee){
//         //             return $byEmployee;
//         //         }
//         //     }
//         // }

//         // return $data->groupBy(['Kronos_job_number', 'oracle_job_number', 'employee_name']);

//         $output = $data->groupBy(['Kronos_job_number', 'oracle_job_number', 'employee_name'])
//         ->map(function ($byKronos) use (&$holiday, &$employee_rate_details) {
           
//             $total = [
//                 'paid_hours_total' => 0,
//                 'actual_hours_total' => 0,
//                 'total_overtime_perdate' => [],
//             ];
//             $data = $byKronos->map(function ($byOracle) use (&$holiday, &$total, &$employee_rate_details) {
//                 return $byOracle->map(function ($byEmployee) use (&$holiday, &$total, &$employee_rate_details) {
//                         $emp = $byEmployee->first();
//                         $emp_rates = $employee_rate_details->where('emp_id', $emp['no'])->first();
//                         $result = [
//                             'emp' => $emp['no'],
//                             'classification' => $emp['job_dissipline'],
//                             'Kronos_job_number' => $emp["Kronos_job_number"],
//                             'parent_id' => $emp["parent_id"],
//                             'employee_name' => $emp["employee_name"],
//                             'slo_no' => $emp["slo_no"],
//                             'oracle_job_number' => $emp["oracle_job_number"],
//                             'rate' => $emp_rates->rate ?? 1,
//                             'dates' => [],
//                             'paid_hours_total' => 0,
//                             'actual_hours_total' => 0,
//                             'overtime_hours_total' => 0
//                         ];
//                         $byEmployee->each(function ($employeeData) use (&$result, &$holiday, &$total) {
//                             $is_holiday = false;
//                             // if day is sunday then is_holiday = true
//                             $date = Carbon::parse($employeeData["date"]);
//                             $result['paid_hours_total'] += (double) $employeeData["paid_hours"];
//                             $result['actual_hours_total'] += (double) $employeeData["actual_hours"];
//                             if ($date->dayOfWeek == 0) {
//                                 $is_holiday = true;
//                             }
//                             // check if day is holiday
//                             $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
//                             if ($holidayCheck) {
//                                 $is_holiday = true;
//                             }
//                             // filter $employeeData->OvertimeTimesheet and get only hours value
//                             $employeeData->overtime_timesheet = $employeeData->OvertimeTimesheet->map(function ($overtime) use (&$result) {
//                                 $result['overtime_hours_total'] += (double) $overtime->total_hours;
//                                 return $overtime->hours;
                                
//                             });

//                             // $result['total_overtime_hours_total'] += (double) $employeeData["total_overtime_hours"];
//                             $date = $date->format('m-d-Y');
//                             if (!isset($result['dates'][$date])) {
//                                 $result['dates'][$date] = [
//                                     'overtime_timesheet' => $employeeData->overtime_timesheet,
//                                     'is_holiday' => $is_holiday,
//                                     'basic_hours' => (double)$employeeData['basic_hours'] - (double)$employeeData['deduction_hours'],
//                                 ];
//                             }
//                                 //sum total overtime hours per date
//                                 $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
//                                     return $overtime;
//                                 }) + (double)$employeeData["basic_hours"] - (double)$employeeData["deduction_hours"];
//                                 if (isset($total['total_overtime_perdate'][$date])) {
//                                     $total['total_overtime_perdate'][$date] += $sum;
//                                 } else {
//                                     $total['total_overtime_perdate'][$date] = $sum;
//                                 }

                                
//                             });

//                             $total['paid_hours_total'] += (double) $result['paid_hours_total'];
//                             $total['actual_hours_total'] += (double) $result['actual_hours_total'];

//                         return $result;
//                     })->values();
//                 })->collapse();
//             return [
//                 "data" => $data,
//                 // total overtime hours from data
//                 "total_overtime_hours" => $total['total_overtime_perdate'],
//                 "paid_hours_total" => $total['paid_hours_total'],
//                 "actual_hours_total" => $total['actual_hours_total'],
//             ];
//         });
//         return $output;
//         return view('excel.timesheet-export', compact('days', 'output', 'temptimesheet'));
// });

Route::get('test', function () {
    $dateTime = Carbon::now();
    $dateTime = $dateTime->format('YmdHis');
    (new tempTimesheetExportMI(4))->store("TSMI" . $dateTime . '.xlsx', 'local');
});
