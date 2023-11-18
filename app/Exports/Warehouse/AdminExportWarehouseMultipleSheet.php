<?php

namespace App\Exports\Warehouse;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportWarehouseMultipleSheet implements WithMultipleSheets
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
            $sheets[] = new AdminExportWarehouseSheet($item);
        }
        return $sheets;

    }

}
