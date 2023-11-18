<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopDavicookProductSupplier extends Model
{
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_davicook_product_supplier';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customer(){
        return $this->belongsTo(ShopDavicookCustomer::class, 'customer_id', 'id');
    }

    public function product(){
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function supplier(){
        return $this->belongsTo(ShopSupplier::class, 'supplier_id', 'id');
    }

    public static function getSupplierOfProductAndCustomer($product_id, $customer_id)
    {
        return ShopDavicookProductSupplier::where('product_id', $product_id)->where('customer_id', $customer_id)->first();
    }
}
