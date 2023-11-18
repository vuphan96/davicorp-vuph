<?php

namespace App\Admin\Api\Warehouse\Admin;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminDriver;
use App\Admin\Models\AdminHoliday;
use App\Admin\Models\AdminImport;
use App\Admin\Models\AdminImportDetail;
use App\Admin\Models\AdminImportHistory;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Admin\Models\AdminWarehouse;
use App\Exports\DavicorpOrder\AdminExportMultipleSheet;
use App\Exports\DavicorpOrder\AdminMultipleSheetSalesInvoiceListRealOrder;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopGenId;
use App\Front\Models\ShopImport;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopUserPriceboard;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProfileAdminController extends ApiController
{

    public function getProfileAdmin(Request $request){
        $user = $request->user('warehouse');
        return $this->responseSuccess($user);
    }

    public function changePassword(Request $request, $id){
        $user = $request->user('warehouse');
        if(!$user || $user->id != $id){
            return response()->json(['message' => 'Lỗi truy cập'], 401);
        }
        $currentPassword = $request->input('current_password');

        if (!Hash::check($currentPassword, $user->password)) {
            return response()->json(['message' => 'Mật khẩu hiện tại không đúng'], 400);
        }

        $newPassword = $request->input('new_password');
        $user->password = Hash::make($newPassword);
        $user->save();

        return response()->json(['message' => 'Đổi mật khẩu thành công!'], 200);
    }
}