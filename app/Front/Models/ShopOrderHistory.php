<?php
#S-Cart/Core/Front/Models/ShopOrderHistory.php
namespace App\Front\Models;

use App\Admin\Models\AdminUser;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Admin\Admin;
use App\Admin\Models\AdminCustomer;

class ShopOrderHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    public $table = SC_DB_PREFIX.'shop_order_history';
    protected $connection = SC_CONNECTION;
    const CREATED_AT = 'add_date';
    const UPDATED_AT = null;
    protected $guarded           = [];

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($obj) {
                //
            }
        );

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_order_detail');
            }
        });
    }

    public function detail(){
        return $this->hasOne(ShopOrderDetail::class, 'id', 'detail_id');
    }

    public function getEditor(){
        if(isset($this->admin_id)){
           return '(Ad) ' . AdminUser::find($this->admin_id)->name ?? 'Undefine Admin';
        }
        return AdminCustomer::find($this->customer_id)->name ?? 'Undefine User';
    }
}
