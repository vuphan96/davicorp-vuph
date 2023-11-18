<?php

namespace App\Exports\Point;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminExportPoint implements FromView, WithColumnFormatting, WithColumnWidths, WithStyles
{
    public $data;
    public $time;
    public function __construct($data, $time)
    {
        $this->data = $data;
        $this->time = $time;
    }

    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.points.excel.export_point_template')->with(['data' => $this->data, "time" => $this->time]);
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            "F" => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            'A:I' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:I1' => ['font' => ['bold' => true]]
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            "A" => 7,
            "B" => 55,
            "C" => 20,
            "D" => 20,
            "E" => 20,
            "F" => 20,
            "G" => 20,
        ];
    }
}
