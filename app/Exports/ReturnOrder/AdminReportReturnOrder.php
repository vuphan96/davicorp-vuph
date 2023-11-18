<?php

namespace App\Exports\ReturnOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminReportReturnOrder implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $data;
    protected $date;

    /**
     * AdminExportOrderDavicookSheet constructor.
     * @param $data
     * @param $type
     */
    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.excel_template.report_order_return')->with(['order' => $this->data, 'date' => $this->date]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A1:G1' => ['font' => ['bold' => true]],
            'A:L' => [
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
            'A' => 12, // ngày
            'B' => 13, // Số HĐ
            'C' => 12, // Diễn giải
            'D' => 33, // Mã sản phẩm
            'E' => 12, // tên mặt hàng
            'F' => 12, // tên mặt hàng
            'G' => 33, // tên mặt hàng
            'H' => 10, // tên mặt hàng
            'I' => 12, // tên mặt hàng
            'J' => 12, // tên mặt hàng
            'K' => 15, // tên mặt hàng
            'L' => 15, // tên mặt hàng
        ];
    }
    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function title(): string
    {
        return 'Report Return Order';
    }

}
