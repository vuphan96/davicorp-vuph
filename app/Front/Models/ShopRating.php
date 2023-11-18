<?php
#S-Cart/Core/Front/Models/ShopSupplier.php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;


class ShopRating extends Model
{
    use ModelTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_rating';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customer()
    {
        return $this->belongsTo(ShopCustomer::class, 'customer_id', 'id');
    }
}