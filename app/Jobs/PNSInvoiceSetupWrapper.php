<?php

namespace App\Jobs;

use App\Exports\InvoiceSetup;
use Illuminate\Bus\Batchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PNSInvoiceSetupWrapper implements ShouldQueue
{
    use Queueable, Batchable;

    protected $date1;
    protected $date2;

    protected $path;
    /**
     * Create a new job instance.
     */
    public function __construct($date1, $date2, $path)
    {
        // Initialize any properties if needed
        $this->date1 = $date1;
        $this->date2 = $date2;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        (new InvoiceSetup($this->date1, $this->date2))
            ->store((string) $this->path . "setup" . '.xlsx', 'public');
    }
}
