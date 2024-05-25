<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeMultiplicationSetup extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function calculation()
    {
        return $this->belongsTo(MultiplicationCalculation::class, 'multiplication_calc_id');
    }
}
