<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function education()
    {
        return $this->hasMany(EducationLevel::class);
    }

    public function classificationOfTaxPayer()
    {
        return $this->belongsTo(ClassificationOfTaxPayer::class);
    }

    public function employeeAddress()
    {
        return $this->hasOne(EmployeeAddress::class);
    }
}
