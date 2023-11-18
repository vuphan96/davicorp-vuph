<?php

namespace App\Exports;

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

class AdminReportTargetExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $sorted;

    public function __construct($dataSearch, $sorted)
    {
        $this->dataSearch = $dataSearch;
        $this->sorted = $sorted;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.report.target.export_excel_template')->with(['data' => $this->sorted, 'dataSearch' => $this->dataSearch]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A10:E10' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7, // stt
            'B' => 15, //mã 
            'C' => 80, // tên
            'D' => 20, // số lượng
            'E' => 50 // Ghi chú
        ];
    }
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
