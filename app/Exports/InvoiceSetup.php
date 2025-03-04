<?php

namespace App\Exports;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InvoiceSetup implements FromView, WithTitle, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $date1;
    protected $date2;

    public function __construct(Carbon $date1, Carbon $date2)
    {
        $this->date1 = $date1;
        $this->date2 = $date2;
    }

    public function view(): \Illuminate\View\View
    {
        // $date1 = February 1, 2022
        $date1 = $this->date1->format("F d, Y");
        $date2 = $this->date2->format("F d, Y");

        $dateNow = Carbon::now()->format("d/m/Y");
        $period = (string) $date1 . " - " . (string) $date2;
        $invoiceYear = "/PNS-INV/" . $this->date2->format("/m/Y");
        return view('excel.invoice.invoice-setup', compact('dateNow', 'period', 'invoiceYear'));
    }

    public function title(): string
    {
        return 'Setup';
    }
}
