<?php

namespace App\Exports\DavicookMenuCard;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportMenuCardSheetForStudent implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;

    /**
     * AdminExportSalesInvoiceListRealOrder constructor.
     * @param $dataSearch
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
            'screen.davicook_menu_card.excel.for_student_template')->with(['data' => $this->data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:E' => [
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => '12',
                ]
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 23, // ngày
            'B' => 28, // Số HĐ
            'C' => 40, // Diễn giải
            'D' => 40, // Mã sản phẩm
            'E' => 25, // tên mặt hàng
        ];
    }
    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::builtInFormatCode(3),
            'I' => NumberFormat::builtInFormatCode(3),
        ];
    }

    public function title(): string
    {
        return 'Học sinh';
    }

}
