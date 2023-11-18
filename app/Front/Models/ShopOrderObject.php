<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopOrderObject extends Model
{
    use HasFactory;

    public $table = SC_DB_PREFIX.'shop_order_object';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
    protected static $listObject = null;

    public static function getIdAll()
    {
        if (!self::$listObject) {
            self::$listObject = self::pluck('name', 'id')->all();
        }
        return self::$listObject;
    }
}
