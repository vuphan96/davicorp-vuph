<?php

namespace App\Exports;

use App\Front\Models\ShopUserPriceboard;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ImportPriceboardExport implements WithMultipleSheets
{
    use Exportable;
    protected $sheetData;
    public $sheets = [];

    public function __construct($sheets)
    {
        $this->sheetData = $sheets;
    }

    public function sheets(): array
    {

        $sheets = [];
        foreach ($this->sheetData as $sheet){
            $sheets[] = new ImportPriceboardSheet($sheet);
        }
        return $sheets;
    }
}
