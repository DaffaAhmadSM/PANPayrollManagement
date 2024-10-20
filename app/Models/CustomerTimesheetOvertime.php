<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTimesheetOvertime extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function multiplicationSetup()
    {
        return $this->belongsTo(OvertimeMultiplicationSetup::class, "multiplication_id", "id");
    }
}
