<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopRewardPrinciple extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_reward_principle';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
}
