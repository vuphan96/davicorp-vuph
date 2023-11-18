<?php

namespace App\Exports\DavicorpOrder;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportOrderToEinvoice implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
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
            'screen.davicorp_order.excel.export_order_to_einvoice_template')->with(['data' => $this->data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:AE' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:AE1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // ngày
            'B' => 10, // Số HĐ
            'C' => 12, // Diễn giải
            'D' => 12, // Diễn giải
            'E' => 12, // Diễn giải
            'F' => 20, // Diễn giải
            'G' => 1, // Diễn giải
            'H' => 1, // Diễn giải
            'I' => 12, // Diễn giải
            'J' => 20, // Diễn giải
            'K' => 12, // Diễn giải
            'L' => 12, // Diễn giải
            'M' => 12, // Diễn giải
            'N' => 20, // Diễn giải
            'O' => 20, // Diễn giải
            'P' => 1, // Diễn giải
            'Q' => 1, // Mã sản phẩm
            'R' => 10, // tên mặt hàng
            'S' => 10, // ĐVt
            'T' => 10, // Số lương
            'U' => 10, // Gía bán
            'V' => 10, // Doanh thu
            'W' => 10, // Doanh thu
            'X' => 10, // Doanh thu
            'Y' => 10, // Doanh thu
            'Z' => 10, // Doanh thu
            'AA' => 10, // Doanh thu
            'AB' => 10, // Doanh thu
            'AC' => 10, // Doanh thu
            'AD' => 10, // Doanh thu
            'AE' => 10, // Doanh thu
        ];
    }
    public function columnFormats(): array
    {
        return [
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function title(): string
    {
        return $this->data->first()->name ?? '';
    }

}
