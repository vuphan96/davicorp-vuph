<?php

namespace App\Exports\OrderImport;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportOrderImport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $data;

    /**
     * Constructor.
     * @param $item
     */
    public function __construct($item)
    {
        $this->data = $item;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.warehouse.import.excel.export_template')->with(['dataImport' => $this->data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '10']],
//            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 17,
            'D' => 10,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
        ];
    }
    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::builtInFormatCode(3),
            'G' => NumberFormat::builtInFormatCode(3),
        ];
    }

    public function title(): string
    {
        return $this->data->id_name ?? '';
    }

}
