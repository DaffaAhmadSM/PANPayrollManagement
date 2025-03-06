<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\DailyRate;
use App\Models\TempTimeSheet;
use Illuminate\Bus\Batchable;
use App\Jobs\PNSInvoiceSummary;
use App\Models\InvoiceTotalAmount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PNSInvoiceSummaryWrapper implements ShouldQueue
{
    use Queueable, Batchable;
    protected $date1;
    protected $date2;

    protected $path;
    protected $string_id;
    public function __construct($string_id, $date1, $date2, $path)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->path = $path;
        $this->string_id = $string_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date1 = $this->date1;
        $date2 = $this->date2;
        $path = $this->path;
        $string_id = $this->string_id;
        (new PNSInvoiceSummary($string_id, $date1, $date2))->store((string) $path . "summary" . '.xlsx', 'public');
    }
}
