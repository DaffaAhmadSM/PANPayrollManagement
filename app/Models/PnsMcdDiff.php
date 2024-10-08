<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PnsMcdDiff extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function tempTimeSheet()
    {
        return $this->belongsTo(TempTimeSheet::class);
    }
}
