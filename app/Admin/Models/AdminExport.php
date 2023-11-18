<?php

namespace App\Admin\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AdminExport extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = SC_DB_PREFIX . 'shop_export';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public static $EXPORT_STATUS = [
        1 => 'Chưa xuất kho',
        2 => 'Đã xuất kho',
        3 => 'Hủy đơn xuất',
    ];
    public static $TYPE = [
        0 => 'Loại đơn',
        1 => "Đơn thủ công",
        2 => "Đơn từ báo cáo",
        4 => "Đơn từ báo cáo nợ hàng",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(AdminExportDetail::class, 'export_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function history()
    {
        return $this->hasMany(AdminExportHistory::class, 'export_id', 'id')->orderByDesc('created_at');
    }

    public static function getExportListAdmin(array $dataSearch = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $from_to = $dataSearch['from_date'] ?? '';
        $end_to = $dataSearch['end_date'] ?? '';
        $status = $dataSearch['status'] ?? '';
        $type = $dataSearch['type'] ?? '';
        $date_type = $dataSearch['date_type'] ?? '';
        $dataExport = new AdminExport();
       if ($keyword) {
           $dataExport = $dataExport->where(function ($sql) use ($keyword) {
                $sql->where('id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('note', 'like', '%' . $keyword . '%')
                ;
            });
        }
       if ($date_type){
           if ($date_type == 1) {
               if($from_to) {
                   $dataExport = $dataExport->whereDate("created_at", ">=", convertVnDateObject($from_to)->startOfDay()->toDateTimeString());
               }
               if($end_to) {
                   $dataExport = $dataExport->whereDate("created_at", "<=", convertVnDateObject($end_to)->startOfDay()->toDateTimeString());
               }
           } else {
               if($from_to) {
                   $dataExport = $dataExport->whereDate("date_export", ">=", convertVnDateObject($from_to)->startOfDay()->toDateTimeString());
               }
               if($end_to) {
                   $dataExport = $dataExport->whereDate("date_export", "<=", convertVnDateObject($end_to)->startOfDay()->toDateTimeString());
               }
           }

       }
       if($status) {
           $dataExport = $dataExport->where('status', $status);
       }
       if($type) {
           if($type == 1){
               $dataExport = $dataExport->where('type_order', $type);
           } else {
               $dataExport = $dataExport->where('type_order','!=' , 1);
           }
       }

        $dataExport = $dataExport->orderBy('created_at', 'desc')->paginate(config('pagination.admin.medium'));
        return $dataExport;
    }

    public static function getProductList()
    {
        $dataProduct = AdminProduct::with('unit')->where('status', 1)->select('id','name', 'sku', 'unit_id', 'kind', 'category_id')->get();
        return $dataProduct;
    }


    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_export');
            }
        });
        static::deleting(function ($order) {
            $order->details()->delete(); //delete details
            $order->history()->delete(); //delete details
        });
    }

}
