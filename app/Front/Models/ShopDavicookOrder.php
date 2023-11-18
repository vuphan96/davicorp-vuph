<?php
#S-Cart/Core/Front/Models/ShopOrder.php
namespace App\Front\Models;
use Illuminate\Database\Eloquent\Model;

class ShopDavicookOrder extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    public $table = SC_DB_PREFIX.'shop_davicook_order';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public static $NOTE = [
        'Đơn chính',
        'Đơn sáng',
        'Kho sáng',
        'Kho chính',
        'Nhu yếu phẩm',
        'Hàng đợt 2',
        'Khác'
    ];

    public static $DELIVERY_STATUS = [
        1 => "Chưa giao",
        2 => "Đang giao",
        3 => "Đã giao",
    ];

    public static $PurchasePriorityLevels = [
        'Bình thường',
        'Cần đặt hàng ngay'
    ];
    
    /**
     * Davicook order detail.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(ShopDavicookOrderDetail::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(ShopDavicookCustomer::class, 'customer_id', 'id');
    }

    public function orderDavicookStatus()
    {
        return $this->hasOne(ShopDavicookOrderStatus::class, 'id', 'status');
    }

    public function history()
    {
        return $this->hasMany(ShopDavicookOrderHistory::class, 'order_id', 'id')->orderByDesc('add_date');
    }

    /**
     * Relationship History return order.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function returnHistory()
    {
        return $this->hasMany(ShopDavicookOrderReturnHistory::class, 'order_id', 'id');
    }

    public function status(){
        return $this->hasOne(ShopDavicookOrderStatus::class, 'id', 'status');
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($order) {
            $order->details()->delete(); //delete order details
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

    /**
     * Add order history
     * @param [array] $dataHistory
     */
    public function addOrderHistory($dataHistory)
    {
        return ShopDavicookOrderHistory::create($dataHistory);
    }

    // Get next order_id
    public function getNextId(){
        return ShopGenId::genNextId('order_cook');
    }
}
