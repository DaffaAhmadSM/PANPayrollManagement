<?php

namespace App\Exports;

use DateTime;
use DatePeriod;
use DateInterval;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\EmployeeRate;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\EmployeeRateDetail;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportInvoice implements WithMultipleSheets
{
    use Exportable, SerializesModels;

    protected $dataKronos;
    protected $dataNonKronos;
    protected $tempTimesheet;
    protected $customerData;

    public function __construct($dataKronos, $dataNonKronos, $tempTimesheet, $customerData)
    {
        $this->dataKronos = $dataKronos;
        $this->dataNonKronos = $dataNonKronos;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
    }

    public function sheets(): array
    {

        $date1 = Carbon::parse($this->tempTimesheet->from_date);
        $date1end = Carbon::parse($this->tempTimesheet->to_date)->subDays(15);
        // date2 end date - 15 days to get the start date
        $date2start = Carbon::parse($this->tempTimesheet->to_date)->subDays(14);
        $date2 = Carbon::parse($this->tempTimesheet->to_date);

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


        $employee_rates = EmployeeRate::where('random_string', $this->tempTimesheet->rate_id)->first();
        $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $employee_rates->id)->get();
        unset($employee_rates);
        // make date into 2 parts
        $sheets = [];
        $count = 1;

        $sheets[] = new ExportInvoiceData($this->dataKronos, $this->dataNonKronos, $this->tempTimesheet, $this->customerData);

        foreach ($this->dataKronos as $dataKey => $data) {

            foreach ($data as $key => $chunk) {
               $oracle_job = $chunk->pluck("oracle_job_number")->toArray();
                $sheets[] = new InvoiceItemGroup($chunk, $this->tempTimesheet, $this->customerData, (string)$count, $dataKey);

                $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
                ->whereIn("oracle_job_number", $oracle_job)
                ->whereBetween("date", [$date1, $date1end])
                ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                $data2 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
                ->whereIn("oracle_job_number", $oracle_job)
                ->whereBetween("date", [$date2start, $date2])
                ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                foreach ($chunk as $key => $value) {
                    $sheets[] = new InvoiceItemDetail($data1[$value->oracle_job_number], $this->tempTimesheet, $data2[$value->oracle_job_number], (string)$count, $days1, $days2, $employee_rate_details, $holiday, $value->oracle_job_number);
                }
                $count++;
            }
        }

        foreach ($this->dataNonKronos["NK-"] as $dataKey => $data) {

            foreach ($data as $key => $chunk) {
               $oracle_job = $chunk->pluck("oracle_job_number")->toArray();
                $sheets[] = new InvoiceItemGroup($chunk, $this->tempTimesheet, $this->customerData, (string)$count, $dataKey);

                $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
                ->whereIn("oracle_job_number", $oracle_job)
                ->whereBetween("date", [$date1, $date1end])
                ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                $data2 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
                ->whereIn("oracle_job_number", $oracle_job)
                ->whereBetween("date", [$date2start, $date2])
                ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

                foreach ($chunk as $key => $value) {
                    $sheets[] = new InvoiceItemDetail($data1[$value->oracle_job_number], $this->tempTimesheet, $data2[$value->oracle_job_number], (string)$count, $days1, $days2, $employee_rate_details, $holiday, $value->oracle_job_number);
                }
                $count++;
            }
        }
        

        $oracle_job = $this->dataNonKronos["NK"]->pluck("oracle_job_number")->toArray();
        $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
        ->whereIn("oracle_job_number", $oracle_job)
        ->whereBetween("date", [$date1, $date1end])
        ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        $data2 = tempTimesheetLine::where("temp_timesheet_id", $this->tempTimesheet->id)->with("overtimeTimesheet")
        ->whereIn("oracle_job_number", $oracle_job)
        ->whereBetween("date", [$date2start, $date2])
        ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        foreach ($this->dataNonKronos["NK"] as $dataKey => $data) {
            $sheets[] = new InvoiceItemGroup(collect([$data]), $this->tempTimesheet, $this->customerData, (string)$count, $dataKey);
           
            $sheets[] = new InvoiceItemDetail($data1[$data->oracle_job_number], $this->tempTimesheet, $data2[$data->oracle_job_number], (string)$count, $days1, $days2, $employee_rate_details, $holiday, $data->oracle_job_number);
           $count++;
        }


        // $sheets[] = new InvoiceItemGroup($this->dataNonKronos["Daily"], $this->tempTimesheet, $this->customerData, (string)$count, $dataKey);
        // foreach ($this->dataNonKronos["Daily"] as $dataKey => $data) {
        //     $sheets[] = new InvoiceItemDetail($data->oracle_job_number, $this->tempTimesheet, $this->customerData, (string)$count, $days1, $days2, $employee_rate_details, $holiday, $date1, $date1end, $date2start, $date2);
        // }

        return $sheets;
    }
}
