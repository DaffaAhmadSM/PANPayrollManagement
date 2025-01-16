<?php

namespace App\Exports;

use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeDepartment;
use App\Models\EmployeeRateDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TempTimesheetExport implements FromView, ShouldQueue, ShouldAutoSize, WithStyles
{
    use Exportable, SerializesModels, InteractsWithQueue;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $random_string;
    protected $real_timesheet;
    public function __construct($random_string, $real_timesheet)
    {
        $this->random_string = $random_string;
        $this->real_timesheet = $real_timesheet;
    }

    public function view(): View
    {
    try {
        $temptimesheet = TempTimeSheet::where('random_string', $this->random_string)->first();
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
        $employee_department = EmployeeDepartment::orderBy('created_at', 'desc')->distinct("emp_id")->get();
        // return $employee_department;
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
        $data = tempTimesheetLine::where('temp_timesheet_id', $tempTimesheetId)
            ->with('overtimeTimesheet')
            // ->get(['id', 'no', 'job_dissipline', 'date', 'actual_hours', 'total_overtime_hours', 'paid_hours', 'custom_id', 'basic_hours', 'slo_no', 'oracle_job_number', 'Kronos_job_number', 'parent_id', 'rate', 'employee_name', 'deduction_hours'])->sortBy(['Kronos_job_number', 'oracle_job_number']);
            ->lazy()->sortBy(['employee_name', 'Kronos_job_number', 'oracle_job_number']);
        // return $data->groupBy(['employee_name', 'Kronos_job_number', 'oracle_job_number']);

        $output = $data->groupBy(['no', 'Kronos_job_number', 'oracle_job_number'])
        ->map(function ($byKronos) use (&$holiday, &$employee_rate_details, &$employee_department) {
            $total = [
                'paid_hours_total' => 0,
                'actual_hours_total' => 0,
                'total_overtime_perdate' => [],
            ];
       
            $employee = $byKronos->first()->first()->first();
            $dep = $employee_department->where('emp_id', $employee['no'])->first()["department"] ?? "N/A";

            $data = $byKronos->map(function ($byOracle) use (&$holiday, &$total, &$employee_rate_details, &$employee) {
                return $byOracle->map(function ($byEmployee) use (&$holiday, &$total, &$employee_rate_details, &$employee) {
                        
                        $emp_rates = $employee_rate_details->where('emp_id', $employee['no'])->first();
                        $current_oracle = $byEmployee->first();
                        $result = [
                            'emp' => $employee['no'],
                            'classification' => $emp_rates->classification ?? $employee['job_dissipline'],
                            'Kronos_job_number' => $current_oracle["Kronos_job_number"],
                            'parent_id' => $current_oracle["parent_id"],
                            'employee_name' => $current_oracle["employee_name"],
                            'slo_no' => $current_oracle["slo_no"],
                            'oracle_job_number' => $current_oracle["oracle_job_number"],
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
                            if (!isset($result['dates'][$date])) {
                                $result['dates'][$date] = [
                                    'overtime_timesheet' => $employeeData->overtime_timesheet,
                                    'is_holiday' => $is_holiday,
                                    'basic_hours' => (double)$employeeData['basic_hours'] - (double)$employeeData['deduction_hours'],
                                ];
                            }
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

                        return $result;
                    })->values();
                })->collapse();
            return[
                "dep" => $dep,
                "data" => $data,
                "total_overtime_hours" => $total['total_overtime_perdate'],
                "paid_hours_total" => $total['paid_hours_total'],
                "actual_hours_total" => $total['actual_hours_total'],];
        })->sortKeys();
        $output =  $output->groupBy('dep')->sortKeysUsing('strnatcasecmp');
        return view('excel.timesheet-export-pns', compact('days', 'output', 'temptimesheet'));
        } catch (\Throwable $th) {
            $this->failed($th);
        }
    }

    public function failed($exception)
    {
        Log::error($exception);
        $this->real_timesheet->update([
            'status' => 'failed'
        ]);
        $this->fail($exception);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setShowGridlines(false);
    }
}
