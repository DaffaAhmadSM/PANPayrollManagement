<?php

namespace App\Http\Controllers;

use App\Models\TimeSheet;
use App\Models\TimeSheetLine;
use Illuminate\Http\Request;

class TimesheetController extends Controller
{
    public function list (Request $request) {
        $page = $request->perpage ?? 75;
        $list = TimeSheet::orderBy('id', 'desc')->with('user')->cursorPaginate($page, ['id', 'user_id', 'from_date', 'to_date', 'description', 'filename', 'random_string', 'status']);
        return response()->json([
            'status' => 200,
            'data' => $list,
            'header' => ['Name',  'Creator','Code', 'From Date', 'To Date', 'Description','Status']
        ]);
    }

    public function all()
    {

    }

    public function detail($timesheet_str)
    {
        $timesheet = TimeSheet::where('random_string', $timesheet_str)->first();
        $page = $request->perpage ?? 70;
        $timesheetLine = TimeSheetLine::where('timesheet_id', $timesheet->id)->with("overtimeTimesheet", "overtimeTimesheet.multiplicationSetup")->orderBy('no', 'asc')->cursorPaginate($page, ['id', 'no', 'date', 'basic_hours', 'actual_hours', 'deduction_hours', 'overtime_hours', 'total_overtime_hours', 'paid_hours', 'custom_id']);
        return response()->json([
            'status' => 200,
            'data' => $timesheetLine,
            'header' => [
                'No_Leg',
                'Date',
                'Basic Hours',
                'Actual Hours',
                'Deduction Hours',
                'Overtime Hours',
                'Total Overtime Hours',
                'Paid Hours'
            ]
        ]);
    }

    public function update(Request $request, $id)
    {

    }

    public function delete($id)
    {

    }

    public function create(Request $request)
    {

    }

    public function search(Request $request)
    {

    }
}
