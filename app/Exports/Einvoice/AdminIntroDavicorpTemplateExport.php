<?php

namespace App\Exports\Einvoice;

use App\Front\Models\ShopEInvoiceDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminIntroDavicorpTemplateExport implements FromView, WithStyles, WithColumnWidths
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.e_invoice.excel.export_intro_report')->with(['data' => $this->data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return ['A:F' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 20,
            'C' => 10,
            'D' => 17,
            'E' => 20,
            'F' => 20
        ];
    }
}
