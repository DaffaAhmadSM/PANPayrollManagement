<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceItemGroup implements FromView, ShouldAutoSize, WithTitle, WithStyles, ShouldQueue
{

    use Exportable, SerializesModels;

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

    public function view(): View
    {
        $data = $this->data;
        $tempTimesheet = $this->tempTimesheet;
        $customerData = $this->customerData;
        $prCode = $this->prCode;
        return view('excel.invoice.invoice-item-group', compact('data', 'tempTimesheet', 'customerData', 'prCode'));
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle("A:Z")->getFont()->setName("Times New Roman");
        $sheet->getStyle("A:Z")->getFont()->setSize(9);
        $sheet->setShowGridlines(false);

        $sheet->getParentOrThrow()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'size' => 9
            ]
        ]);
        $sheet->getSheetView()->setView('pageBreakPreview');
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
    }


    public function title(): string
    {
        return $this->title;
    }
}
