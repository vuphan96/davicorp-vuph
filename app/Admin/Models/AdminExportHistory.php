<?php

namespace App\Admin\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class AdminExportHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = SC_DB_PREFIX . 'shop_export_history';
    protected $guarded = [];

    

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_export_history');
            }
        });
    }

}
