<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerTimesheetLine extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function customerTimesheet()
    {
        return $this->belongsTo(CustomerTimesheet::class);
    }

    public function overtimeCustomerTimesheet()
    {
        return $this->hasMany(CustomerTimesheetOvertime::class, "custom_id", "custom_id");
    }
}
