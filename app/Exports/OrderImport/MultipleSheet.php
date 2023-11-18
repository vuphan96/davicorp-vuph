<?php

namespace App\Exports\OrderImport;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MultipleSheet implements WithMultipleSheets
{
    use Exportable;
    protected $data;

    /**
     * AdminPrintOrderDavicorp constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    // Excel export
    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $item) {
            $sheets[] = new AdminExportOrderImport($item);
        }

        return $sheets;

    }

}
