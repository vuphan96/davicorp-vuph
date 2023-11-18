<?php

namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;

class AdminProductExchange extends Model
{
    public $table = SC_DB_PREFIX . 'shop_product_exchange';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    const STATUS = [
        0 => 'OFF',
        1 => 'ON',
    ];

    public static function getList($dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $status = $dataSearch['status'] ?? '';
        $tableProductExchange = (new AdminProductExchange())->table;
        $tableProductName = (new ShopProduct())->table;
        $tableUnitName = (new AdminUnit())->table;
        $productExchangeList = AdminProductExchange::leftJoin($tableProductName. " as sp", function ($join) use ($tableProductExchange) {
            $join->on("sp.id", $tableProductExchange . ".product_id");
        })->leftJoin($tableProductName. " as spx", function ($join) use ($tableProductExchange) {
            $join->on("spx.id", $tableProductExchange . ".product_exchange_id");
        })
        ->leftJoin($tableUnitName. " as au", function ($join) {
            $join->on("sp.unit_id", "au.id");
        })
        ->leftJoin($tableUnitName. " as aux", function ($join) {
            $join->on("spx.unit_id", "aux.id");
        });

        if ($keyword != '') {
            $productExchangeList = $productExchangeList->where(function ($q) use ($keyword){
                $q->where('spx.name', 'like', '%' . $keyword . '%');
                $q->orWhere('sp.name', 'like', '%' . $keyword . '%');
                $q->orWhere('spx.sku', 'like', '%' . $keyword . '%');
                $q->orWhere('sp.sku', 'like', '%' . $keyword . '%');
            });
        }

        if ($status != '') {
            $productExchangeList = $productExchangeList->where($tableProductExchange.'.status', $status);
        }

        $productExchangeList = $productExchangeList->select(
            'sp.name as product_name',
            'sp.sku as product_code',
            'spx.sku as product_code_exchange',
            'spx.name as product_name_exchange',
            'au.name as unit_name',
            'aux.name as unit_name_exchange',
            $tableProductExchange.'.status',
            $tableProductExchange.'.id',
            $tableProductExchange.'.product_id',
            $tableProductExchange.'.product_exchange_id',
            $tableProductExchange.'.qty_exchange'
        )->orderBy('id','desc');

        return $productExchangeList;
    }
}
