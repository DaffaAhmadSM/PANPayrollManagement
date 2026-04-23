<?php

namespace App\Imports;

use App\Models\TempMcd;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class McdImport implements ToModel, WithChunkReading, WithCustomCsvSettings
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TempMcd([
            //
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ';',
            'input_encoding' => 'UTF-8',
        ];
    }
}
