<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminProductPrice extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'admin_product_price';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    const UPDATED_AT = null;

    public function User(){
        return $this->belongsTo('SCart\Core\Admin\Models\AdminUser','user_id','id');
    }

    public static function getProductPriceListAdmin(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $productPriceList = new AdminProductPrice();
        if ($keyword) {
            $productPriceList = $productPriceList
                ->where('name', 'like', '%' . $keyword . '%');
                
        }
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $productPriceList = $productPriceList->orderBy($field , $sort_field);
        } else {
            $productPriceList = $productPriceList->orderBy('id', 'desc');
        }
        $productPriceList = $productPriceList->paginate(20);
        return $productPriceList;
    }
    
    public function getItem(){
        return $this->orderBy('id','DESC')->limit(1)->get();
    }

    public function productPriceDetails()
    {
        return $this->hasMany(AdminProductPriceDetail::class, 'product_price_id', 'id');
    }

    public function prices(){
        return $this->hasMany(AdminProductPriceDetail::class, 'product_price_id', 'id');
    }

    public function getProductPriceExcelList()
    {
        $objProductPrice = AdminProductPrice::with('prices')->get();
        return $objProductPrice;
    }
}
