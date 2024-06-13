<?php

namespace App\Http\Controllers\Calculation;

use App\Http\Controllers\Controller;
use App\Models\OvertimeMultiplicationSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class OvertimeMultiplicationSetupController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            'day_type' => 'required|in:Normal,Holiday',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'from_hours' => 'required|decimal:0,2',
            'to_hours' => 'required|decimal:0,2',
            'multiplication_calc_id' => 'required|exists:multiplication_calculations,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()->first()
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
            'from_hours' => 'decimal:0,2',
            'to_hours' => 'decimal:0,2',
            'multiplication_calc_id' => 'exists:multiplication_calculations,id'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validate->errors()->first()
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


    function detail ($id) {
        $setup = OvertimeMultiplicationSetup::with('calculation')->find($id);

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
        $setups = OvertimeMultiplicationSetup::with('calculation:id,code')->cursorPaginate(10, ['id', 'day_type', 'day', 'from_hours', 'to_hours', 'multiplication_calc_id']);

        $data = new Collection();
        // merge array
        foreach ($setups->items() as $setup) {
            $data->push([
                'day_type' => $setup->day_type,
                'day' => $setup->day,
                'from_hours' => $setup->from_hours,
                'to_hours' => $setup->to_hours,
                'multiplication_calc_code' => $setup->calculation->code,
                'id' => ["calc_id" => $setup->calculation->id, "main_id" => $setup->id]
            ]);
        }

        return response()->json([
            'message' => 'Overtime multiplication setups found',
            'header' => ['Day Type', 'Day', 'From Hours', 'To Hours', 'Code'],
            'data' => ["data" => $data],
            'next_page' => $setups->nextCursor(),
            'prev_page' => $setups->previousCursor(),
            'next_page_url' => $setups->nextPageUrl(),
            'prev_page_url' => $setups->previousPageUrl()
        ]);
    }

    function search (Request $request) {
        $validate = Validator::make($request->all(), [
            'search' => 'required|string'
        ]);

        if ($validate->fails()) {
            return response()->json([
                'message' => $validate->errors()->first()
            ], 400);
        }

        $setups = OvertimeMultiplicationSetup::with('calculation:id,code')
            ->where('day_type', 'like', "%$request->search%")
            ->orWhere('day', 'like', "%$request->search%")
            ->cursorPaginate(10, ['id', 'day_type', 'day', 'from_hours', 'to_hours', 'multiplication_calc_id']);

        $data = new Collection();
        // merge array
        foreach ($setups->items() as $setup) {
            $data->push([
                'day_type' => $setup->day_type,
                'day' => $setup->day,
                'from_hours' => $setup->from_hours,
                'to_hours' => $setup->to_hours,
                'multiplication_calc_code' => $setup->calculation->code,
                'id' => ["calc_id" => $setup->calculation->id, "main_id" => $setup->id]
            ]);
        }

        return response()->json([
            'message' => 'Overtime multiplication setups found',
            'header' => ['Day Type', 'Day', 'From Hours', 'To Hours', 'Code'],
            'data' => ["data" => $data],
            'next_page' => $setups->nextCursor(),
            'prev_page' => $setups->previousCursor(),
            'next_page_url' => $setups->nextPageUrl(),
            'prev_page_url' => $setups->previousPageUrl()
        ]);
    }

}
