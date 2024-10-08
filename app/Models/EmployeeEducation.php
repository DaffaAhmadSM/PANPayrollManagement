<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEducation extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    protected $attributes = [
        'notes' => 'N/A',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function educationLevel(){
        return $this->belongsTo(EducationLevel::class);
    }
}
