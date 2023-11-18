<?php
namespace App\Exports\Einvoice;

use App\Admin\Models\AdminDavicookOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminEInvoiceReportAcceptanceExport implements FromView, WithStyles, WithColumnWidths
{
    protected $data;
    protected $attributes;
    /**
     * AdminReportDavicookExport constructor.
     * @param $dataSearch
     */
    public function __construct($data, $attributes)
    {
        $this->data = $data;
        $this->attributes = $attributes;
    }

    // Excel export
    public function view(): View
    {
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.e_invoice.excel.export_acceptance_report')->with(['data' => $this->data, 'attributes' => $this->attributes]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:G' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:G1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 13, // stt
            'B' => 35, // Tên sp
            'C' => 13, // DVT
            'D' => 20, // Số lương
            'E' => 20, // giá bán
            'F' => 20,// Doanh thu
        ];
    }
}