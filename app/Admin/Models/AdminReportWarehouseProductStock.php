<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminReportWarehouseProductStock extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'report_warehouse_product_stock';
    protected $connection = SC_CONNECTION;
    protected $keyType = 'string';
    protected $guarded           = [];
    protected static $getList = null;

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'report_warehouse_product_stock');
            }
        });
    }

}
