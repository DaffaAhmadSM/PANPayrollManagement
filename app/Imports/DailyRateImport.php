<?php

namespace App\Imports;

use App\Models\DailyRate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class DailyRateImport implements ToModel, WithChunkReading, WithCustomCsvSettings
{
    use Importable;
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        return new DailyRate([
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
            'delimiter' => ';'
        ];
    }
}
