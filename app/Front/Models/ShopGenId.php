<?php
namespace App\Front\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Support\Facades\Log;

class ShopGenId extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    public $table = SC_DB_PREFIX.'shop_gen_id';
    protected $primaryKey = 'id';
    protected $guarded = [];
    protected $connection = SC_CONNECTION;

    public static function genNextId($idName){
        $maxId = self::where('id_name', $idName)->first();
        if (!$maxId){
            return '';
        }

        $nextId = $maxId->current_value + 1;
        $maxId->current_value = $nextId;
        $maxId->save();

        $prefix = $maxId->prefix;
        $maxLen = $maxId->max_len;

        return $prefix . substr(str_repeat('0', $maxLen) . $nextId, -$maxLen);
    }

}