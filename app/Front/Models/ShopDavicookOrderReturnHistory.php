<?php

namespace App\Front\Models;

use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopDavicookOrderReturnHistory extends Model
{
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX.'shop_davicook_order_return_history';
    protected $guarded = [];
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product(){
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    public function detail()
    {
        return $this->hasOne(ShopDavicookOrderDetail::class, 'id', 'detail_id');
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(ShopDavicookOrder::class, 'order_id', 'id');

    }

//    public function
    public function getEditor()
    {
        if(!empty($this->admin_id)){
            return '(Ad) ' . AdminUser::find($this->admin_id)->name ?? 'Undefine Admin';
        }
        return '';
    }
}
