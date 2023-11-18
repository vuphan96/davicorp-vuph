<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopDavicookMenu extends Model
{

    public $table = SC_DB_PREFIX . 'shop_davicook_menu';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function details(){
        return $this->hasMany(ShopDavicookMenuDetail::class, 'menu_id', 'id');
    }

    public function customer(){
        return $this->belongsTo(ShopDavicookCustomer::class, 'customer_id', 'id');
    }

    public function dish(){
        return $this->belongsTo(ShopDish::class, 'dish_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->details()->delete();
        });
    }
}
