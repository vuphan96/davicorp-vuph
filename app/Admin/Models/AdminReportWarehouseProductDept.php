<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminReportWarehouseProductDept extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'report_warehouse_product_dept';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;
    protected $keyType = "string";
    const UPDATED_AT = null;

    public function orderCorp(){
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }
    public function orderCook(){
        return $this->belongsTo(ShopDavicookOrder::class, 'order_id', 'id');
    }
    public function product(){
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
    public function customerCorp(){
        return $this->belongsTo(ShopCustomer::class, 'customer_code', 'customer_code');
    }
    public function customerCook(){
        return $this->belongsTo(ShopDavicookCustomer::class, 'customer_code', 'customer_code');
    }
}
