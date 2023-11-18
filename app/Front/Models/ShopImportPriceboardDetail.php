<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopImportPriceboardDetail extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_import_priceboard_detail';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
    }

    public function priceboard()
    {
        return $this->belongsTo(ShopImportPriceboard::class, 'priceboard_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

}
