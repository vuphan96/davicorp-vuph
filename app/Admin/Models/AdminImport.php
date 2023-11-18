<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopSupplier;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminImport extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX . 'shop_import';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public static $IMPORT_STATUS = [
        1 => 'Chưa xác nhận',
        2 => 'Đã xác nhận',
        3 => 'Đã nhập kho',
        4 => 'Hủy đơn hàng',
    ];

    public static $TYPE = [
        0 => 'Đơn thủ công',
        1 => "Mẫu NH 1",
        2 => "Mẫu NH 2",
        4 => "Nhập từ BC trả hàng",
        5 => "Đơn từ App - Thủ kho",
    ];
    const UPDATED_AT = null;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(AdminImportDetail::class, 'import_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function return()
    {
        return $this->hasMany(AdminImportReturn::class, 'import_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(AdminImportHistory::class, 'import_id', 'id')->orderByDesc('created_at');
    }

    public static function getImportListAdmin(array $dataSearch = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $status = $dataSearch['status'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $department = $dataSearch['department'] ?? '';
        $type_date = $dataSearch['type_date'] ?? '';
        $data = new AdminImport();

        if ($keyword) {
            $data = $data->where(function ($q) use ($keyword){
                $q->where('id_name', 'like', '%' . $keyword . '%');
                $q->orWhere('supplier_name', 'like', '%' . $keyword . '%');
            });
        }

        if ($status) {
            $data = $data->where('status', $status);
        }

        if($type_date == 'created_at'){
            if ($from_to != '') {
                $data = $data->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $from_to));
            }
            if ($end_to != '') {
                $data = $data->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $end_to));
            }
        }
        if ($type_date == 'delivery_date'){
            if ($from_to != '') {
                $data = $data->whereDate('delivery_date', '>=', Carbon::createFromFormat('d/m/Y', $from_to));
            }
            if ($end_to != '') {
                $data = $data->whereDate('delivery_date', '<=', Carbon::createFromFormat('d/m/Y', $end_to));
            }
        }

        if($department){
            $customer_id = ShopCustomer::where('department_id',$department)->pluck('id');
            $importIds = AdminImportDetail::whereIn('customer_id', $customer_id)->pluck('import_id')->unique();
            $data = $data->whereIn('id', $importIds);
        }

        $data = $data->orderBy('id', 'desc')->paginate(config('pagination.admin.medium'));
        return $data;
    }

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_import');
            }
        });

    }
}
