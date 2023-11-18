<?php

namespace App\Exports;

use App\Admin\Models\AdminCustomer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerExport implements FromView, WithColumnWidths, WithStyles
{
    private $data;

    // Excel export
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.davicorp_customer.excel.export_template')->with(['data' => $this->data]);
    }

    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            // Styling a specific cell by coordinate.
            'A:V' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:V1' => ['font' => ['bold' => true]]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 12,
            'D' => 12,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 10,
            'O' => 25,
            'P' => 15,
            'Q' => 15,
            'R' => 12,
            'S' => 25,
            'T' => 30,
            'U' => 20,
            'V' => 30,
        ];
    }
}
