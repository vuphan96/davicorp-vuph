<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminWarehouseTransfer extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX . 'shop_warehouse_transfer';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
    const STATUS = [
        1 => 'Chưa chuyển',
        2 => 'Đang chuyển',
        3 => 'Đã nhận',
        4 => 'Đã hủy',
    ];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(AdminWarehouseTransferDetail::class, 'warehouse_transfer_id', 'id');
    }

    public function imports()
    {
        return $this->hasMany(AdminWarehouseTransferWithImport::class, 'warehouse_transfer_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(AdminWarehouseTransferHistory::class, 'warehouse_transfer_id', 'id')->orderByDesc('created_at');
    }

    public static function getLitAll($dataSearch)
    {
        $type_date = $dataSearch['type_date'] ?? '';
        $date_start = $dataSearch['date_start'] ?? '';
        $date_end = $dataSearch['date_end'] ?? '';
        $status = $dataSearch['status'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $result = new AdminWarehouseTransfer();
        if ($keyword) {
            $result = $result->where(function ($q) use ($keyword) {
                $q->where('id_name', $keyword);
                $q->orWhere('title', 'like', '%' . $keyword . '%');
            });
        }

        if ($status) {
            $result = $result->where('status', $status);
        }

        if ($type_date == 'created_at') {
            if ($date_start != '') {
                $result = $result->whereDate('created_at', '>=', Carbon::createFromFormat('d/m/Y', $date_start));
            }
            if ($date_end != '') {
                $result = $result->whereDate('created_at', '<=', Carbon::createFromFormat('d/m/Y', $date_end));
            }
        }

        if ($type_date == 'export_date') {
            if ($date_start != '') {
                $result = $result->whereDate('date_export', '>=', Carbon::createFromFormat('d/m/Y', $date_start));
            }
            if ($date_end != '') {
                $result = $result->whereDate('date_export', '<=', Carbon::createFromFormat('d/m/Y', $date_end));
            }
        }

        $result = $result->orderBy('id_name', 'desc')->paginate(15);

        return $result;
    }


    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_warehouse_transfer');
            }
        });

    }
}
