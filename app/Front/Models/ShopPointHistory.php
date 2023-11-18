<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopPointHistory extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_point_history';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customer()
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }

    public function getPoint()
    {
        return $this->point ?? 0;
    }
}
