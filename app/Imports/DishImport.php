<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;


class DishImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {

    }

    public function headingRow(): int
    {
        return 1;
    }

}
