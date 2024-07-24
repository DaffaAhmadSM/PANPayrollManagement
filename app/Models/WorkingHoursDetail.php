<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHoursDetail extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function workingHours()
    {
        return $this->belongsTo(WorkingHour::class);
    }
}
