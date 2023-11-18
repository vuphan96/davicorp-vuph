<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class AdminDriver extends Authenticatable
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    use HasApiTokens, Notifiable;

    public $table = SC_DB_PREFIX . 'shop_driver';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_customer');
            }
        });

        static::deleted(function ($model) {
            $model->details()->delete();
        });
    }
    public function deleteDriverDetails(){
        AdminDriverCustomer::where('staff_id', $this->id)->delete();
    }
    public function details(){
        return $this->hasMany(AdminDriverCustomer::class, 'staff_id', 'id');
    }
    public static function getListAll($dataSearch)
    {
        if (!self::$getList) {
            self::$getList = self::pluck('full_name', 'id_name')->all();
        }
        return self::$getList;
    }
    public static function getListDriver(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $driverList = (new AdminDriver());

        if ($keyword) {
            $driverList = $driverList->where('id_name', 'like', '%' . $keyword . '%')
                ->orWhere('full_name', 'like', '%' . $keyword . '%');
        }
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $driverList = $driverList->orderBy($field, $sort_field);
        } else {
            $driverList = $driverList->orderBy('id', 'desc');
        }
        $driverList = $driverList->paginate(config('pagination.admin.customer'));

        return $driverList;
    }

    /**
     * Find the user instance for the given username.
     */
    public function findForPassport(string $username): AdminDriver
    {
        return $this->where('login_name', $username)->first();
    }
    /**
     * Validate the password of the user for the Passport password grant.
     */
    public function validateForPassportPasswordGrant(string $password): bool
    {
        return Hash::check($password, $this->password);
    }

    public function getDeliveryOrderDavicorp($id, $dataFilter){
        $keyword = $dataFilter['keyword'];
        $start_day = $dataFilter['start_day'];
        $end_day = $dataFilter['end_day'];


        $table = SC_DB_PREFIX.'shop_customer';

        $dataDeliveryOrder = ShopCustomer::join(SC_DB_PREFIX.'shop_driver_customer as sdc', function ($join){
           $join->on(SC_DB_PREFIX.'shop_customer.id', 'sdc.customer_id');
        })->join(SC_DB_PREFIX.'shop_order as so', function ($join){
            $join = $join->on(SC_DB_PREFIX.'shop_customer.id', 'so.customer_id');
            if('sdc.type_order'==2) {
                $join = $join->where('so.explain', 'Hàng đợt 2');
            } else{
                $join = $join->where('so.explain', '!=',  'Hàng đợt 2');
            }
            return $join;
        })->join(SC_DB_PREFIX.'shop_driver as sd', function ($join){
            $join->on('sdc.staff_id', 'sd.id');
        });
        if ($keyword) {
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($keyword, $table) {
                $sql->where($table.'.name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%');
            });
        }
        if ($start_day) {

            $start_day = convertVnDateObject($start_day)->startOfDay()->toDateTimeString();
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($start_day) {
                $sql->where('so.delivery_time', '>=', $start_day);
            });
        }
        if ($end_day) {
            $end_day = convertVnDateObject($end_day)->endOfDay()->toDateTimeString();
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($end_day) {
                $sql->where('so.delivery_time', '<=', $end_day);
            });
        }
        $dataDeliveryOrder = $dataDeliveryOrder->where('sdc.staff_id',$id)
            ->where('sdc.type_customer', 1)
            ->where('so.status', '!=', 7)
            ->select($table.'.id as customer_id',
                $table.'.name as customer_name',
                'sd.id_name as staff_code',
                'sd.full_name as staff_name',
                'sdc.type_order',
                'so.id_name as order_code',
                'so.id as order_id',
                'so.status',
                'so.delivery_time as delivery_date',
                'so.customer_code',
                'so.explain',
                'so.actual_total_price as total_price')->orderBy('so.delivery_time', 'DESC')->get();
        return $dataDeliveryOrder;
    }

    public function getDeliveryOrderDavicook($id, $dataFilter){
        $keyword = $dataFilter['keyword'];
        $start_day = $dataFilter['start_day'];
        $end_day = $dataFilter['end_day'];


        $table = SC_DB_PREFIX.'shop_davicook_customer';

        $dataDeliveryOrder = ShopDavicookCustomer::join(SC_DB_PREFIX.'shop_driver_customer as sdc', function ($join){
            $join->on(SC_DB_PREFIX.'shop_davicook_customer.id', 'sdc.customer_id');
        })->join(SC_DB_PREFIX.'shop_davicook_order as so', function ($join){
            $join = $join->on(SC_DB_PREFIX.'shop_davicook_customer.id', 'so.customer_id');
            if('sdc.type_order'==2) {
                $join = $join->where('so.explain', 'Hàng đợt 2');
            } else{
                $join = $join->where('so.explain', '!=',  'Hàng đợt 2');
            }
            return $join;
        })->join(SC_DB_PREFIX.'shop_driver as sd', function ($join){
            $join->on('sdc.staff_id', 'sd.id');
        });
        if ($keyword) {
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($keyword, $table) {
                $sql->where($table.'.name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%');
            });
        }
        if ($start_day) {
            $start_day = convertVnDateObject($start_day)->startOfDay()->toDateTimeString();
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($start_day) {
                $sql->where('so.delivery_date', '>=', $start_day);
            });
        }
        if ($end_day) {
            $end_day = convertVnDateObject($end_day)->endOfDay()->toDateTimeString();
            $dataDeliveryOrder = $dataDeliveryOrder->where(function ($sql) use ($end_day) {
                $sql->where('so.delivery_date', '<=', $end_day);
            });
        }
        $dataDeliveryOrder = $dataDeliveryOrder->where('sdc.staff_id',$id)
            ->where('sdc.type_customer', 1)
            ->where('so.status', '!=', 7)
            ->select($table.'.id as customer_id',
                $table.'.name as customer_name',
                'sd.id_name as staff_code',
                'sd.full_name as staff_name',
                'sdc.type_order',
                'so.id_name as order_code',
                'so.id as order_id',
                'so.status',
                'so.delivery_date',
                'so.customer_code',
                'so.explain',
                'so.total as total_price')->orderBy('so.delivery_date', 'DESC')->get();
        return $dataDeliveryOrder;
    }


}
