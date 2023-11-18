<?php
#S-Cart/Core/Front/Models/ShopSupplier.php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use SCart\Core\Front\Models\ModelTrait;


class ShopZone extends Model
{
    use ModelTrait;

    private static $getList = null;
    public $table = SC_DB_PREFIX . 'shop_zone';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public function customers()
    {
        return $this->hasMany(ShopCustomer::class, 'zone_id', 'id')->orderBy('name', 'ASC');
    }

    public static function getFormatedCustomerZone()
    {
        $zones = self::with('customers')->orderBy('name')->get();
        $output = [];
        foreach ($zones as $zone) {
            $output[] = $zone->name ?? '';
            foreach ($zone->customers ?? [] as $customer) {
                $output[$customer->id] = '-' . $customer->name ?? '';
            }
        }
        return $output;
    }
}
