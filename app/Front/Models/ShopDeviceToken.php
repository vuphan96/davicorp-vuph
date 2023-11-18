<?php

namespace App\Front\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;
use SCart\Core\Front\Models\UuidTrait;

class ShopDeviceToken extends Model
{
    use HasFactory;
    use ModelTrait;
    use UuidTrait;

    public $table = SC_DB_PREFIX . 'shop_device_tokens';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;
}
