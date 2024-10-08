<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProjectExperience extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    protected $attribute = [
        "homepage" => "N/A",
        "phone" => "N/A",
        "location" => "N/A",
        "notes" => "N/A",
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
