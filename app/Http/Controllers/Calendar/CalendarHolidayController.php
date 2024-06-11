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
            "date" => "required|date:Y-m-d",
            "remarks" => "required|string",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        // parse date using carbon
        $date = \Carbon\Carbon::parse($request->date)->format('Y-m-d');

        CalendarHoliday::create([
            "code" => $request->code,
            "date" => $date,
            "remarks" => $request->remarks,
        ]);

        return response()->json([
            "message" => "Calendar holiday created"
        ], 201);
    }

    function update (Request $request, $id) {
        $validate = Validator::make($request->all(), [
            "code" => "string",
            "date" => "date",
            "remarks" => "string",
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

        if ($request->date) {
            $date = \Carbon\Carbon::parse($request->date)->format('Y-m-d');
            $request->merge(['date' => $date]);
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
            "message" => "Success get calendar holiday detail",
            "data" => $calendarHoliday
        ], 200);
    }

    function getList () {
        $validate = Validator::make(request()->all(), [
            "perpage" => "integer",
        ]);

        if ($validate->fails()) {
            return response()->json([
                "message" => $validate->errors()
            ], 400);
        }

        $page = request()->perpage ? request()->perpage : 20;

        $calendarHoliday = CalendarHoliday::cursorPaginate($page, ['id', 'code', 'date', 'remarks']);

        return response()->json([
            'message' => 'Success get calendar holiday list',
            'header' => ['code', 'date', 'remarks'],
            "data" => $calendarHoliday
        ], 200);
    }
}
