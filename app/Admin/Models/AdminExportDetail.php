<?php

namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use Cache;
use Illuminate\Database\Eloquent\Model;

class AdminExportDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = SC_DB_PREFIX . 'shop_export_detail';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_export_detail');
            }
            $model->created_at = $model->freshTimestamp();
            $model->updated_at = $model->freshTimestamp();
        });
    }

}
