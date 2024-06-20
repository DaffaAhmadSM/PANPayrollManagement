<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function customerContract()
    {
        return $this->belongsTo(CustomerContract::class);
    }
}
