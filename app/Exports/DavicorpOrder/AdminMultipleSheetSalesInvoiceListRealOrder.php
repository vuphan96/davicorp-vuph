<?php

namespace App\Exports\DavicorpOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminMultipleSheetSalesInvoiceListRealOrder implements WithMultipleSheets
{
    use Exportable;
    protected $dataSearch;
    protected $dataCustomerIds;

    /**
     * AdminMultipleSheetSalesInvoiceListRealOrder constructor.
     * @param $dataSearch
     * @param $dataCustomerIds
     */
    public function __construct($dataSearch, $dataCustomerIds)
    {
        $this->dataSearch = $dataSearch;
        $this->dataCustomerIds = $dataCustomerIds;
    }

    // Excel export
    public function sheets(): array
    {

        $sheets = [];
        foreach ($this->dataCustomerIds as $key => $item) {
            $sheets[] = new AdminExportSalesInvoiceListRealOrder($this->dataSearch, $item);
        }

        return $sheets;
    }

}
