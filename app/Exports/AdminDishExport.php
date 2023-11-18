<?php

namespace App\Exports;

use App\Admin\Models\AdminDish;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDishExport implements FromView, WithStyles, WithColumnWidths
{
    protected $dataSearch;
    protected $ids;
    public function __construct($dataSearch, $ids)
    {
        $this->dataSearch = $dataSearch;
        $this->ids = $ids;
    }


    // Excel export
    public function view(): View
    {
        $data = (new AdminDish())->getListDish($this->dataSearch, $this->ids);
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.dish_export_excel_template')->with(['data' => $data]);
    }
    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            'A:C' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            'A1:C1' => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 30,
        ];
    }
}
