<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDavicookCustomerExportMulti implements WithMultipleSheets
{
    use Exportable;
    protected $data;

    /**
     * AdminMultipleSheetSalesInvoiceListRealOrder constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data= $data;
    }

    public function sheets(): array
    {
        $sheets = [];
        foreach ($this->data as $key => $item) {
            $sheets[] = new AdminDavicookCustomerExport($item);
        }

        return $sheets;
    }

}