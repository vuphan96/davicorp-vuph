<?php
#S-Cart/Core/Front/Models/ShopOrderDetail.php
namespace App\Front\Models;

use App\Front\Models\ShopProduct;
use App\Front\Models\ShopDavicookOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShopDavicookDish extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    
    protected $table = SC_DB_PREFIX.'shop_davicook_dish';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $listDish = null;
    protected static $listDishs = null;


    public function getTable()
    {
        return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }
    public function order()
    {
        return $this->belongsTo(ShopDavicookOrder::class, 'order_id', 'id');
    }
    public function product()
    {
        return $this->hasMany(ShopProduct::class, 'product_id', 'id');
    }
    public function menu() {
        return $this->hasMany(ShopDavicookMenu::class, 'dish_id', 'id');
    }
    public static function getIdAll()
    {
        if (!self::$listDish) {
            self::$listDish = self::pluck('name','id')->all();
        }
        return self::$listDish;
    }
    public static function getDishCode()
    {
        if (!self::$listDishs) {
            self::$listDishs = self::pluck('code','id')->all();
        }
        return self::$listDishs;
    }

}
