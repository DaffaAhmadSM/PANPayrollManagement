<?php

namespace App\Http\Controllers;

use App\Models\TimeSheet;
use Illuminate\Http\Request;
use App\Models\TempTimeSheet;
use App\Models\TimeSheetLine;
use App\Models\tempTimesheetLine;
use Illuminate\Support\Facades\Storage;

class TimesheetController extends Controller
{
    public function list (Request $request) {
        $page = $request->perpage ?? 75;
        $list = TimeSheet::orderBy('id', 'desc')->with('user', 'attachments')->cursorPaginate($page, ['id', 'user_id', 'from_date', 'to_date', 'description', 'filename', 'random_string', 'status', 'file_path']);
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
        $timesheetLine = tempTimesheetLine::where('temp_timesheet_id', $timesheet->temp_timesheet_id)->with("overtimeTimesheet", "overtimeTimesheet.multiplicationSetup")->orderBy('no', 'asc')->cursorPaginate($page, ['id', 'no', 'date', 'basic_hours', 'actual_hours', 'deduction_hours', 'overtime_hours', 'total_overtime_hours', 'paid_hours', 'custom_id']);
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
        ], 200);
    }

    public function downloadTimesheet($random_string)
    {
        $timesheet = TimeSheet::where('random_string', $random_string)->first();
        $path = $timesheet->file_path;
        return Storage::download($path);
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
