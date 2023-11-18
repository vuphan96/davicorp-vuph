<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SCart\Core\Admin\Controllers\RootAdminController;

class UserPriceboardSheet implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $sheet;

    public function __construct($sheet)
    {
        $this->sheet = $sheet;
    }

    public function view(): View
    {
        return view((new RootAdminController)->templatePathAdmin . 'screen.user_priceboard_export_excel_templete')
            ->with(['sheet' => $this->sheet]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:C' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:C7' => ['font' => ['bold' => true]]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 35,
        ];
    }

    public function title(): string
    {
       return $this->sheet->priceboard_code ?? 'Sheet-' . now();
    }
}
