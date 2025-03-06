<?php

namespace App\Jobs;

use App\Models\InvoiceExportPath;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PNSInvoiceAddPath implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */

    protected $path;
    protected $filename;
    protected $string_id;
    protected $count;

    public function __construct($filename, $path, $string_id, $count = 1)
    {
        $this->filename = $filename;
        $this->path = $path;
        $this->string_id = $string_id;
        $this->count = $count;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        InvoiceExportPath::create([
            "filename" => $this->filename . "_" . $this->count,
            "file_path" => $this->path,
            "invoice_string_id" => $this->string_id
        ]);
    }
}
