<?php

namespace App\Exports;

use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopSupplier;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminRevenueReportExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    public function __construct($dataSearch)
    {
        $this->dataSearch = $dataSearch;
    }

    // Excel export
    public function view(): View
    {
        $obj = new AdminOrder();

        if (!empty($this->dataSearch['department'])) {
            if ($this->dataSearch['department'] == 999) {
                $dataTmp = $obj->getListOrderDavicook($this->dataSearch);
            } else {
                $dataTmp = $obj->getRevenueReportOrder($this->dataSearch);
            }
        } else {
            $objOrderDavicorp = $obj->getRevenueReportOrder($this->dataSearch);
            $objOrderDavicook = $obj->getListOrderDavicook($this->dataSearch);
            $dataTmp = $objOrderDavicorp->mergeRecursive($objOrderDavicook);
        }

        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.report.revenue.export_excel_template')->with(['dataRevenueReportOrders' => $dataTmp, 'dataSearch' => $this->dataSearch]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 50,
            'F' => 20,
            'G' => 25
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::builtInFormatCode(3),
        ];
    }
}
