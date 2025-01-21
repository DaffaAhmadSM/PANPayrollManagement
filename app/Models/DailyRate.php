<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRate extends Model
{
    protected $guarded = ["id"];

    public function DailyDetails()
    {
        return $this->hasMany(DailyRateDetail::class, 'daily_rate_string', 'string_id');
    }
}
