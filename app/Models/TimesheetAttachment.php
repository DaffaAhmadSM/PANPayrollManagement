<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimesheetAttachment extends Model
{
    protected $guarded = ["id"];

    public function timesheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }
}
