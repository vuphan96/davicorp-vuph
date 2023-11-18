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

class AdminExportSalesInvoiceListRealOrder implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;

    /**
     * AdminExportSalesInvoiceListRealOrder constructor.
     * @param $dataSearch
     * @param $data
     */
    public function __construct($dataSearch, $data)
    {
        $this->dataSearch = $dataSearch;
        $this->data = $data;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.davicorp_order.excel.export_order_list_sales_real')->with(['data' => $this->data , 'dataSearch' => $this->dataSearch]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:I' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:I1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // ngày
            'B' => 20, // Số HĐ
            'C' => 20, // Diễn giải
            'D' => 25, // Mã sản phẩm
            'E' => 50, // tên mặt hàng
            'F' => 10, // ĐVt
            'G' => 20, // Số lương
            'H' => 22, // Gía bán
            'I' => 27, // Doanh thu
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
        return $this->data->first()->name ?? '';
    }

}
