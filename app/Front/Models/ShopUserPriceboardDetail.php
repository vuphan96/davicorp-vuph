<?php

namespace App\Front\Models;

use App\Admin\Models\AdminUserPriceboardDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopUserPriceboardDetail extends Model
{
    use HasFactory;
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_user_priceboard_details';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customer()
    {
        return $this->belongsTo( ShopCustomer::class, 'customer_id', 'id');
    }
    public function priceboard(){
        return $this->belongsTo(ShopCustomer::class,'priceboard_id', 'id');
    }
}
