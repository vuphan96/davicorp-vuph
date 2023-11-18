<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopImportPriceboard extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_import_priceboard';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id();
            }
        });
    }

    public function User(){
        return $this->belongsTo('SCart\Core\Admin\Models\AdminUser','admin_id','id');
    }

    public function supplier()
    {
        return $this->belongsTo(ShopSupplier::class, 'supplier_id', 'id');
    }

    public function details()
    {
        return $this->hasMany(ShopImportPriceboardDetail::class, 'priceboard_id', 'id');
    }

    /**
     * @param $supplier_id
     * @param $bill_date
     * @param $product_id
     * @return string
     */
    public static function getImportPriceDetail($supplier_id, $bill_date, $product_id , $web = null)
    {
        if ($web) {
            $bill_date = Carbon::createFromFormat('Y-m-d', $bill_date);
        } else {
            $bill_date = Carbon::createFromFormat('d-m-Y', $bill_date);
        }

        $priceboard = ShopImportPriceboard::where('supplier_id', $supplier_id)
            ->where("start_date", "<=", $bill_date)
            ->where("end_date", ">=", $bill_date)->first();
        $priceboard_id = $priceboard->id ?? '';

        return ShopImportPriceboardDetail::where('priceboard_id', $priceboard_id)->where('product_id', $product_id)->first()->price ?? '';
    }
}
