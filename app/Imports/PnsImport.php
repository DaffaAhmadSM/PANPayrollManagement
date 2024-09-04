<?php

namespace App\Imports;

use App\Models\TempPns;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PnsImport implements ToModel, WithChunkReading
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new TempPns([
            //
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
