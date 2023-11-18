<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RatingExport implements FromView, WithColumnWidths, WithStyles
{
    public $data;
    public $month;
    public $year;
    public function __construct($data, $monthYear)
    {
        $now = now();
        $this->data = $data;
        $inputMonth = explode("/", $monthYear);
        $month = $inputMonth[0] ?? $now->format("m");
        $year = $inputMonth[1] ?? $now->format("Y");
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'print.rating_export_pdf_templete')->with(['data' => $this->data, 'month' => $this->month, 'year' => $this->year]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A:E' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],

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
            "C" => 35,
            "D" => 20,
            "E" => 90,
        ];
    }
}
