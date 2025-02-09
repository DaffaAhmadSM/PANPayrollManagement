<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExportInvoice implements WithMultipleSheets
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

    public function sheets(): array
    {
        $sheets = [];
        $count = 1;

        $sheets[] = new ExportInvoiceData($this->dataKronos, $this->dataNonKronos, $this->tempTimesheet, $this->customerData);

        foreach ($this->dataKronos as $dataKey => $data) {
            foreach ($data as $key => $chunk) {
                $sheets[] = new InvoiceItemGroup($chunk, $this->tempTimesheet, $this->customerData, (string)$count, $dataKey);
                $count++; 
                
            }
        }

        return $sheets;
    }
}