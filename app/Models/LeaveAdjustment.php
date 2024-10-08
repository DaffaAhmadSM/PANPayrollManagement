<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAdjustment extends Model
{
    use HasFactory;

    protected $table = 'leave_adjustments';

    protected $guarded = ['id'];

    protected $attributes = [
        'remark' => 'N/A'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveCategory()
    {
        return $this->belongsTo(LeaveCategory::class);
    }
}
