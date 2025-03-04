<?php

namespace App\Jobs;

use App\Models\TempMcd;
use App\Models\Customer;
use App\Models\PositionRate;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Models\WorkingHoursDetail;
use Illuminate\Support\Facades\DB;
use App\Models\tempTimeSheetOvertime;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\OvertimeMultiplicationSetup;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Throwable;

class OvertimeCalcJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */

    protected $temptimesheet;
    protected $type;

    public function __construct($temptimesheet, $type)
    {
        $this->temptimesheet = $temptimesheet;
        $this->type = $type;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $temptimesheet = $this->temptimesheet;
        $type = $this->type;
        if ($type) {
            tempTimesheetLine::where('temp_timesheet_id', $temptimesheet->id)->delete();
            tempTimeSheetOvertime::where("random_string", $temptimesheet->random_string)->delete();
            $temptimesheet->update(['status' => 'recalculating']);
        } else {
            $temptimesheet->update(['status' => 'calculating']);
        }

        $mcd = TempMcd::where('temp_time_sheet_id', $temptimesheet->id)->lazy();
        // return $mcd;
        // $mcd Group by employee name and date
        $mcd = $mcd->groupBy(['leg_id', 'date'])->collapse();
        // return $mcd;
        $customer = Customer::find($temptimesheet->customer_id);
        $working_hour_detail = WorkingHoursDetail::where('working_hours_id', $customer->working_hour_id)->get();
        $calendar_holiday = CalendarHoliday::whereBetween('date', [$temptimesheet->from_date, $temptimesheet->to_date])->get();
        $overtime_multiplication_all = OvertimeMultiplicationSetup::with('calculation')->get();
        // $position_rate = PositionRate::all();

        try {
            DB::beginTransaction();
            $processedMcd = [];
            $timesheet_overtime = [];
            $create_position_rate = [];
            $count = 0;
            foreach ($mcd as $datemcd) {
                $date = Carbon::parse($datemcd->first()->date);
                $day = $date->dayName;
                $working_day = $working_hour_detail->firstWhere("day", $day);
                $working_day_hours = $working_day ? $working_day->hours : 0;
                $is_holiday = $calendar_holiday->firstWhere("date", $date->format('Y-m-d'));
                if ($date->dayOfWeek == 0) {
                    $is_holiday = true;
                }
                $holiday = $is_holiday ? true : false;
                $deduction_hour = 0;
                if (!$working_day || $holiday) {
                    $working_day_hours = 0;
                }

                if ($holiday) {
                    $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Holiday')->where('day', $day)
                        ->all();
                    if (!$overtime_multiplication) {
                        $overtime_multiplication = $overtime_multiplication_all
                            ->where('day_type', 'Holiday')
                            ->where('day', "all")->all();
                    }
                } else {
                    $overtime_multiplication = $overtime_multiplication_all
                        ->where('day_type', 'Normal')
                        ->where('day', $day)->all();
                    if (!$overtime_multiplication) {
                        $overtime_multiplication = $overtime_multiplication_all
                            ->where('day_type', 'Normal')
                            ->where('day', 'all')->all();
                    }
                }

                $Xremains = collect([]);
                foreach ($overtime_multiplication as $data) {
                    $Xremains->push([
                        'code' => $data->calculation->code,
                        'remaining' => $data->to_hours - $data->from_hours,
                    ]);
                }

                foreach ($datemcd as $item) {
                    $deduction_hour = $item->value < $working_day_hours ? $working_day_hours - $item->value : 0;
                    if ($working_day_hours < 0) {
                        $overtime_hour = $item->value;
                    } else {
                        $overtime_hour = $item->value > $working_day_hours ? $item->value - $working_day_hours : 0;
                    }
                    $total_overtime_hours = 0;
                    // return $total_actual_hour;


                    if ($overtime_hour > 0) {
                        $remaining = $overtime_hour;
                        if ($overtime_multiplication) {
                            foreach ($overtime_multiplication as $multiplication) {
                                $xrem = $Xremains->where('code', $multiplication->calculation->code)->first();

                                // $fromHour = $multiplication->from_hours;
                                // $toHour = $multiplication->to_hours;
                                $to = $xrem['remaining'];
                                $multiplier = $multiplication->calculation->multiplier;

                                if ($remaining <= 0) {
                                    break;
                                }
                                $range = min($to, $remaining);
                                $result = $range * $multiplier;
                                $remaining -= $range;
                                // update xremains
                                $Xremains = $Xremains->map(function ($x) use ($range, $multiplication) {
                                    if ($x['code'] == $multiplication->calculation->code) {
                                        $x['remaining'] -= $range;
                                    }
                                    return $x;
                                });
                                // $Xremains->whereIn('code', $multiplication->calculation->code)->first()['remaining'] -= $range;

                                // return $range. ' - ' . $remaining. ' - ' . $result . '-'. $from . ' - ' . $to;
                                $timesheet_overtime[] = [
                                    "random_string" => $temptimesheet->random_string,
                                    "multiplication_id" => $multiplication->id,
                                    "multiplication_code" => $multiplication->calculation->code,
                                    "hours" => $range,
                                    "total_hours" => $result,
                                    "custom_id" => $temptimesheet->random_string . '-' . $count
                                ];

                                $total_overtime_hours += $result;
                            }
                        } else {
                            $total_overtime_hours = $overtime_hour;
                        }
                    }
                    $processedMcd[] = [
                        'temp_timesheet_id' => $temptimesheet->id,
                        'no' => $item->leg_id,
                        'working_hours_id' => $customer->working_hour_id,
                        'kronos_job_number' => $item->kronos_job_number,
                        'parent_id' => $item->parent_id,
                        'oracle_job_number' => $item->oracle_job_number,
                        'employee_name' => $item->employee_name,
                        'job_dissipline' => $item->job_dissipline,
                        'leg_id' => $item->leg_id,
                        'slo_no' => $item->slo_no,
                        'date' => $item->date,
                        'basic_hours' => $working_day_hours <= 0 ? 0 : $working_day_hours,
                        'actual_hours' => $item->value,
                        'deduction_hours' => $deduction_hour,
                        'overtime_hours' => $overtime_hour,
                        'total_overtime_hours' => $total_overtime_hours,
                        'paid_hours' => $item->value + $total_overtime_hours - $overtime_hour,
                        'rate' => $item->rate,
                        'custom_id' => $temptimesheet->random_string . '-' . $count++
                    ];

                    if ($item->value > 0) {
                        $working_day_hours -= $item->value;
                    }
                }
            }


            $chunk = array_chunk($processedMcd, 1000);
            foreach ($chunk as $key => $value) {
                tempTimesheetLine::insert($value);
            }
            $timehseet_overtime_chunk = array_chunk($timesheet_overtime, 1000);
            foreach ($timehseet_overtime_chunk as $key => $value) {
                tempTimeSheetOvertime::insert($value);
            }

            PositionRate::insert($create_position_rate);

            $temptimesheet->update(['status' => 'calculated']);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->fail($th->getMessage());
        }
    }

    public function failed(?Throwable $exception)
    {
        Log::info('OvertimeCalc Failed: ' . $exception->getMessage());
    }
}
