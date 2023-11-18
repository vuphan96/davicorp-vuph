<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWarehouseProduct extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_product_warehouse';
    // public $table = SC_DB_PREFIX.'shop_length';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    const UPDATED_AT = null;

}
