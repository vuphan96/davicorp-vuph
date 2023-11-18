<?php

namespace App\Exports\Warehouse;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportWarehouseSheet implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $data;

    /**
     * AdminExportSalesInvoiceListRealOrder constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.warehouse.export.excel.export_template')->with(['data' => $this->data]);


    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('H')->getAlignment()->setWrapText(true);
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '10']],
//            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 3.88, // STT
            'B' => 12, // Mã sản phẩm
            'C' => 20, // Tên sản phẩm
            'D' => 6, // DVT
            'E' => 8, //
            'G' => 12, // Số lượng
            'H' => 16, // Ghi chú
        ];
    }
    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function title(): string
    {
        return $this->data->id_name ?? '';
    }

}
