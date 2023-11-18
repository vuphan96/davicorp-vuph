<?php

namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ReportWarehouseCard extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = SC_DB_PREFIX . 'report_warehouse_card';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public static $TYPE_ORDER = [
        1 => "Phiếu nhập kho",
        2 => "Phiếu xuất kho",
        3 => "Phiếu điều chuyển",
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }


    public static function getListWarehouseCard(array $dataSearch = null, $checkQty = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $explain = $dataSearch['explain'] ?? '';
        $type_order = $dataSearch['type_order'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $warehouse = $dataSearch['warehouse'] ?? [];
        $product_id = $dataSearch['product_id'] ?? '';
        $supplier_id = $dataSearch['supplier_id'] ?? '';

        $dataWarehouseCard = new ReportWarehouseCard();
       if ($keyword) {
           $dataWarehouseCard = $dataWarehouseCard->where(function ($sql) use ($keyword) {
                $sql->where('object_name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_code', 'like', '%' . $keyword . '%');
            });
        }
       if($explain) {
           $dataWarehouseCard = $dataWarehouseCard->where('explain', $explain);
       }
       if($type_order) {
           $dataWarehouseCard = $dataWarehouseCard->where('type_order', $type_order);
       }
       if($checkQty) {
           if ($from_to != '') {

               $dataWarehouseCard = $dataWarehouseCard->whereDate('bill_date', '<', Carbon::createFromFormat('d/m/Y', $from_to));
           }
       } else {
           if ($from_to != '') {
               $dataWarehouseCard = $dataWarehouseCard->whereDate('bill_date', '>=', Carbon::createFromFormat('d/m/Y', $from_to));
           }
           if ($end_to != '') {
               $dataWarehouseCard = $dataWarehouseCard->whereDate('bill_date', '<=', Carbon::createFromFormat('d/m/Y', $end_to));
           }
       }

       if ($product_id) {
           $dataWarehouseCard = $dataWarehouseCard->where('product_id', $product_id);
       }
       if ($supplier_id) {
           $dataWarehouseCard = $dataWarehouseCard->where('supplier_id',$supplier_id);
       }
       if ($warehouse) {
           $dataWarehouseCard= $dataWarehouseCard->whereIn('warehouse_id', $warehouse);
       }

        $dataWarehouseCard = $dataWarehouseCard->orderBy('id', 'desc');
        return $dataWarehouseCard;
    }



    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'report_warehouse_card');
            }
        });
//        static::deleting(function ($order) {
//            $order->details()->delete();
//            $order->history()->delete();
//        });
    }

}
