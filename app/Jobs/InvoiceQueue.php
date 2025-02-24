<?php

namespace App\Jobs;

use App\Exports\ExportInvoice;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceQueue implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $string_id;
    protected $filename;

    public function __construct($string_id, $filename)
    {
        $this->string_id = $string_id;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $string_id = $this->string_id;
        $filename = $this->filename;
        (new ExportInvoice(string_id: $string_id))->store("invoice/" . (string) $filename . '.xlsx', "public");
    }
}
