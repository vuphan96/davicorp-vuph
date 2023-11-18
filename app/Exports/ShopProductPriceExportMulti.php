<?php

namespace App\Exports;

use App\Admin\Models\AdminProductPrice;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShopProductPriceExportMulti implements WithMultipleSheets
{
    // WithMultipleSheets,
    use Exportable;
    protected $ids;

    function __construct($ids){
        $this->ids = $ids;
    }

    // Excel export
    public function sheets(): array
    {   
        $arrId =  $this->ids;
        $sheets = [];
        if(!empty($arrId)){
            $data = (new AdminProductPrice())->getProductPriceExcelList()->whereIn('id',$arrId);
        } else{
            $data = (new AdminProductPrice())->getProductPriceExcelList();
        }
        foreach ($data as $key => $item) {
            $sheets[] = new ShopProductPriceExport($item);
        }
        return $sheets;
    }
}
