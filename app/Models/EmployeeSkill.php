<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeSkill extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    protected $attributes = [
        'notes' => 'N/A'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function jobSkill()
    {
        return $this->belongsTo(JobSkill::class);
    }
}
