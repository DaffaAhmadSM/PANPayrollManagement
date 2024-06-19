<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function workingHour()
    {
        return $this->belongsTo(WorkingHour::class);
    }

    public function numberSequence()
    {
        return $this->belongsTo(NumberSequence::class);
    }
}
