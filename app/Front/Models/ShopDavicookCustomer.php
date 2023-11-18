<?php
namespace App\Front\Models;
use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopZone;
use Illuminate\Database\Eloquent\Model;
use DB;

class ShopDavicookCustomer extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;


    public $table = SC_DB_PREFIX . 'shop_davicook_customer';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function zone()
    {
        return $this->belongsTo(ShopZone::class,'zone_id', 'id');
    }

    public function tier()
    {
        return $this->belongsTo(ShopRewardTier::class,'tier_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(ShopDepartment::class,'department_id', 'id');
    }
    public function davicookProductSuppliers(){
        return $this->hasMany(ShopDavicookProductSupplier::class, 'customer_id', 'id');
    }
    public function menu(){
        return $this->hasMany(ShopDavicookMenu::class, 'customer_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_davicook_customer');
            }
        });
        static::deleted(function ($model) {
            $model->davicookProductSuppliers()->delete();
            foreach ($model->menu as $item){
                $item->details()->delete();
            }
            $model->menu()->delete();
        });
    }
}