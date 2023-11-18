<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopRewardTier extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_reward_tier';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
}
