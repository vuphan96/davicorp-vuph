<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSyncSupplier extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'warehouse_config';
    protected $connection = SC_CONNECTION;
    protected static $getList = null;
    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'group')->all();
        }
        return self::$getList;
    }
}
