<?php

namespace App\Exports;

use App\Admin\Models\AdminDavicookOrder;
use App\Admin\Models\AdminOrder;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminReportMealDifferenceExport implements FromView, WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected $dataSearch;

    /**
     * AdminReportDavicookExport constructor.
     * @param $dataSearch
     */
    public function __construct($dataSearch)
    {
        $this->dataSearch = $dataSearch;
    }

    // Excel export
    public function view(): View
    {
        $dataAllOrderReportMealDifference = (new AdminDavicookOrder())->getAllOrderReportMealDifference($this->dataSearch);

        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.report.meal_difference.excel_template')->with(['dataProducts' => $dataAllOrderReportMealDifference, 'dataSearch' => $this->dataSearch]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {

        return [
            // Styling a specific cell by coordinate.
            'A:J' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:J1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 7, // stt
            'B' => 20, //mã
            'C' => 25, // tên
            'D' => 15, // DVT
            'E' => 22, // Số lương
            'F' => 22,// Giá trị
            'G' => 22, // Số lương
            'H' => 22, // Gía trị
            'I' => 22, // Số lượng
            'J' => 22, // Gía trị
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

}
