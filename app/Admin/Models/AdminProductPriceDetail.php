<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use App\Front\Models\ShopProduct;
use DB;

class AdminProductPriceDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'admin_product_price_detail';
    public $tableDescription = SC_DB_PREFIX . 'shop_product_description';
    public $timestamps = true;
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    

    public function product(){
        return $this->belongsTo(ShopProduct::class,'product_id','id');
    }

    public function getProductPriceListAdmin(array $dataSearch,$idProductPrice=null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $productPriceList = AdminProductPriceDetail::with("product");
        if ($keyword) {
            $productPriceList = $productPriceList->whereHas("product", function ($query) use ($keyword){
               $query->where("name", "like", "%$keyword%");
            });
        }
        if($idProductPrice){
            $productPriceList = $productPriceList->where('product_price_id', $idProductPrice);
        }
        $productPriceList = $productPriceList->paginate(20);

        return $productPriceList;
    }
    public function getItems($id){
        return $this->where('product_price_id',$id)->get();
    }
    
}
