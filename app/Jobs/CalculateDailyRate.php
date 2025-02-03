<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CalculateDailyRate implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $daily_rate_data;
    public function __construct($daily_rate_data)
    {
        $this->daily_rate_data = $daily_rate_data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $daily_rate = $this->daily_rate_data;
    }
}
