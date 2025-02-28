<?php

namespace App\Exports;

use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PNSINVExportDaily implements WithMultipleSheets
{

    use Exportable, SerializesModels;

    protected $string_id;
    protected $chunk_data;

    public function __construct($string_id, $chunk_data)
    {
        $this->string_id = $string_id;
        $this->chunk_data = $chunk_data;
    }

    public function sheets(): array
    {
        $sheets = [];


        return $sheets;
    }
}
