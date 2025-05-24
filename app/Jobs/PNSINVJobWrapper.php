<?php

namespace App\Jobs;

use App\Exports\PNSINVExportDaily;
use App\Exports\PNSINVExportNKronos;
use Error;
use Exception;
use Illuminate\Auth\Events\Failed;
use Illuminate\Bus\Batchable;
use App\Exports\PNSINVExportKronos;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Log;

class PNSINVJobWrapper implements ShouldQueue
{
    use Queueable, Batchable;

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
    protected $type;
    protected $path;

    protected $days;

    public function __construct($string_id, $chunk_data, $count, $tempTimesheet, $customerData, Carbon $date1, Carbon $date1end, Carbon $date2start, Carbon $date2, $employee_rate_details, $holiday, $prCode, $days1, $days2, $path, string $type, $days=null)
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
        $this->type = $type;
        $this->path = $path;
        $this->days = $days;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $string_id = $this->string_id;
        $chunk_data = $this->data_chunk;
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
        $type = $this->type;
        $path = $this->path;
        $days = $this->days;

        switch ($type) {
            case 'kronos':
                (new PNSINVExportKronos($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $prCode, $days1, $days2))->store((string) $path . (string) $count . '.xlsx', 'public');
                break;
            case 'NK-':
                (new PNSINVExportNKronos($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, "NK", $days1, $days2))->store((string) $path . (string) $count . '.xlsx', 'public');
                break;
            case 'NK':
                (new PNSINVExportNKronos($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, "NK", $days1, $days2))->store((string) $path . (string) $count . '.xlsx', 'public');
                break;
            case 'daily':
                (new PNSINVExportDaily($string_id, $chunk_data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, "daily", $days))->store((string) $path . (string) $count . '.xlsx', 'public');
                break;
            default:
                $this->fail(new Error('Invalid type'));
                break;
        }
    }

    public function failed(Exception $exception)
    {
        Log::error($exception->getMessage());
    }
}
