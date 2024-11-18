<?php

namespace App\Exports;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;

class TempTimesheetExport implements FromView, ShouldQueue
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $temp_timesheet_id;
    public function __construct($temp_timesheet_id)
    {
        $this->temp_timesheet_id = $temp_timesheet_id;
    }

    public function view(): View
    {
        $temptimesheet = TempTimeSheet::find($this->temp_timesheet_id);
        $startDate = Carbon::parse($temptimesheet->from_date);
        $endDate = Carbon::parse($temptimesheet->to_date);
        $tempTimesheetId = $this->temp_timesheet_id; // Replace 'x' with the actual temp_timesheet_id
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
            return $byKronos->map(function ($byOracle) use (&$holiday) {
                return $byOracle->map(function ($byEmployee) use (&$holiday) {
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
                    $byEmployee->each(function ($employeeData) use (&$result, &$holiday) {
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
                    });

                    return $result;
                });
            })->collapse();
        })->sortKeys();
        return view('excel.timesheet-export', compact('days', 'output'));
    }
}
