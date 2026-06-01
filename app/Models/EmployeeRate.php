<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeRate extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'from_date' => 'datetime',
            'to_date' => 'datetime',
        ];
    }

    public function details(): HasMany
    {
        return $this->hasMany(EmployeeRateDetail::class);
    }
}
