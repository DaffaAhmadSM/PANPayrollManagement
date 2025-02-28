<?php

namespace App\Exports;

use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PNSINVExportNKronos implements WithMultipleSheets
{

    use Exportable, SerializesModels;

    protected $string_id;

    public function __construct($string_id)
    {
        $this->string_id = $string_id;
    }

    public function sheets(): array
    {
        $sheets = [];


        return $sheets;
    }
}
