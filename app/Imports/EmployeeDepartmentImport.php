<?php

namespace App\Imports;

use App\Models\EmployeeDepartment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithUpserts;

class EmployeeDepartmentImport implements
    ToModel,
    WithChunkReading,
    WithHeadingRow,
    WithBatchInserts,
    WithCustomCsvSettings,
    WithUpserts
{
    use Importable;
    public function model(array $row): EmployeeDepartment
    {
        return new EmployeeDepartment([
            "emp_id" => $row["emp_id"],
            "department" => $row["department"],
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

    public function getCsvSettings(): array
    {
        return [
            "delimiter" => ";",
        ];
    }

    public function uniqueBy()
    {
        return "emp_id";
    }
}
