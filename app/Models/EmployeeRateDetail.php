<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRateDetail extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
        ];
    }

    public function employeeRate(): BelongsTo
    {
        return $this->belongsTo(EmployeeRate::class);
    }
}
