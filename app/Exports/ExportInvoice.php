<?php

namespace App\Exports;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\DailyRate;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeRateDetail;
use App\Models\InvoiceTotalAmount;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportInvoice implements WithMultipleSheets
{
    use Exportable, SerializesModels;

    protected $string_id;

    public function __construct($string_id)
    {
        $this->string_id = $string_id;
    }

    public function sheets(): array
    {

        $string_id = $this->string_id;

        $tempTimesheet = TempTimeSheet::where('random_string', $string_id)->first();

        $customerData = Customer::where('id', $tempTimesheet->customer_id)->first();

        $dataKronos = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'not regexp', '^NK')
            ->lazy()->groupBy(['parent_id']);

        $dataKronos = $dataKronos->map(function ($item) {
            return $item->chunk(15);
        });

        $dataNonKronos = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'regexp', '^NK$')
            ->get()->groupBy(['parent_id']);

        $dataNonKronosPlus = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'regexp', '^NK-')
            ->get()->groupBy(['parent_id']);

        $dataNonKronosPlus = $dataNonKronosPlus->map(function ($item) {
            return $item->chunk(15);
        });

        $dataDailyRate = DailyRate::where('temptimesheet_string', $string_id)->get();

        $dataNonKronos = [
            "NK" => $dataNonKronos->collapse(),
            "NK-" => $dataNonKronosPlus,
            "Daily" => $dataDailyRate
        ];

        $date1 = Carbon::parse($tempTimesheet->from_date);
        $date1end = Carbon::parse($tempTimesheet->to_date)->subDays(15);
        // date2 end date - 15 days to get the start date
        $date2start = Carbon::parse($tempTimesheet->to_date)->subDays(14);
        $date2 = Carbon::parse($tempTimesheet->to_date);

        $holiday = CalendarHoliday::whereBetween('date', [$date1, $date2])->get();
        $period1 = new DatePeriod(
            new DateTime($date1),
            new DateInterval('P1D'),
            (new DateTime($date1end))->modify('+1 day')
        );

        $period2 = new DatePeriod(
            new DateTime($date2start),
            new DateInterval('P1D'),
            (new DateTime($date2))->modify('+1 day')
        );

        $days1 = [];
        foreach ($period1 as $date) {
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


            $days1[] = [
                'date' => $date->format('M d'),
                'is_holiday' => $isholiday
            ];
        }


        $days2 = [];

        foreach ($period2 as $date) {
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

            $days2[] = [
                'date' => $date->format('M d'),
                'is_holiday' => $isholiday
            ];
        }


        $employee_rates = EmployeeRate::where('random_string', $tempTimesheet->rate_id)->first();
        $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $employee_rates->id)->get();
        unset($employee_rates);
        $sheets = [];
        $count = 1;

        $sheets[] = new InvoiceSetup();

        $sheets[] = new ExportInvoiceData($dataKronos, $dataNonKronos, $tempTimesheet, $customerData);

        foreach ($dataKronos as $dataKey => $data) {

            foreach ($data as $key => $chunk) {
                $oracle_job = $chunk->pluck("oracle_job_number")->toArray();
                $sheets[] = new InvoiceItemGroup($chunk, $tempTimesheet, $customerData, (string) $count, $dataKey, $count);
                $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with("overtimeTimesheet")
                    ->whereIn("oracle_job_number", $oracle_job)
                    ->whereBetween("date", [$date1, $date1end])
                    ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                $data2 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with("overtimeTimesheet")
                    ->whereIn("oracle_job_number", $oracle_job)
                    ->whereBetween("date", [$date2start, $date2])
                    ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                foreach ($chunk as $key => $value) {
                    $sheets[] = new InvoiceItemDetail($data1[$value->oracle_job_number], $tempTimesheet, $data2[$value->oracle_job_number], (string) $count, $days1, $days2, $employee_rate_details, $holiday, $value->oracle_job_number, $count);
                }
                $count++;
            }
        }

        foreach ($dataNonKronos["NK-"] as $dataKey => $data) {

            foreach ($data as $key => $chunk) {
                $oracle_job = $chunk->pluck("oracle_job_number")->toArray();
                $sheets[] = new InvoiceItemGroup($chunk, $tempTimesheet, $customerData, (string) $count, "", $count);
                $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with([
                    'overtimeTimesheet' => function ($query) {
                        $query->select('id', 'custom_id', 'hours', 'total_hours');
                    }
                ])
                    ->whereIn("oracle_job_number", $oracle_job)
                    ->whereBetween("date", [$date1, $date1end])
                    ->select([
                        'id',
                        'no',
                        'employee_name',
                        'oracle_job_number',
                        'date',
                        'paid_hours',
                        'actual_hours',
                        'basic_hours',
                        'deduction_hours',
                        'job_dissipline'
                    ])
                    ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                $data2 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with("overtimeTimesheet")
                    ->whereIn("oracle_job_number", $oracle_job)
                    ->whereBetween("date", [$date2start, $date2])
                    ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                foreach ($chunk as $key => $value) {
                    $sheets[] = new InvoiceItemDetail($data1[$value->oracle_job_number], $tempTimesheet, $data2[$value->oracle_job_number], (string) $count, $days1, $days2, $employee_rate_details, $holiday, $value->oracle_job_number, $count);
                }
                $count++;
            }
        }


        $oracle_job = $dataNonKronos["NK"]->pluck("oracle_job_number")->toArray();
        $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with([
            'overtimeTimesheet' => function ($query) {
                $query->select('id', 'custom_id', 'hours', 'total_hours');
            }
        ])
            ->whereIn("oracle_job_number", $oracle_job)
            ->whereBetween("date", [$date1, $date1end])
            ->select([
                'id',
                'no',
                'employee_name',
                'oracle_job_number',
                'date',
                'paid_hours',
                'actual_hours',
                'basic_hours',
                'deduction_hours',
                'job_dissipline'
            ])
            ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        $data2 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with([
            'overtimeTimesheet' => function ($query) {
                $query->select('id', 'custom_id', 'hours', 'total_hours');
            }
        ])
            ->whereIn("oracle_job_number", $oracle_job)
            ->whereBetween("date", [$date2start, $date2])
            ->select([
                'id',
                'no',
                'employee_name',
                'oracle_job_number',
                'date',
                'paid_hours',
                'actual_hours',
                'basic_hours',
                'deduction_hours',
                'job_dissipline'
            ])
            ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        foreach ($dataNonKronos["NK"] as $dataKey => $data) {
            $sheets[] = new InvoiceItemGroup(collect([$data]), $tempTimesheet, $customerData, (string) $count, "", $count);

            $sheets[] = new InvoiceItemDetail($data1[$data->oracle_job_number], $tempTimesheet, $data2[$data->oracle_job_number], (string) $count, $days1, $days2, $employee_rate_details, $holiday, $data->oracle_job_number, $count);
            $count++;
        }


        // $sheets[] = new InvoiceItemGroup($dataNonKronos["Daily"], $tempTimesheet, $customerData, (string) $count, $dataKey);
        // foreach ($dataNonKronos["Daily"] as $dataKey => $data) {
        //     $sheets[] = new InvoiceItemDetail($data->oracle_job_number, $tempTimesheet, $customerData, (string) $count, $days1, $days2, $employee_rate_details, $holiday, /);
        // }

        return $sheets;
    }
}
