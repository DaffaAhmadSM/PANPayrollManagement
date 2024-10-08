<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $attributes = [
        'description' => 'N/A',
        'note' => 'N/A'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
