<?php

namespace App\Exports;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;

class AdminExportOrderDavicookSheet implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    protected $data;
    protected $type;
    protected $date;

    /**
     * AdminExportOrderDavicookSheet constructor.
     * @param $data
     * @param $type
     * @param $date
     */
    public function __construct($data, $type, $date)
    {
        $this->data = $data;
        $this->type = $type;
        $this->date = $date;
    }

    // Excel export
    public function view(): View
    {
        if ($this->type == 'export_with_meal') {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.excel_template.export_order_davicook_with_meal')->with(['order' => $this->data]);
        }
        if ($this->type == 'export_combine_with_meal') {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.excel_template.export_combine_order_davicook_with_meal')->with(['order' => $this->data]);
        }
        if ($this->type == 'export_with_product') {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.excel_template.export_order_davicook_with_product')->with(['order' => $this->data]);
        }
        if ($this->type == 'export_combine_with_product') {
            return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
                'screen.excel_template.export_combine_order_davicook_with_product')->with(['order' => $this->data, 'date' => $this->date]);
        }
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => [
                'name' => 'Times New Roman',
                'size' => '12',
            ]],
            'A3:G3' => ['font' => [
                'size' => '14',
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7, // ngày
            'B' => 18, // Số HĐ
            'C' => 18, // Diễn giải
            'D' => 13, // Mã sản phẩm
            'E' => 30, // tên mặt hàng
        ];
    }
    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function title(): string
    {
        return 'Davicook';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $a = $event->sheet->getDelegate()->getStyle()
                                ->getFont()
                                ->getColor()
                                ->setName('DD4B39');
                dd($a);
            },
        ];
    }

}
