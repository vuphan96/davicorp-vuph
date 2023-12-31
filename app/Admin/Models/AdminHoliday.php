<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminHoliday extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_holidays';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    const UPDATED_AT = null;

    const Type = [
        'hodilay' => 'Khóa theo nghĩ lễ',
        'everyday' => 'Khóa theo T7, CN',
        'date_range' => 'Khóa theo khoảng ngày',
        ];

    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'name')->all();
        }
        return self::$getList;
    }
}
