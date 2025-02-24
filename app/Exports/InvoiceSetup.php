<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class InvoiceSetup implements FromView, WithTitle, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): \Illuminate\View\View
    {
        return view('excel.invoice.invoice-setup');
    }

    public function title(): string
    {
        return 'Setup';
    }
}
