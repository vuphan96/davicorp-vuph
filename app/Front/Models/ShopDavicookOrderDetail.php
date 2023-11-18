<?php
#S-Cart/Core/Front/Models/ShopOrderDetail.php
namespace App\Front\Models;
use App\Front\Models\ShopProduct;
//use AWS\CRT\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class ShopDavicookOrderDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    protected $table = SC_DB_PREFIX.'shop_davicook_order_detail';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(ShopDavicookOrder::class, 'order_id', 'id');
    }

    /**
     * Relationship davicook order.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function davicookOrder()
    {
        return $this->belongsTo(ShopDavicookOrder::class, 'order_id', 'id');
    }

    /**
     * Relationship products.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function dish() 
    {
        return $this->belongsTo(ShopDavicookDish::class, 'dish_id', 'id');
    }

    public function getTable()
    {
        return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
    }
    // public function updateDetail($id, $data)
    // {
    //     return $this->where('id', $id)->update($data);
    // }
    public function addNewDetail(array $data)
    {
        if ($data) {
            $this->insert($data);
            //Update stock, sold
            foreach ($data as $key => $item) {
                //Update stock, sold
                ShopProduct::updateStock($item['product_id'], $item['qty']);
            }
        }
    }

    /**
     * Get product for davicook order return.
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|Model|object|null
     */
    public static function getProductDavicookOrderDetail($id)
    {
        $data = self::with(['product'])
            ->where('order_id', $id);
        return $data->get();
    }

    /**
     * @param $id
     * @param $product_id
     * @return mixed
     */
    public function getSumQtyDetailProduct($id, $product_id)
    {
        $data = self::where('order_id', $id)
            ->where('product_id', $product_id)
            ->groupBy('product_id')
            ->selectRaw('*, sum(real_total_bom) as sum');
        return $data->first()->sum;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($order) {
            $order->details()->delete(); //delete order details
            $order->orderTotal()->delete(); //delete order total
            $order->history()->delete(); //delete history
            $order->returnHistory()->delete(); //delete history
        });

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_davicook_order');
            }
        });

    }

}
