<?php

namespace App\Jobs;

use App\Models\InvoiceExportPath;
use DateTime;
use DatePeriod;
use DateInterval;
use App\Models\Customer;
use App\Models\DailyRate;
use App\Models\EmployeeRate;
use App\Models\TempTimeSheet;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Carbon;
use App\Models\CalendarHoliday;
use App\Models\tempTimesheetLine;
use App\Exports\ExportInvoiceData;
use App\Models\EmployeeRateDetail;
use App\Models\InvoiceTotalAmount;
use App\Exports\PNSINVExportKronos;
use Illuminate\Support\Facades\Bus;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Container\Attributes\Storage;

class PNSInvoiceJobBatch implements ShouldQueue
{
    use Queueable, Batchable;

    protected $string_id;

    protected $filename;
    public function __construct($string_id, $filename)
    {
        $this->string_id = $string_id;
        $this->filename = $filename;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $string_id = $this->string_id;
        $filename = $this->filename;

        $tempTimesheet = TempTimeSheet::where('random_string', $string_id)->first();

        $customerData = Customer::where('id', $tempTimesheet->customer_id)->first();

        $dataKronos = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'not regexp', '^NK')
            ->lazy()->groupBy(['parent_id']);

        $dataKronos = $dataKronos->map(function ($item) {
            return $item->chunk(15);
        });

        $dataNonKronos = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'regexp', '^NK$')
            ->get()->groupBy(['parent_id']);

        $dataNonKronosPlus = InvoiceTotalAmount::where('random_string', $string_id)
            ->where('parent_id', 'regexp', '^NK-')
            ->get()->groupBy(['parent_id']);

        $dataNonKronosPlus = $dataNonKronosPlus->map(function ($item) {
            return $item->chunk(15);
        });

        $dataDailyRate = DailyRate::where('temptimesheet_string', $string_id)->get();

        $dataNonKronos = [
            "NK" => $dataNonKronos->collapse(),
            "NK-" => $dataNonKronosPlus,
            "Daily" => $dataDailyRate
        ];

        $date1 = Carbon::parse($tempTimesheet->from_date);
        $date1end = Carbon::parse($tempTimesheet->to_date)->subDays(15);
        // date2 end date - 15 days to get the start date
        $date2start = Carbon::parse($tempTimesheet->to_date)->subDays(14);
        $date2 = Carbon::parse($tempTimesheet->to_date);

        $holiday = CalendarHoliday::whereBetween('date', [$date1, $date2])->get();
        $period1 = new DatePeriod(
            new DateTime($date1),
            new DateInterval('P1D'),
            (new DateTime($date1end))->modify('+1 day')
        );

        $period2 = new DatePeriod(
            new DateTime($date2start),
            new DateInterval('P1D'),
            (new DateTime($date2))->modify('+1 day')
        );

        $days1 = [];
        foreach ($period1 as $date) {
            $isholiday = false;
            // check if day is sunday
            if ($date->format('w') == 0) {
                $isholiday = true;
            }
            // check if day is holiday
            $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
            if ($holidayCheck) {
                $isholiday = true;
            }


            $days1[] = [
                'date' => $date->format('M d'),
                'is_holiday' => $isholiday
            ];
        }


        $days2 = [];

        foreach ($period2 as $date) {
            $isholiday = false;
            // check if day is sunday
            if ($date->format('w') == 0) {
                $isholiday = true;
            }
            // check if day is holiday
            $holidayCheck = $holiday->firstWhere('date', $date->format('Y-m-d'));
            if ($holidayCheck) {
                $isholiday = true;
            }

            $days2[] = [
                'date' => $date->format('M d'),
                'is_holiday' => $isholiday
            ];
        }


        $employee_rates = EmployeeRate::where('random_string', $tempTimesheet->rate_id)->first();
        $employee_rate_details = EmployeeRateDetail::where('employee_rate_id', $employee_rates->id)->get();
        unset($employee_rates);
        $count = 1;
        $path = 'invoice/' . $filename . '/';

        $inv_path = InvoiceExportPath::where('invoice_string_id', $string_id)->first();
        if ($inv_path) {
            InvoiceExportPath::where('invoice_string_id', $string_id)->delete();
        }

        $batch = [];
        $batch[] = [
            new PNSInvoiceSummaryWrapper($string_id, $date1, $date2, $path),
            new PNSInvoiceAddPath("summary", $path . 'summary.xlsx', $string_id)
        ];
        foreach ($dataKronos as $dataKey => $group) {
            foreach ($group as $key => $data) {
                $batch[] = [
                    (new PNSINVJobWrapper($string_id, $data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $dataKey, $days1, $days2, $path, 'kronos')),
                    new PNSInvoiceAddPath('inv', $path . $count . '.xlsx', $string_id, $count)
                ];
                $count++;
            }
        }

        foreach ($dataNonKronos["NK-"] as $dataKey => $group) {
            foreach ($group as $key => $data) {
                $batch[] = [
                    (new PNSINVJobWrapper($string_id, $data, $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $dataKey, $days1, $days2, $path, 'NK-')),
                    new PNSInvoiceAddPath('inv', $path . $count . '.xlsx', $string_id, $count)
                ];
                $count++;
            }
        }
        foreach ($dataNonKronos["NK"] as $dataKey => $data) {
            $batch[] = [
                (new PNSINVJobWrapper($string_id, collect([$data]), $count, $tempTimesheet, $customerData, $date1, $date1end, $date2start, $date2, $employee_rate_details, $holiday, $dataKey, $days1, $days2, $path, 'NK-')),
                new PNSInvoiceAddPath('inv', $path . $count . '.xlsx', $string_id, $count)
            ];
            $count++;
        }
        Bus::batch($batch)->dispatch();
    }
}
