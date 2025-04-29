<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeRateClass extends Model
{
    protected $guarded = [
        'id',
    ];

    public function rateClass()
    {
        return $this->belongsTo(RateClass::class, 'rate_class_id');
    }

    public function rateClassParent()
    {
        return $this->belongsTo(RateClassParent::class, 'rate_class_parent_id');
    }
}
