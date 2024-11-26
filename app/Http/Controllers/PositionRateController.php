<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PositionRateController extends Controller
{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'position_id' => 'required',
            'rate' => 'required',
        ]);
    }

    
}
