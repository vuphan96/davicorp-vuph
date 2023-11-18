<?php

namespace App\Exports\Einvoice;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminMultipleSheetSalesInvoiceDetailVirtualOrder implements WithMultipleSheets
{
    use Exportable;
    protected $dataSearch;
    protected $dataCustomerIds;
    protected $department;

    /**
     * AdminMultipleSheetSalesInvoiceListVirtualOrder constructor.
     * @param $dataSearch
     * @param $dataCustomerIds
     */
    public function __construct($dataSearch, $dataCustomerIds, $department)
    {
        $this->dataSearch = $dataSearch;
        $this->dataCustomerIds = $dataCustomerIds;
        $this->department = $department;
    }

    // Excel export
    public function sheets(): array
    {

        $sheets = [];
        foreach ($this->dataCustomerIds as $key => $item) {
            $sheets[] = new AdminExportSalesInvoiceDetailVirtualOrder($this->dataSearch, $item, $this->department);
        }

        return $sheets;
    }

}