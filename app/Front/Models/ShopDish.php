<?php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShopDish extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;


    public $table = SC_DB_PREFIX.'shop_davicook_dish';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function menu() {
        return $this->hasMany(ShopDavicookMenu::class, 'dish_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_davicook_dish');
            }
        });
        static::deleted(function ($model) {
            foreach ($model->menu as $item){
                $item->details()->delete();
            }
            $model->menu()->delete();
        });


    }

}