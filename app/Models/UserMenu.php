<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model
{
    use HasFactory;

    function user() {
        return $this->belongsTo(User::class);
    }

    function menu() {
        return $this->belongsTo(Menu::class);
    }
}
