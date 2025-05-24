<?php

namespace App\Exports;


use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use Maatwebsite\Excel\Concerns\FromCollection;

class InvoiceItemDetailDaily implements FromView, WithTitle, WithStyles, WithDrawings
{

    use Exportable, SerializesModels;
    protected $tempTimesheet;
    protected $title = '1';
    protected $days;
    protected $holiday;
    protected $date1;
    protected $date1end;
    protected $date2start;
    protected $date2;
    protected $data;

    protected $oracle_job_number;

    protected $count;


    public function __construct(Collection $data, $tempTimesheet, string $title, $days, $count, $oracle_job_number)
    {
        $this->data = $data;
        $this->tempTimesheet = $tempTimesheet;
        $this->title = $title;
        $this->days = $days;
        $this->count = $count;
        $this->oracle_job_number = $oracle_job_number;
    }
    // 22

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
        $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_TABLOID);
    }

    public function view(): View
    {
        $holiday = $this->holiday;
        $days = $this->days;
        $temptimesheet = $this->tempTimesheet;
        $oracle_job_number = $this->oracle_job_number;
        $count = $this->count;
        $dailyRates = $this->data;



        return view('excel.invoice.invoice-item-detail-daily', compact('dailyRates', 'days', 'temptimesheet', 'oracle_job_number', 'count'));
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('ttd');
        $drawing->setDescription('ttd');
        $drawing->setPath(public_path('/images/ttd.png'));
        $drawing->setWidth(85);
        $drawing->setHeight(85);

        $data_count = $this->data->count();

        $drawing->setCoordinates('Z' . $data_count + 18);

        return $drawing;
    }

    public function title(): string
    {
        return $this->title;
    }

}