<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExportInvoiceData implements FromView, ShouldAutoSize, WithTitle, WithStyles, HasReferencesToOtherSheets
{

    use Exportable, SerializesModels;
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

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle("A:Z")->getFont()->setName("Calibri");
        $sheet->getStyle("A:Z")->getFont()->setSize(10);

        $sheet->setShowGridlines(false);

        $sheet->getParentOrThrow()->getDefaultStyle()->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'size' => 10
            ]
        ]);

        $sheet->getSheetView()->setView('pageBreakPreview');
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setPrintArea("A:I");
        $sheet->getPageSetup()->setHorizontalCentered(true);


        $sheet->getPageMargins()->setTop(2);
        $sheet->getPageMargins()->setRight(0.32);
        $sheet->getPageMargins()->setBottom(0.4);
        $sheet->getPageMargins()->setLeft(0.32);

        $drawing = new HeaderFooterDrawing();
        $drawing->setName('kop surat');
        $drawing->setPath(public_path('images/kop_surat.jpg'));
        $drawing->setWidth(720);

        $sheet->getHeaderFooter()->addImage($drawing, HeaderFooter::IMAGE_HEADER_CENTER);
        $sheet->getHeaderFooter()->setOddHeader('&C&G');

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
        $date = Carbon::parse($this->tempTimesheet->from_date)->format('M Y');
        return 'Summary Invoice ' . $date;
        // return "test";
    }
}

