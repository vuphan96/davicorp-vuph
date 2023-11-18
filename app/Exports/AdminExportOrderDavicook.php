<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportOrderDavicook implements WithMultipleSheets
{
    use Exportable;
    protected $data;
    protected $type;

    /**
     * AdminPrintOrderDavicorp constructor.
     * @param $data
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    // Excel export
    public function sheets(): array
    {
        if ($this->type == 'export_with_meal' || $this->type == 'export_with_product') {
            $sheets = [];
            foreach ($this->data as $item) {
                $sheets[] = new AdminExportOrderDavicookSheet($item, $this->type, null);
            }

            return $sheets;
        }
//        if ($this->type == 'export_with_product') {
//            $sheets = [];
//            foreach ($this->data as $item) {
//                $sheets[] = new AdminExportOrderDavicookSheet($item, $this->type);
//            }
//
//            return $sheets;
//        }
        if ($this->type == 'export_combine_with_meal') {
            $sheets = [];
            foreach ($this->data as $customers) {
                foreach ($customers as $billDates) {
                    foreach ($billDates as $explains) {
                        $sheets[] = new AdminExportOrderDavicookSheet($explains, $this->type, null);
                    }
                }
            }

            return $sheets;
        }
        if ($this->type == 'export_combine_with_product') {
            $sheets = [];
            foreach ($this->data as $customers) {
                foreach ($customers as $keyBillDate => $billDates) {
                    foreach ($billDates as $explains) {
                        $sheets[] = new AdminExportOrderDavicookSheet($explains, $this->type, $keyBillDate);
                    }
                }
            }

            return $sheets;
        }

    }

}
