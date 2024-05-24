<?php

namespace App\Http\Controllers\Calendar;

use Illuminate\Http\Request;
use App\Models\CalendarHoliday;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CalendarHolidayController extends Controller
{
    function create (Request $request) {
        $validate = Validator::make($request->all(), [
            "code" => "required|string",
            "date" => "required|date",
            "remark" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        CalendarHoliday::create([
            "code" => $request->code,
            "date" => $request->date,
            "remark" => $request->remark,
        ]);

        return response()->json([
            "message" => "Calendar holiday created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "date" => "date",
            "remark" => "string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $calendarHoliday = CalendarHoliday::find($id);
        if (!$calendarHoliday) {
            return response()->json([
                "message" => "Calendar holiday not found"
            ], 404);
        }

        $calendarHoliday->update($request->all());

        return response()->json([
            "message" => "Calendar holiday updated"
        ], 200);
    }

    function delete ($id) {
        $calendarHoliday = CalendarHoliday::find($id);
        if (!$calendarHoliday) {
            return response()->json([
                "message" => "Calendar holiday not found"
            ], 404);
        }

        $calendarHoliday->delete();

        return response()->json([
            "message" => "Calendar holiday deleted"
        ], 200);
    }

    function detail ($id) {
        $calendarHoliday = CalendarHoliday::find($id);
        if (!$calendarHoliday) {
            return response()->json([
                "message" => "Calendar holiday not found"
            ], 404);
        }

        return response()->json([
            "data" => $calendarHoliday
        ], 200);
    }

    function getList () {
        $calendarHoliday = CalendarHoliday::all();

        return response()->json([
            "data" => $calendarHoliday
        ], 200);
    }
}
