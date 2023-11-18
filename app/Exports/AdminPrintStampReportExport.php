<?php

namespace App\Exports;

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

class AdminPrintStampReportExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;
    public function __construct($data, $dataSearch)
    {
        $this->dataSearch = $dataSearch;
        $this->data = $data;
    }


    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.stamp_report_export_excel_template')->with(['data' => $this->data, 'dataSearch' => $this->dataSearch]);
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
            'A' => 15,
            'B' => 30,
            'C' => 30,
            'D' => 8,
            'E' => 12,
            'F' => 12,
            'G' => 17
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::builtInFormatCode(3),
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
