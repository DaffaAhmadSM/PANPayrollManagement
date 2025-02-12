<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportInvoiceData implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $dataKronos;
    protected $dataNonKronos;
    protected $tempTimesheet;
    protected $customerData;

    public function __construct($dataKronos, $dataNonKronos, $tempTimesheet, $customerData)
    {
        $this->dataKronos = $dataKronos;
        $this->dataNonKronos = $dataNonKronos;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
    }

    public function view(): View
    {

        $dataKronos = $this->dataKronos;
        $dataNonKronos = $this->dataNonKronos;
        $customerData = $this->customerData;
        $tempTimesheet = $this->tempTimesheet;

        return view('excel.invoice', compact('dataKronos', 'dataNonKronos', 'customerData', 'tempTimesheet'));
    }

    public function title(): string
    {
        $date = Carbon::parse($this->tempTimesheet->from_date)->format('M') . ' ' . Carbon::parse($this->tempTimesheet->from_date)->format('Y');
        return 'Summary Invoice ' . $date;
    }
}

