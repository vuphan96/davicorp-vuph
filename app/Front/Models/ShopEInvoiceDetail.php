<?php
#S-Cart/Core/Front/Models/ShopOrderDetail.php
namespace App\Front\Models;

use App\Front\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUnit;
use DB;

class ShopEInvoiceDetail extends Model
{
    use \SCart\Core\Front\Models\ModelTrait;
    use \SCart\Core\Front\Models\UuidTrait;

    protected $table = SC_DB_PREFIX.'shop_einvoice_detail';
    protected $primaryKey = 'id';
    protected $connection = SC_CONNECTION;

    public function getEinvoiceDetail(array $id)
    {
        $dataDetails = ShopEInvoiceDetail::whereIn('einv_id', $id)->select('product_name','unit', 'price', 'qty', 'product_code')
            ->orderBy('product_name')->get();
        $dataGroupDetail = [];
        foreach ($dataDetails->groupBy(['product_code', 'price']) as $dataDetail) {
            foreach ($dataDetail as $value) {
                $dataGroupDetail[] = [
                    'product_name' => $value->first()->product_name ?? '',
                    'unit' => $value->first()->unit,
                    'qty' => $value->sum('qty') ?? 0,
                    'price' => $value->first()->price ?? 0,
                    'total_price' => ($value->sum('qty') *  $value->first()->price) ?? '',
                ];
            }
        }
        return $dataGroupDetail;
    }

}
