<?php

namespace App\Exports\Warehouse\Report\ReportWarehouseCard;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopSupplier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelWarehouseCardExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;

    public function __construct($dataSearch, $data, $qtyStockBegin)
    {
        $this->dataSearch = $dataSearch;
        $this->qtyStockBegin = $qtyStockBegin;
        $this->data = $data;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.warehouse.warehouse_card.exportExcelTemplate')->with(['data' => $this->data, 'dataSearch' => $this->dataSearch, 'qtyStockBegin'=>$this->qtyStockBegin]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:I' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:H1' => ['font' => ['bold' => true]],
            'A3:I3' => ['font' => ['bold' => true]],
            'A4:I4' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 15,
            'C' => 35,
            'D' => 15,
            'E' => 15,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 50,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
