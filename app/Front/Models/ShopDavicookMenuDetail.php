<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopDavicookMenuDetail extends Model
{

    public $table = SC_DB_PREFIX . 'shop_davicook_menu_detail';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function menu(){
        return $this->belongsTo(ShopDavicookMenu::class, 'menu_id', 'id');
    }

    public function product(){
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

}
