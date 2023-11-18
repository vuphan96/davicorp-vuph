<?php
namespace App\Exports\Einvoice;

use App\Admin\Models\AdminDavicookOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminEInvoiceReportPaymentOfferExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $data;
    protected $attributes;
    protected $keyPrintPaymentOfferByCash;
    protected $keyPrintPaymentOfferByTransfer;
    /**
     * AdminReportDavicookExport constructor.
     * @param $dataSearch
     */
    public function __construct($data, $attributes, $keyPrintPaymentOfferByCash, $keyPrintPaymentOfferByTransfer)
    {
        $this->data = $data;
        $this->attributes = $attributes;
        $this->keyPrintPaymentOfferByCash = $keyPrintPaymentOfferByCash;
        $this->keyPrintPaymentOfferByTransfer = $keyPrintPaymentOfferByTransfer;
    }

    // Excel export
    public function view(): View
    {
        $keyPrintPaymentOfferByCash = $this->keyPrintPaymentOfferByCash;
        $pathViews = isset($keyPrintPaymentOfferByCash) ? 'export_payment_offer_by_cash_report' : 'export_payment_offer_by_transfer_report' ;
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.e_invoice.excel.' . $pathViews)->with(['data' => $this->data, 'attributes' => $this->attributes]);
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
            'A' => 18, // stt
            'B' => 9, // Tên sp
            'C' => 13, // DVT
            'D' => 13, // Số lương
            'E' => 13, // giá bán
            'F' => 13,// Doanh thu
            'G' => 20,// Doanh thu
            'H' => 20,// Doanh thu
        ];
    }
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::builtInFormatCode(3),
            'C' => NumberFormat::builtInFormatCode(3),
            'D' => NumberFormat::builtInFormatCode(3),
        ];
    }
}