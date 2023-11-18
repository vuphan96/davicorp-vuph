<?php

namespace App\Http\Models;

use App\Front\Models\ShopProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopProductDescription extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = SC_DB_PREFIX.'shop_product_description';
    protected $connection = SC_CONNECTION;


    public function product(){
        return $this->belongsTo(ShopProduct::class,'product_id','id');
    }
}
