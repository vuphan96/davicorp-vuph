<?php
namespace App\Admin\Models;
use App\Front\Models\ShopGenId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AdminDavicookMenuCardChildren extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX.'shop_davicook_menu_card';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    /**
     * Davicook order detail.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(AdminDavicookMenuCardChilldenDetail::class, 'menu_card_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($order) {
            $order->children()->delete(); //delete order details
        });
//        //Uuid
//        static::creating(function ($model) {
//            if (empty($model->{$model->getKeyName()})) {
//                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_davicook_order');
//            }
//        });
    }

}
