<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tempTimesheetLine extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function overtimeTimesheet()
    {
        return $this->hasMany(tempTimeSheetOvertime::class, "custom_id", "custom_id");
    }
}
