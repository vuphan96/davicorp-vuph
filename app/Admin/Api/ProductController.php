<?php

namespace App\Admin\Api;

use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminUnit;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductDescription;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopUserPriceboardDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\String\lower;

class ProductController extends ApiController
{
    /**
     * Danh sách sản phẩm lấy ở bảng báo giá gán cho KH mà có hiệu lực theo ngày in đơn hàng
     * Trường hợp không có bảng báo giá nào có hiệu lực thì lấy theo bảng báo giá gần nhất và set là 0đ
     *
     */

    public function getListProduct(Request $request)
    {
        try {
            $billDateSt = $request->bill_date;
            if (!$billDateSt){
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Vui lòng chọn ngày trên hóa đơn!');
            }

            $user = $request->user();
            $customer_id = $user->id;

            $billDateSt = $request->bill_date;

            $data = AdminOrder::getListProductForCustomerByBillDate($billDateSt, $customer_id);

            return $this->responseSuccess($data);
        } catch (\Throwable $error) {
            Log::error($error->getMessage());
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $error->getMessage());
        }
    }

}
