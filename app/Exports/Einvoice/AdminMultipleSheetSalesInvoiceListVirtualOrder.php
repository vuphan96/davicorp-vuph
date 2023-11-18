<?php

namespace App\Exports\Einvoice;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminMultipleSheetSalesInvoiceListVirtualOrder implements WithMultipleSheets
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
    public function __construct($dataSearch, $dataCustomerIds, $orderNum, $department)
    {
        $this->dataSearch = $dataSearch;
        $this->dataCustomerIds = $dataCustomerIds;
        $this->order_num = $orderNum;
        $this->department = $department;
    }

    // Excel export
    public function sheets(): array
    {

        $sheets = [];
        foreach ($this->dataCustomerIds as $key => $item) {
            $sheets[] = new AdminExportSalesInvoiceListVirtualOrder($this->dataSearch, $item, $this->order_num, $this->department);
        }

        return $sheets;
    }

}