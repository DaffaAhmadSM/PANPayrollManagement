<?php

namespace App\Exports;

use App\Models\DailyRate;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PNSINVExportDaily implements WithMultipleSheets
{

    use Exportable, SerializesModels;

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
    protected $days;

    public function __construct($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $prCode, $days)
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
        $this->days = $days;
    }

    public function sheets(): array
    {
        $sheets = [];
        $chunk = $this->data_chunk;
        $count = $this->count;
        $tempTimesheet = $this->temptimesheet;
        $customerData = $this->customerData;
        $date1 = $this->date1;
        $date2 = $this->date2;

        $subcount = 1;

        $sheets[] = new InvoiceSetup($date1, $date2);

        $sheets[] = new InvoiceItemGroup($chunk, $tempTimesheet, $customerData, (string) $count, "", $count);
        $string_ids = $chunk->pluck("string_id")->toArray();


        foreach ($chunk as $key => $value) {
            $dailyRates = DailyRate::where('string_id', $value->string_id)->with('DailyDetails:daily_rate_string,value,date')->get();
            $name = (string) $count . "." . (string) $subcount;
            $sheets[] = new InvoiceItemDetailDaily($dailyRates, $tempTimesheet, (string) $count, $this->days, $count, $value->oracle_job_number);
            $subcount++;
        }

        if ($dailyRates->isEmpty()) {
            $dailyRates = [];
        }

        return $sheets;
    }
}
