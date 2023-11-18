<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShopProductPriceExport implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $sheet;

    function __construct($sheetData)
    {
        $this->sheet = $sheetData;
    }

    public function view(): View
    {

        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.product_price_export_excel_templete')->with(['data' => $this->sheet]);

    }

    public function styles(Worksheet $sheet): array
    {

        return [
            'A:E' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:E1' => ['font' => ['bold' => true]],
            'A2:E2' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 7,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 20,
        );
    }

    public function title(): string
    {
        return $this->sheet->price_code ?? '';
    }
}
