<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResponsibility extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $attributes = [
        'description' => 'N/A'
    ];
}
