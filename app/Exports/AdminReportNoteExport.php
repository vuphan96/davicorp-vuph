<?php

namespace App\Exports;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use App\Front\Models\ShopSupplier;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminReportNoteExport implements FromView, WithStyles, WithColumnWidths
{
    protected $dataSearch;
    public function __construct($dataSearch)
    {
        $this->dataSearch = $dataSearch;
    }

    // Excel export
    public function view(): View
    {
        $dataTmp = null;
        $dataSearch = $this->dataSearch;

        if (!empty($dataSearch['department'])) {
            if ($dataSearch['department'] == 2) {
                $dataTmp = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            } else {
                $dataTmp = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            }
        } else {
            $objOrderDavicorp = (new AdminOrder())->getNoteReportOrder($dataSearch)->get();
            $objOrderDavicook = (new AdminDavicookOrder())->getNoteReportOrder($dataSearch)->get();
            $dataTmp = $objOrderDavicorp->mergeRecursive($objOrderDavicook);
        }

        $data = [];
        foreach ($dataTmp as $key => $datum) {
            $j = 0;
            foreach ($datum->details as $keyItem => $value) {
                if(!empty($value->comment)) {
                    $j++;
                }
            }
            if(!empty($datum->comment) || $j > 0) {
                $data[] = $datum;
            }
        }

        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.note_report_export_excel_templete')->with(['dataNoteReportOrders' => $data, 'dataSearch' => $this->dataSearch]);
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
            'A' => 7,
            'B' => 50,
            'C' => 50,
            'D' => 50,
            'E' => 50
        ];
    }
}
