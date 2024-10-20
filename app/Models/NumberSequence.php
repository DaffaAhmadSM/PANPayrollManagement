<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberSequence extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


   static public function generateNumber($code)
    {
        $numberSequence = self::where('code', $code)->first();

        if ($numberSequence->starting_number > $numberSequence->current_number) {
            $numberSequence->current_number = $numberSequence->starting_number;
        }

        $numberSequence->current_number++;
        $numberSequence->save();

        $digits = strlen((string) $numberSequence->ending_number);
        $prefix = $numberSequence->prefix ? $numberSequence->prefix : '';
        $generated_number = $prefix . str_pad($numberSequence->current_number, $digits, '0', STR_PAD_LEFT);

        return $generated_number;
    }
}
