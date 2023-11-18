<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace App\Admin\Models;

use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Model;

class AdminImportReturn extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    public $table = SC_DB_PREFIX . 'shop_import_return';
    protected $keyType = 'string';
    protected $connection = SC_CONNECTION;
    protected $guarded = [];
    protected static $getList = null;

    public function getEditor(){
        if(isset($this->admin_id)){
            return '(Ad) ' . AdminUser::find($this->admin_id)->name ?? 'Undefine Admin';
        }
        return AdminCustomer::find($this->customer_id)->name ?? 'Undefine User';
    }

}
