<?php

namespace App\Exports\ImportPrice;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use SCart\Core\Admin\Controllers\RootAdminController;

class ExportNotification implements FromView, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view((new RootAdminController)->templatePathAdmin . 'screen.import_prices_product.excel.export_notification_template')
            ->with(['data' => $this->data]);
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
