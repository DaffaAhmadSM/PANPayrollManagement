<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];

    public function workingHourDetail(){
        return $this->hasMany(WorkingHoursDetail::class, 'working_hours_id', 'id');
    }
}
