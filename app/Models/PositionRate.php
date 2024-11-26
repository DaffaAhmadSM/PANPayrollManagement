<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'from_date',
        'to_date',
        'type',
        'rate',
        'meal_per_day'
    ];
}
