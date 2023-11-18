<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Model;

class AdminDriverCustomer extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX . 'shop_driver_customer';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;
    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
        });
    }

    public function customerDavicorp(){
        return $this->hasOne(ShopCustomer::class, 'id', 'customer_id');
    }
    public function customerDavicook(){
        return $this->hasOne(ShopDavicookCustomer::class, 'id', 'customer_id');
    }


    public static function getImportListAdmin(array $dataSearch = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $data = new AdminDriverCustomer();

        if ($keyword) {
            $data = $data->where('full_name', 'like', '%' . $keyword . '%');
        }
        $data = $data->orderBy('created_at', 'desc')->paginate(config('pagination.admin.medium'));
        return $data;
    }
}
