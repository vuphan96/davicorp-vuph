<?php

namespace App\Exports\DavicorpOrder;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportMultipleSheet implements WithMultipleSheets
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
        if ($this->type == 'print') {
            $sheets = [];
            foreach ($this->data as $item) {
                $sheets[] = new AdminExportSheet($item, $this->type);
            }

            return $sheets;
        }
        if ($this->type == 'print_combine') {
            $sheets = [];
            foreach ($this->data as $customer) {
                foreach ($customer as $billDate) {
                    foreach ($billDate as $object) {
                        foreach ($object as $explain) {
                            $sheets[] = new AdminExportSheet($explain, $this->type);
                        }
                    }
                }
            }

            return $sheets;
        }

    }

}
