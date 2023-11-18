<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Model;

class AdminImportHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_import_history';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;



}
