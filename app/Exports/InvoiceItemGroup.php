<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\HasReferencesToOtherSheets;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooter;
use PhpOffice\PhpSpreadsheet\Worksheet\HeaderFooterDrawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class InvoiceItemGroup implements FromView, ShouldAutoSize, WithTitle, WithStyles, HasReferencesToOtherSheets, WithDrawings
{

    use Exportable, SerializesModels;

    protected $data;
    protected $tempTimesheet;
    protected $customerData;
    protected $title = '1';
    protected $prCode;

    protected $count;

    public function __construct($data, $tempTimesheet, $customerData, $title, $prCode, $count)
    {
        $this->data = $data;
        $this->tempTimesheet = $tempTimesheet;
        $this->customerData = $customerData;
        $this->title = $title;
        $this->prCode = $prCode;
        $this->count = $count;
    }

    public function view(): View
    {
        $data = $this->data;
        $tempTimesheet = $this->tempTimesheet;
        $customerData = $this->customerData;
        $prCode = $this->prCode;
        $count = $this->count;
        return view('excel.invoice.invoice-item-group', compact('data', 'tempTimesheet', 'customerData', 'prCode', 'count'));
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle("A:Z")->getFont()->setName("Times New Roman");
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
        $sheet->getPageSetup()->setPrintArea("A:J");
        $sheet->getPageSetup()->setHorizontalCentered(true);


        $sheet->getPageMargins()->setTop(1.25);
        $sheet->getPageMargins()->setRight(0);
        $sheet->getPageMargins()->setBottom(0.6);
        $sheet->getPageMargins()->setLeft(0);
        $sheet->getPageMargins()->setFooter(0.3);

        $drawing = new HeaderFooterDrawing();
        $drawing->setName('kop surat');
        $drawing->setPath(public_path('images/kop_surat.jpg'));
        $drawing->setWidth(690);

        $sheet->getHeaderFooter()->addImage($drawing, HeaderFooter::IMAGE_HEADER_CENTER);
        $sheet->getHeaderFooter()->setOddHeader('&C&G');

    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('ttd');
        $drawing->setDescription('ttd');
        $drawing->setPath(public_path('/images/ttd.png'));
        $drawing->setWidth(80);
        $drawing->setHeight(80);

        $drawing->setCoordinates('I39');

        return $drawing;
    }


    public function title(): string
    {
        return $this->title;
    }
}
