<?php

namespace App\Imports\ProductExchange;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithColumnLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportProductExchange implements ToModel, WithHeadingRow, WithColumnLimit
{
    public function model(array $row)
    {
        // TODO: Implement model() method.
    }
    public function headingRow(): int
    {
        return 1;
    }

    public function endColumn(): string
    {
        return 'F';
    }
}
