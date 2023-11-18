<?php

namespace App\Exports;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopSupplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminImportReportExportTemplate implements WithColumnFormatting, FromView, WithStyles, WithColumnWidths
{
    protected $dataSupplier;
    protected $dataSearch;
    public function __construct($dataSupplier, $dataSearch)
    {
        $this->dataSupplier = $dataSupplier;
        $this->dataSearch = $dataSearch;
    }
    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.export_excel_report_import_price_template_one')->with(['dataSupplier' => $this->dataSupplier,'dataSearch' => $this->dataSearch]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('C')->getAlignment()->setWrapText(true);
        $sheet->getStyle('G')->getAlignment()->setWrapText(true);
        return ['A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],];
    }
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, // stt
            'B' => 15, //mã
            'C' => 50, // tên
            'D' => 16, // số lượng
            'E' => 16, // đơn giá
            'F' => 16, // thành tiền
            'G' => 16// Ghi chú
        ];
    }
}
