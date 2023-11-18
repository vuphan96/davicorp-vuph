<?php
namespace App\Admin\Models;
use App\Front\Models\ShopGenId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AdminSystemChangeHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX.'admin_system_change_history';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public static function storeData($attributes) {
        return AdminSystemChangeHistory::create($attributes);
    }

}
