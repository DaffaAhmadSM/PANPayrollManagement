<?php

namespace App\Jobs;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQueueStatus implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $model;
    protected $status;
    public function __construct($model, $status=null)
    {
        $this->model = $model;
        $this->status = $status??'completed';
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->model->update([
            'status' => $this->status
        ]);
    }
}
