<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class, 'employment_type_id');
    }
}
