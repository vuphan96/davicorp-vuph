<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWarehouseTransferDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_warehouse_transfer_detail';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
}
