<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceItemGroup implements FromView, ShouldAutoSize, WithTitle
{

    use Exportable;

    protected $data;
    protected $tempTimesheet;
    protected $customerData;
    protected $title = '1';
    protected $prCode;

    public function __construct($data, $tempTimesheet, $customerData, $title, $prCode)
    {
        $this->data = $data;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->title = $title;
        $this->prCode = $prCode;
    }

    public function view() : View
    {   
        $data = $this->data;
        $tempTimesheet = $this->tempTimesheet;
        $customerData = $this->customerData;
        $prCode = $this->prCode;
        return view('excel.invoice.invoice-item-group', compact('data', 'tempTimesheet', 'customerData', 'prCode'));
    }


    public function title(): string
    {
        return $this->title;
    }
}
