<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminWarehouseTransferHistory extends Model
{

    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_warehouse_transfer_history';
    protected $connection = SC_CONNECTION;
    protected $guarded           = [];

    public function getEditor(){
        return '(Ad) ' . AdminUser::find($this->admin_id)->name ?? 'Undefine Admin';
    }
}
