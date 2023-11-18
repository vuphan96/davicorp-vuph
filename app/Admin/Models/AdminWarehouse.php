<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWarehouse extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_warehouse';
    // public $table = SC_DB_PREFIX.'shop_length';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    const UPDATED_AT = null;

    public static function getListAll($dataSearch)
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'name','warehouse_code')->all();
        }
        return self::$getList;
    }
}
