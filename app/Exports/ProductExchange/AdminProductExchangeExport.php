<?php

namespace App\Exports\ProductExchange;

use App\Admin\Models\AdminProduct;
use App\Front\Models\ShopProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminProductExchangeExport implements FromView, WithStyles, WithColumnWidths
{
    use Exportable;
    private $data;
    // Excel export
    public function __construct($data)
    {
        $this->data = $data ?? [];
    }

    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.product_exchange.excel.export_template')->with(['data' => $this->data]);
    }

    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            'A:P' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            // Styling a specific cell by coordinate.
            'A1:P1' => [

                'font' => ['bold' => true],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 18,
            'D' => 25,
            'E' => 25,
            'F' => 20,
        ];
    }
}
