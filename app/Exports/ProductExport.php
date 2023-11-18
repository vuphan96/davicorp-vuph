<?php

namespace App\Exports;

use App\Admin\Models\AdminProduct;
use App\Front\Models\ShopProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductExport implements FromView, WithStyles, WithColumnWidths
{
    use Exportable;
    private $filter;
    private $ids;
    private $option;
    // Excel export
    public function __construct(array $filter = [], array $ids = [], int $option = 0)
    {
        $this->filter = $filter ?? '';
        $this->ids = $ids ?? '';
        $this->option = $option ?? 0; // 0: Tất cả 1 Filter 2 slect
    }

    public function view(): View
    {
        $dataWarehouse = \App\Admin\Models\AdminWarehouse::all();
        $output = [];
        switch ($this->option){
            case 0:
                $output = ShopProduct::with('unit', 'category','warehouse');
                if(!empty($this->filter['category_id'])){
                    $output = $output->where('category_id', $this->filter['category_id']);
                }
                if(!empty($this->filter['keyword'])){
                    $keyword = $this->filter['keyword'];
                    $output = $output->where('name', 'like', "%{$keyword}%");
                }
                $output = $output->get();
                break;
            case 1:
                $output = ShopProduct::with('unit', 'category','warehouse')->whereIn('id', $this->ids)->get();
                break;
        }
        return view((new \SCart\Core\Admin\Controllers\RootAdminController())->templatePathAdmin .
            'screen.product.excel.export_template')->with(['data' => $output,'dataWarehouse'=>$dataWarehouse]);
    }

    // Change excel style
    public function styles(Worksheet $sheet): array
    {
        return [
            'A:T' => ['font' => ['name' => 'Times New Roman', 'size' => '12']],
            // Styling a specific cell by coordinate.
            'A1:T1' => [

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
            'G' => 25,
            'H' => 17,
            'I' => 11,
            'J' => 15,
            'K' => 20,
            'L' => 27,
            'M' => 30,
            'N' => 30,
            'O' => 30,
            'P' => 20

        ];
    }
}
