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

class ReportWarehouseProductDeptHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'report_warehouse_product_dept_history';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;
    protected $keyType = "string";
    public function history(){
        return $this->belongsTo(ReportWarehouseProductDept::class, 'report_product_dept_id', 'id');
    }
}
