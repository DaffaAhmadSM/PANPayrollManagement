<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PnsMcdDiff;

class TempTimeSheet extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function pnsMcdDiff() {
        return $this->hasMany(PnsMcdDiff::class, 'temp_time_sheet_id', 'id');   
    }
}
