<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DavicookCustomerImport implements ToModel, WithHeadingRow, WithColumnLimit, WithChunkReading
{
    public function model(array $row)
    {
        
    }

    public function headingRow(): int
    {
        return 2;
    }

    public function endColumn(): string
    {
        return 'M';
    }

    public function chunkSize(): int
    {
        return 700;
    }

}
