<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Model;

class AdminImportDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_import_detail';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

}
