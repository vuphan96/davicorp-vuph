<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SCart\Core\Admin\Controllers\RootAdminController;

class ImportPriceboardSheet implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $sheet;

    public function __construct($sheet)
    {
        $this->sheet = $sheet;
    }

    public function view(): View
    {
        return view((new RootAdminController)->templatePathAdmin . 'screen.import_prices_product.excel.export_price_template')
            ->with(['sheet' => $this->sheet]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:I' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:I1' => ['font' => ['bold' => true]]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 35,
            'C' => 20,
            'D' => 25,
            'E' => 20,
            'F' => 25,
            'G' => 20,
            'H' => 25,
            'I' => 20
        ];
    }

    public function title(): string
    {
       return $this->sheet->code ?? 'Sheet-' . now();
    }
}
