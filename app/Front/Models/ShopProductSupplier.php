<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopProductSupplier extends Model
{
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_product_supplier';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customer(){
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'id');
    }

    public function product(){
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function supplier(){
        return $this->belongsTo(ShopSupplier::class, 'supplier_id', 'id');
    }

    /**
     * @param $product_id
     * @param $customer_id
     * @return mixed
     */
    public static function getSupplierOfProductAndCustomer($product_id, $customer_id)
    {
        return ShopProductSupplier::where('product_id', $product_id)->where('customer_id', $customer_id)->first();
    }
}
