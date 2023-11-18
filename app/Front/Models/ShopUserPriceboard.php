<?php

namespace App\Front\Models;

use App\Admin\Models\AdminProductPrice;
use App\Admin\Models\AdminUserPriceboardDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopUserPriceboard extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_user_priceboard';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($userPriceboard) {
            $userPriceboard->customers()->delete();
        });
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id();
            }
        });
    }

    public function customers()
    {
        return $this->hasMany(AdminUserPriceboardDetail::class, 'user_priceboard_id', 'id');
    }

    public function priceBoard()
    {
        return $this->hasOne(AdminProductPrice::class, 'id', 'product_price_id');
    }
}
