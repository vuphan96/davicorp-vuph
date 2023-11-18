<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopPoint extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_point';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
    protected $primaryKey = 'id';
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($model) {
            $model->history()->delete();
        });
    }


    public function customer()
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'id');
    }

    public function getCustomerName()
    {
        return $this->customer->name ?? '';
    }

    public function getCustomerCode()
    {
        return $this->customer->customer_code ?? '';
    }

    public function getCustomerId()
    {
        return $this->customer->id ?? '';
    }

    public function getPoint()
    {
        return $this->point ?? 0;
    }

    public function getIncomingPoint()
    {
        $count = 0;
        $history = $this->history()->where('action', 1)->get();
        foreach ($history as $item) {
            $count += $item->change_point;
        }
        return $count;
    }

    public function getHistory()
    {
        return $this->history()->where('action', 1)->get();
    }

    public function getExchangeMoney(int $rate, bool $formated = null)
    {
        return $this->getPoint() * $rate;
    }

    public function history()
    {
        return $this->hasMany(ShopPointHistory::class, 'point_id', 'id');
    }

    public function histories()
    {
        return $this->hasMany(ShopPointHistory::class, 'point_id', 'id');
    }
}
