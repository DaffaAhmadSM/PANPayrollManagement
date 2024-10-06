<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSheetLine extends Model
{
    use HasFactory;

    protected $guarded = [];
    

    public function timesheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function overtimeTimesheet()
    {
        return $this->hasMany(TimeSheetOvertime::class, "custom_id", "custom_id");
    }
}
