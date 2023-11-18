<?php

namespace App\Exports\Einvoice;

use App\Admin\Models\AdminEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDetermineVolumeExport implements FromView, WithStyles, WithColumnWidths
{
    protected $dataImport;
    public function __construct($dataImport)
    {
        $this->dataImport = $dataImport;
    }

    // Excel export
    public function view(): View
    {
        $ids = $this->dataImport['id_invoice'];
        $arrID = explode(',', $ids);
        $objInvoices = AdminEInvoice::whereIn('id', $arrID)->get();
        $einvoiceIds = $objInvoices->pluck('id')->toArray();
        $objInvoiceDetails = (new ShopEInvoiceDetail())->getEinvoiceDetail($einvoiceIds);

        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.e_invoice.excel.export_determine_volume_report')->with(['data'=>$this->dataImport, 'objInvoiceDetails'=>$objInvoiceDetails]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A')->getAlignment()->setWrapText(true);
        return [
            'A:M' => ['font' => [
                'name' => 'Times New Roman',
            ]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 35,
            'C' => 7,
            'D' => 12,
            'E' => 12,
            'F' => 18,
        ];
    }
}
