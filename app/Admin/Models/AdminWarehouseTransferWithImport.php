<?php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWarehouseTransferWithImport extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_warehouse_transfer_with_import';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];
}
