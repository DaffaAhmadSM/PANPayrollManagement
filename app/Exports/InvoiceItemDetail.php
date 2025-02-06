<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceItemDetail implements FromView, ShouldAutoSize, WithTitle
{

    protected $dataKronos;
    protected $dataNonKronos;
    protected $tempTimesheet;
    protected $customerData;
    protected $title = '1';

    public function __construct($dataKronos, $dataNonKronos, $tempTimesheet, $customerData, $title)
    {
        $this->dataKronos = $dataKronos;
        $this->dataNonKronos = $dataNonKronos;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->title = $title;
    }

    public function view() : View
    {   
        return view('excel.invoice.invoice-item-detail');
    }

    public function title(): string
    {
        return $this->title;
    }
    
}
