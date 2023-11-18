<?php
namespace App\Admin\Models;
use App\Front\Models\ShopGenId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AdminDavicookMenuCardChilldenDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;
    public $table = SC_DB_PREFIX.'shop_davicook_menu_card_detail';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;


}
