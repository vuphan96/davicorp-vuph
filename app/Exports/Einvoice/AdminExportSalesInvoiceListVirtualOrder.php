<?php

namespace App\Exports\Einvoice;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportSalesInvoiceListVirtualOrder implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;
    protected $data;
    protected $department;

    /**
     * AdminExportSalesInvoiceListVirtualOrder constructor.
     * @param $dataSearch
     * @param $data
     */
    public function __construct($dataSearch, $data, $order_num, $department)
    {
        $this->dataSearch = $dataSearch;
        $this->data = $data;
        $this->order_num = $order_num;
        $this->department = $department;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.e_invoice.excel.export_einvoice_list_virtual_report')->with(['data' => $this->data , 'dataSearch' => $this->dataSearch, 'order_num' => $this->order_num, 'department'=>$this->department]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Chứng từ,Ngày
            'B' => 20, // Mã hóa đơn
            'C' => 45, // Tên mặt hàng
            'D' => 10, // Đvt
            'E' => 13, // Số lượng
            'F' => 15, // Giá bán
            'G' => 15, // Doanh thu
        ];
    }

    public function title(): string
    {
        return $this->data->first()->name ?? '';
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