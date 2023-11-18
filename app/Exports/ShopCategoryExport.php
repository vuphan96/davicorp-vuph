<?php

namespace App\Exports;

use App\Front\Models\ShopCategory;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShopCategoryExport implements FromView, WithStyles, WithColumnWidths
{
    // Excel export
    public function view(): View
    {
        $data = (new ShopCategory())->getExcelShopCategory();
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.category.excel.export_template')->with(['data' => $data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            'A:D' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:I1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 35,
            'C' => 35,
            'D' => 10,
        ];
    }
}
