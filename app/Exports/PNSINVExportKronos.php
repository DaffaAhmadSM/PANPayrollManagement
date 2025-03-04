<?php

namespace App\Exports;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\tempTimesheetLine;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PNSINVExportKronos implements WithMultipleSheets
{

    use SerializesModels, Exportable;

    protected $string_id;
    protected $data_chunk;
    protected $count;
    protected $temptimesheet;
    protected $customerData;
    protected $date1;
    protected $date1end;
    protected $date2start;
    protected $date2;
    protected $employee_rate_details;
    protected $holiday;
    protected $prCode;
    protected $days1;
    protected $days2;

    public function __construct($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $prCode, $days1, $days2)
    {
        $this->string_id = $string_id;
        $this->data_chunk = $chunk_data;
        $this->count = $count;
        $this->temptimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->date1 = $date1;
        $this->date1end = $date1end;
        $this->date2 = $date2;
        $this->date2start = $date2start;
        $this->employee_rate_details = $employee_rate_details;
        $this->holiday = $holiday;
        $this->prCode = $prCode;
        $this->days1 = $days1;
        $this->days2 = $days2;
    }

    public function sheets(): array
    {
        $sheets = [];
        $chunk = $this->data_chunk;
        $count = $this->count;
        $tempTimesheet = $this->temptimesheet;
        $customerData = $this->customerData;
        $date1 = $this->date1;
        $date1end = $this->date1end;
        $date2 = $this->date2;
        $date2start = $this->date2start;
        $employee_rate_details = $this->employee_rate_details;
        $holiday = $this->holiday;
        $prCode = $this->prCode;
        $days1 = $this->days1;
        $days2 = $this->days2;

        $subcount = 1;

        $sheets[] = new InvoiceSetup($date1, $date2);

        $oracle_job = $chunk->pluck("oracle_job_number")->toArray();
        $sheets[] = new InvoiceItemGroup($chunk, $tempTimesheet, $customerData, (string) $count, $prCode, $count);
        $data1 = $data1 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with("overtimeTimesheet")
            ->whereIn("oracle_job_number", $oracle_job)
            ->whereBetween("date", [$date1, $date1end])
            ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        $data2 = tempTimesheetLine::where("temp_timesheet_id", $tempTimesheet->id)->with("overtimeTimesheet")
            ->whereIn("oracle_job_number", $oracle_job)
            ->whereBetween("date", [$date2start, $date2])
            ->get()->sortBy(["employee_name"])->groupBy("oracle_job_number");

        foreach ($chunk as $key => $value) {
            $name = (string) $count . "." . (string) $subcount;
            $sheets[] = new InvoiceItemDetail($data1[$value->oracle_job_number], $tempTimesheet, $data2[$value->oracle_job_number], (string) $name, $days1, $days2, $employee_rate_details, $holiday, $value->oracle_job_number, $count);
            $subcount++;
        }


        return $sheets;
    }
}
