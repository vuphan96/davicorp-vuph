<?php

namespace App\Exports\DavicorpOrder;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportSheet implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $data;
    protected $type;

    /**
     * AdminExportSalesInvoiceListRealOrder constructor.
     * @param $data
     */
    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    // Excel export
    public function view(): View
    {
        if ($this->type == 'print') {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.davicorp_order.excel.export_order_template')->with(['order' => $this->data]);
        } else {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.davicorp_order.excel.export_combine_order_template')->with(['order' => $this->data]);
        }

    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '10']],
            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 3.88, // ngày
            'B' => 6.31, // Số HĐ
            'C' => 17.03, // Diễn giải
            'D' => 4.03, // Mã sản phẩm
            'E' => 7.74, // tên mặt hàng
            'F' => 8.17, // ĐVt
            'G' => 14.45, // ĐVt
        ];
    }
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::builtInFormatCode(3),
            'G' => NumberFormat::builtInFormatCode(3),
        ];
    }

    public function title(): string
    {
        return $this->data->id_name ?? '';
    }

}
