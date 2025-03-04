<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\DailyRate;
use App\Exports\InvoiceSetup;
use App\Models\TempTimeSheet;
use Illuminate\Bus\Batchable;
use App\Exports\ExportInvoiceData;
use App\Models\InvoiceTotalAmount;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Queue\Queueable;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PNSInvoiceSummary implements WithMultipleSheets
{
    use SerializesModels, Exportable;

    /**
     * Create a new job instance.
     */
    protected $dataKronos;
    protected $dataNonKronos;
    protected $tempTimesheet;
    protected $customerData;

    protected $date1;
    protected $date2;
    public function __construct($string_id, $date1, $date2)
    {

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
        $this->dataKronos = $dataKronos;
        $this->dataNonKronos = $dataNonKronos;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->date1 = $date1;
        $this->date2 = $date2;
    }

    /**
     * Execute the job.
     */
    public function sheets(): array
    {
        $sheets = [];
        $dataKronos = $this->dataKronos;
        $dataNonKronos = $this->dataNonKronos;
        $tempTimesheet = $this->tempTimesheet;
        $customerData = $this->customerData;
        $date1 = $this->date1;
        $date2 = $this->date2;
        $sheets[] = new InvoiceSetup($date1, $date2);
        $sheets[] = new ExportInvoiceData($dataKronos, $dataNonKronos, $tempTimesheet, $customerData);

        return $sheets;
    }
}
