<?php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ShopImport extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;


    public $table = SC_DB_PREFIX.'shop_import';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;



}