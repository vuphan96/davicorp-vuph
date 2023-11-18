<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDavicookCustomerExport implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $sheetData;

    function __construct($sheetData)
    {
        $this->sheetData = $sheetData;
    }

    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.davicook_customer.excel.export_template')->with(['data' => $this->sheetData]);

    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('B')->getAlignment()->setWrapText(true);
        $sheet->getStyle('D')->getAlignment()->setWrapText(true);
        return [
            'A:M' => ['font' => [
                        'name' => 'Times New Roman',
                        'size' => '12',
                    ]],
        ];
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 20,
            'B' => 30,
            'C' => 25,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 25,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 15,
            'M' => 15

        );
    }

    public function title(): string
    {
        return $this->sheetData['customer_code'] ?? '';
    }
}
