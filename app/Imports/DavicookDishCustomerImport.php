<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DavicookDishCustomerImport implements ToModel, WithHeadingRow
{
    protected $start;
    function __construct($start){
        $this->start = $start;
    }
    public function model(array $row)
    {
        
    }

    public function headingRow(): int
    {
        return $this->start;
    }
    
}
