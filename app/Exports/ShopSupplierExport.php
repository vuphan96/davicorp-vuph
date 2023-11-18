<?php

namespace App\Exports;

use App\Front\Models\ShopSupplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShopSupplierExport implements FromView, WithStyles, WithColumnWidths
{
    // Excel export
    public function view(): View
    {
        $data = (new ShopSupplier())->all();
        $data = json_decode((json_encode($data)), true);
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.supplier.excel.export_template')->with(['data' => $data]);
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
            'A' => 7,
            'B' => 20, //mã
            'C' => 25, // tên
            'D' => 25, // địa chỉ
            'E' => 25, // sđt
            'F' => 25, // email
            'G' => 15
        ];
    }
}
