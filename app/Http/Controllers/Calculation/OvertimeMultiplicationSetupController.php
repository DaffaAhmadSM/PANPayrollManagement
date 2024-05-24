<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use App\Models\OvertimeMultiplicationSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OvertimeMultiplicationSetupController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            'day_type' => 'required|in:Normal,Holiday',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'from_hours' => 'required|decimal',
            'to_hours' => 'required|decimal',
            'multiplication_calculation_id' => 'required|exists:multiplication_calculations,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 400);
        }

        OvertimeMultiplicationSetup::create($request->all());

        return response()->json([
            'message' => 'Overtime multiplication setup created'
        ]);

    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            'day_type' => 'in:Normal,Holiday',
            'day' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'from_hours' => 'decimal',
            'to_hours' => 'decimal',
            'multiplication_calculation_id' => 'exists:multiplication_calculations,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()
            ], 400);
        }

        $setup = OvertimeMultiplicationSetup::find($id);

        if (!$setup) {
            return response()->json([
                'message' => 'Overtime multiplication setup not found'
            ], 404);
        }

        $setup->update($request->all());

        return response()->json([
            'message' => 'Overtime multiplication setup updated'
        ]);
    }

    function delete ($id) {
        $setup = OvertimeMultiplicationSetup::find($id);

        if (!$setup) {
            return response()->json([
                'message' => 'Overtime multiplication setup not found'
            ], 404);
        }

        $setup->delete();

        return response()->json([
            'message' => 'Overtime multiplication setup deleted'
        ]);
    }

    function get ($id) {
        $setup = OvertimeMultiplicationSetup::find($id);

        if (!$setup) {
            return response()->json([
                'message' => 'Overtime multiplication setup not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Overtime multiplication setup found',
            'data' => $setup
        ]);
    }

    function list () {
        $setups = OvertimeMultiplicationSetup::all();

        return response()->json([
            'message' => 'Overtime multiplication setups found',
            'data' => $setups
        ]);
    }

}
