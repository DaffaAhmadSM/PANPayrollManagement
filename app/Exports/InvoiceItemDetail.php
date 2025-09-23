<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceItemDetail implements FromView, ShouldAutoSize, WithTitle, WithStyles, WithDrawings
{

    use Exportable, SerializesModels;
    protected $tempTimesheet;
    protected $title = '1';
    protected $days1;
    protected $days2;
    protected $employee_rate_details;
    protected $holiday;
    protected $date1;
    protected $date1end;
    protected $date2start;
    protected $date2;
    protected $data1;
    protected $data2;

    protected $oracle_job_number;

    protected $count;


    public function __construct(Collection $data1, $tempTimesheet, Collection $data2, string $title, $days1, $days2, &$employee_rate_details, $holiday, $oracle_job_number, $count)
    {
        $this->data1 = $data1;
        $this->data2 = $data2;
        $this->tempTimesheet = $tempTimesheet;
        $this->title = $title;
        $this->days1 = $days1;
        $this->days2 = $days2;
        $this->employee_rate_details = $employee_rate_details;
        $this->holiday = $holiday;
        $this->oracle_job_number = $oracle_job_number;
        $this->count = $count;

        $this->data1 = $this->data1->groupBy("no")
            ->map(function ($item) use (&$employee_rate_details, &$holiday) {
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

                    // Validate and sanitize basic_hours and deduction_hours
                    $basicHours = is_numeric($employeeData['basic_hours'] ?? 0) ? (double) $employeeData['basic_hours'] : 0;
                    $deductionHours = is_numeric($employeeData['deduction_hours'] ?? 0) ? (double) $employeeData['deduction_hours'] : 0;

                    if (!isset($result['dates'][$date])) {
                        $result['dates'][$date] = [
                            'overtime_timesheet' => $employeeData->overtime_timesheet ?? collect([]),
                            'is_holiday' => $is_holiday,
                            'basic_hours' => $basicHours - $deductionHours,
                        ];
                    } else {
                        try {
                            // Ensure both collections are valid before converting to arrays
                            $existingOvertimeArray = [];
                            $newOvertimeArray = [];

                            if (isset($result['dates'][$date]['overtime_timesheet']) && method_exists($result['dates'][$date]['overtime_timesheet'], 'toArray')) {
                                $existingOvertimeArray = $result['dates'][$date]['overtime_timesheet']->toArray();
                            }

                            if (isset($employeeData->overtime_timesheet) && method_exists($employeeData->overtime_timesheet, 'toArray')) {
                                $newOvertimeArray = $employeeData->overtime_timesheet->toArray();
                            }

                            $result['dates'][$date]['overtime_timesheet'] = collect($this->sumArraysElementWise($existingOvertimeArray, $newOvertimeArray));
                            $result['dates'][$date]['basic_hours'] += $basicHours - $deductionHours;
                        } catch (\Exception $e) {
                            // Log error and use fallback
                            Log::warning('Error summing overtime arrays for date ' . $date . ': ' . $e->getMessage());
                            $result['dates'][$date]['overtime_timesheet'] = $employeeData->overtime_timesheet ?? collect([]);
                        }
                    }

                    // $result['dates'][$date] = [
                    //     'overtime_timesheet' => $employeeData->overtime_timesheet,
                    //     'is_holiday' => $is_holiday,
                    //     'basic_hours' => (double) $employeeData['basic_hours'] - (double) $employeeData['deduction_hours'],
                    // ];
    

                });

                if ($result['actual_hours_total'] == 0) {
                    //    delete this item
                    return null;
                }

                return $result;
            });

        $this->data2 = $this->data2->groupBy("no")
            ->map(function ($item) use (&$employee_rate_details, &$holiday) {
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

                    // Validate and sanitize basic_hours and deduction_hours
                    $basicHours = is_numeric($employeeData['basic_hours'] ?? 0) ? (double) $employeeData['basic_hours'] : 0;
                    $deductionHours = is_numeric($employeeData['deduction_hours'] ?? 0) ? (double) $employeeData['deduction_hours'] : 0;

                    if (!isset($result['dates'][$date])) {
                        $result['dates'][$date] = [
                            'overtime_timesheet' => $employeeData->overtime_timesheet ?? collect([]),
                            'is_holiday' => $is_holiday,
                            'basic_hours' => $basicHours - $deductionHours,
                        ];
                    } else {
                        try {
                            // Ensure both collections are valid before converting to arrays
                            $existingOvertimeArray = [];
                            $newOvertimeArray = [];

                            if (isset($result['dates'][$date]['overtime_timesheet']) && method_exists($result['dates'][$date]['overtime_timesheet'], 'toArray')) {
                                $existingOvertimeArray = $result['dates'][$date]['overtime_timesheet']->toArray();
                            }

                            if (isset($employeeData->overtime_timesheet) && method_exists($employeeData->overtime_timesheet, 'toArray')) {
                                $newOvertimeArray = $employeeData->overtime_timesheet->toArray();
                            }

                            $result['dates'][$date]['overtime_timesheet'] = collect($this->sumArraysElementWise($existingOvertimeArray, $newOvertimeArray));

                            $result['dates'][$date]['basic_hours'] += $basicHours - $deductionHours;
                        } catch (\Exception $e) {
                            // Log error and use fallback
                            Log::warning('Error summing overtime arrays for date ' . $date . ': ' . $e->getMessage());
                            $result['dates'][$date]['overtime_timesheet'] = $employeeData->overtime_timesheet ?? collect([]);
                        }
                    }
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

                if ($result['actual_hours_total'] == 0) {
                    return null;
                }

                return $result;
            });

        $this->data1 = $this->data1->reject(function ($employeeData) {
            return $employeeData == null;
        });

        $this->data2 = $this->data2->reject(
            function ($employeeData) {
                return $employeeData == null;
            }
        );
    }
    // 22

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle("A:Z")->getFont()->setName("Times New Roman");
        $sheet->getStyle("A:Z")->getFont()->setSize(9);
        $sheet->setShowGridlines(false);

        $sheet->getParentOrThrow()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'size' => 9
            ]
        ]);

        $sheet->getSheetView()->setView('pageBreakPreview');
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_TABLOID);
    }

    public function view(): View
    {

        $employee_rate_details = $this->employee_rate_details;
        $holiday = $this->holiday;
        $days1 = $this->days1;
        $days2 = $this->days2;
        $temptimesheet = $this->tempTimesheet;
        $oracle_job_number = $this->oracle_job_number;
        $count = $this->count;

        // data1 from date1 to date1end
        $data1 = $this->data1;

        // data2 from date2start to date2

        $data2 = $this->data2;



        return view('excel.invoice.invoice-item-detail', compact('data1', 'data2', 'days1', 'days2', 'temptimesheet', 'oracle_job_number', 'count'));
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('ttd');
        $drawing->setDescription('ttd');
        $drawing->setPath(public_path('/images/ttd.png'));
        $drawing->setWidth(85);
        $drawing->setHeight(85);

        $data_count = $this->data1->count() + $this->data2->count();

        $drawing->setCoordinates('Z' . $data_count + 23);

        return $drawing;
    }

    public function title(): string
    {
        return $this->title;
    }

    private function sumArraysElementWise(array ...$arrays): array
    {
        $arr = array_map(function (...$numbers) {
            return array_sum($numbers);
        }, ...$arrays);

        Log::info('Summed arrays: ' . json_encode($arr));

        return $arr;
    }

}
