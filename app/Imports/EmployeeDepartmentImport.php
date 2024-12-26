<?php

namespace App\Imports;

use App\Models\EmployeeDepartment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeeDepartmentImport implements ToModel, WithChunkReading, WithHeadingRow, WithBatchInserts
{
    use Importable;
    public function model(array $row)
    {
        return new EmployeeDepartment([
            'emp_id' => $row['emp_id'],
            'department' => $row['department'],
        ]);
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }
}
