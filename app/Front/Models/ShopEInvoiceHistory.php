<?php
#S-Cart/Core/Front/Models/ShopOrderDetail.php
namespace App\Front\Models;

use App\Front\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;

class ShopEInvoiceHistory extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    protected $table = SC_DB_PREFIX.'shop_einvoice_sync_history';
    protected $primaryKey = 'id';
    protected $connection = SC_CONNECTION;

    public static $INVOICE_HISTORY_STATUS = [
        0 => 'Thất bại',
        1 => 'Thành công',
    ];

}
