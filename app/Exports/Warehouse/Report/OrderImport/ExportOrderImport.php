<?php

namespace App\Exports\Warehouse\Report\OrderImport;

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

class ExportOrderImport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;
    protected $count;

    public function __construct($dataSearch, $data, $count)
    {
        $this->dataSearch = $dataSearch;
        $this->data = $data;
        $this->count = $count;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.warehouse.report.order_import.export_excel_template')->with(['data' => $this->data, 'dataSearch' => $this->dataSearch, 'count' => $this->count]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A10:H10' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7,
            'B' => 15,
            'C' => 50,
            'D' => 20,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 20,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
