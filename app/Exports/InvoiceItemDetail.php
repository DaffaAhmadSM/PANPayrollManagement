<?php

namespace App\Exports;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\EmployeeRate;
use App\Models\CalendarHoliday;
use App\Models\EmployeeRateDetail;
use App\Models\tempTimesheetLine;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceItemDetail implements FromView, ShouldAutoSize, WithTitle
{

    protected $oracle_job_number;
    protected $tempTimesheet;
    protected $customerData;
    protected $title = '1';
    protected $days1;
    protected $days2;
    protected $employee_rate_details;
    protected $holiday;
    protected $date1;
    protected $date1end;
    protected $date2start;
    protected $date2;


    public function __construct($oracle_job_number,  $tempTimesheet, &$customerData, $title,  $days1, $days2, &$employee_rate_details, $holiday, $date1, $date1end, $date2start, $date2)
    {
        $this->oracle_job_number = $oracle_job_number;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->title = $title;
        $this->days1 = $days1;
        $this->days2 = $days2;
        $this->employee_rate_details = $employee_rate_details;
        $this->holiday = $holiday;
        $this->date1 = $date1;
        $this->date1end = $date1end;
        $this->date2start = $date2start;
        $this->date2 = $date2;
    }

    public function view() : View { 
        
        $employee_rate_details = $this->employee_rate_details;
        $holiday = $this->holiday;
        $date1 = $this->date1;
        $date1end = $this->date1end;
        $date2start = $this->date2start;
        $date2 = $this->date2;
        $days1 = $this->days1;
        $days2 = $this->days2;
        $temptimesheet = $this->tempTimesheet;
        $oracle_job_number = $this->oracle_job_number;
        

        // $data = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
        // ->where("oracle_job_number", $value->oracle_job_number)
        // ->lazy()
        // ->sortBy([ "employee_name", "Kronos_job_number", "oracle_job_number",]);

        // data1 from date1 to date1end
        $data1 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
        ->where("oracle_job_number", $this->oracle_job_number)
        ->whereBetween("date", [$date1, $date1end])
        ->lazy();

        // data2 from date2start to date2

        $data2 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
        ->where("oracle_job_number", $this->oracle_job_number)
        ->whereBetween("date", [$date2start, $date2])
        ->lazy();

        $data1 = $data1->groupBy("no")
        ->map(function($item) use (&$employee_rate_details, &$holiday) {
            $emp = $item->first();
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
            $item->each(function ($employeeData) use (&$holiday, &$result) {
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
                // $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                //     return $overtime;
                // }) + (double)$employeeData["basic_hours"] - (double)$employeeData["deduction_hours"];
                // if (isset($total['total_overtime_perdate'][$date])) {
                //     $total['total_overtime_perdate'][$date] += $sum;
                // } else {
                //     $total['total_overtime_perdate'][$date] = $sum;
                // }


            });

            return $result;
        });

        $data2 = $data2->groupBy("no")
        ->map(function($item) use (&$employee_rate_details, &$holiday) {
            $emp = $item->first();
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
            $item->each(function ($employeeData) use (&$holiday, &$result) {
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
                // $sum = $employeeData->overtime_timesheet->sum(function ($overtime) {
                //     return $overtime;
                // }) + (double)$employeeData["basic_hours"] - (double)$employeeData["deduction_hours"];
                // if (isset($total['total_overtime_perdate'][$date])) {
                //     $total['total_overtime_perdate'][$date] += $sum;
                // } else {
                //     $total['total_overtime_perdate'][$date] = $sum;
                // }


            });

            return $result;
        });

        

        return view('excel.invoice.invoice-item-detail', compact('data1', 'data2', 'days1', 'days2', 'temptimesheet', 'oracle_job_number'));
    }

    public function title(): string
    {
        return $this->title;
    }
    
}
