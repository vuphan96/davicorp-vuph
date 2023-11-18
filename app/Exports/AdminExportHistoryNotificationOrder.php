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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportHistoryNotificationOrder implements FromView, WithStyles, WithColumnWidths
{
    protected $data;
    protected $date;

    /**
     * AdminExportOrderDavicookSheet constructor.
     * @param $data
     * @param $date
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
            'screen.notification.history.export')->with(['data' => $this->data, 'date' => $this->date]);

    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A3:G3' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5, // ngày
            'B' => 25, // Số HĐ
            'C' => 18, // Diễn giải
            'D' => 13, // Mã sản phẩm
            'E' => 45, // tên mặt hàng
            'F' => 90, // tên mặt hàng
        ];
    }

    public function title(): string
    {
        return 'History_Notification';
    }

}
