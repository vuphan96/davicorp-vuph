<?php
namespace App\Admin\Models;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopGenId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AdminDavicookMenuEstimateCard extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX.'shop_davicook_menu_estimate_card';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
    public static $typeStatusSync = [
        0 => 'Chưa tạo đơn',
        1 => 'Đã tạo đơn',
        2 => 'Lỗi',
    ];

    public static $typeObject = [
        1 => 'Học sinh',
        2 => 'Giáo viên',
    ];

    public static $mapStyleStatus = [
        '0' => 'warning', //Đơn nháp
        '1' => 'success', //Đang khả dụng
        '2' => 'info', //Đã xuất khô
        '7' => 'danger', //Đã hủy
    ];

    public static   $weekMap = [
            0 => 'CN',
            1 => '2',
            2 => '3',
            3 => '4',
            4 => '5',
            5 => '6',
            6 => '7',
    ];

    /**
     * Davicook order detail.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(AdminDavicookMenuCardChildren::class, 'menu_card_estimate_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(ShopDavicookCustomer::class, 'customer_id', 'id');
    }

    // Get next order_id
    public function getNextId(){
        return ShopGenId::genNextId('cook_menu_card');
    }

    /**
     * Get list order in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getDataDavicookMenuCardList(array $dataSearch, $all = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $limit = $dataSearch['limit'] ?? '';
        $start_date = $dataSearch['start_date'] ?? '';
        $end_date = $dataSearch['end_date'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $status_sync = $dataSearch['status_sync'];
        $type_object = $dataSearch['type_object'];
        $is_combine = $dataSearch['is_combine'];
        $orderList = new AdminDavicookMenuEstimateCard;
        if($status_sync != ''){
            $orderList = $orderList->where('status_sync', (int) $status_sync);
        }

        if($is_combine != ''){
            $orderList = $orderList->where('is_combine', (int) $is_combine);
        }

        if($type_object != ''){
            $orderList = $orderList->where('type_object', (int) $type_object);
        }

        if ($keyword) {
            $orderList = $orderList->where(function ($sql) use ($keyword) {
                $sql->where('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_code', 'like', '%'.$keyword.'%')
                    ->orWhere('card_name', 'like', '%'.$keyword.'%')
                    ->orWhere('id_name', 'like', '%'.$keyword.'%');
            });
        }

        if ($start_date) {
            $start_date = Carbon::createFromFormat('d/m/Y', $start_date)->endOfDay()->toDateString();
            $orderList = $orderList->where(function ($sql) use ($start_date) {
                $sql->where('start_date', '>=', $start_date);
            });
        }
        if ($end_date) {
            $end_date = Carbon::createFromFormat('d/m/Y', $end_date)->endOfDay()->toDateString();
            $orderList = $orderList->where(function ($sql) use ($end_date) {
                $sql->where('end_date', '<=', $end_date);
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $orderList = $orderList->orderBy($field, $sort_field);
        } else {
            $orderList = $orderList->orderBy('id', 'desc');
        }

        if ($limit) {
            return $orderList->paginate($limit);
        }

        return $orderList->paginate(15);
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($order) {
             //delete order details
            $order->details()->delete();

        });

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_davicook_menu_estimate_card');
            }
        });

    }

}
