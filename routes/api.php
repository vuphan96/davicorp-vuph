<?php

use App\Admin\Api\RatingController;
use App\Admin\Api\Warehouse\Admin\ProfileAdminController;
use App\Admin\Api\Warehouse\Admin\ReportProductDept;
use App\Admin\Api\Warehouse\Admin\ReportProductReturn;
use App\Admin\Api\Warehouse\Driver\Account\DriverInfomationController;
use App\Admin\Api\Warehouse\Supplier\Account\ProfileSupllierController;
use App\Admin\Api\Warehouse\Admin\AdminOrderDavicorpController;
use App\Admin\Api\Warehouse\Admin\OrderImportController;
use App\Admin\Api\Warehouse\Driver\ApiDriverController;
use App\Admin\Api\Warehouse\Supplier\SupplierController;
use App\Admin\Api\Warehouse\WarehouseAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/login', [App\Admin\Api\AuthController::class, 'login']);
Route::post('/schoolmaster_login', [App\Admin\Api\AuthController::class, 'schoolmasterLogin']);
Route::get('/mobile_app_info', [App\Admin\Api\ShopFrontController::class, 'getMobileAppInfo']);
Route::get('/category', [App\Admin\Api\ShopFrontController::class, 'getAllCategory']);
Route::get('/comment_order', [App\Admin\Api\ShopFrontController::class, 'getCommentOrder']);
Route::get('/check_enable_register_account', [App\Admin\Api\ShopFrontController::class, 'checkEnableRegisterAccount']);
Route::post('/customer/create', [App\Admin\Api\AuthController::class, 'create']);

//Warehouse
//Route::post('/warehouse/login', [WarehouseAuthController::class, 'login']); //Login controller



Route::group([
    'middleware' => ['auth:api', 'scope:user, user-guest']
], function () {
    Route::group(['prefix' => 'customer'], function () {
        Route::post('/logout', [App\Admin\Api\AuthController::class, 'logout']);
        Route::post('/logout_schoolmaster', [App\Admin\Api\AuthController::class, 'logoutSchoolmaster']);
        Route::get('/info', [App\Admin\Api\CustomerController::class, 'getInfo']);
        Route::post('/info/update', [App\Admin\Api\CustomerController::class, 'updateInfo']);
        Route::post('/info/password/check', [App\Admin\Api\CustomerController::class, 'checkPassword']);
        Route::post('/info/password/update', [App\Admin\Api\CustomerController::class, 'updatePassword']);
        Route::post('/info/schoolmaster_password/update', [App\Admin\Api\CustomerController::class, 'updateShoolmasterPassword']);
        Route::get('/info/reward/histories', [App\Admin\Api\CustomerController::class, 'rewardHistories']);
        Route::get('/info/point/histories', [App\Admin\Api\CustomerController::class, 'pointHistories']);
        Route::get('/notifications', [App\Admin\Api\CustomerController::class, 'getNotifications']);
        Route::get('/read/notification/{id}', [App\Admin\Api\CustomerController::class, 'readNotification']);
        Route::get('/read/all/notifications', [App\Admin\Api\CustomerController::class, 'readAllNotifications']);
        Route::post('/remove', [App\Admin\Api\CustomerController::class, 'removeAccount']);
        Route::group(['prefix' => 'rating'], function () {
            Route::get("get", [RatingController::class, "getRating"]);
            Route::post("create", [RatingController::class, "create"]);
            Route::post("edit/{id}", [RatingController::class, "edit"]);
            Route::post("delete/{id}", [RatingController::class, "delete"]);
        });
    });
    Route::group(['prefix' => 'order'], function () {
        Route::post('/validate-order', [App\Admin\Api\OrderController::class, 'validateOrder']);
        Route::post('/create', [App\Admin\Api\OrderController::class, 'createOrder']);
        Route::post('/update/{id}', [App\Admin\Api\OrderController::class, 'updateOrder']);
        Route::get('/detail/{id}', [App\Admin\Api\OrderController::class, 'getOrderDetail']);
        Route::get('/histories', [App\Admin\Api\OrderController::class, 'getOrderHistories']);
        Route::get('/totalAmount', [App\Admin\Api\OrderController::class, 'getTotalAmountByCurrentMonth']);
        Route::post('/cancel/{id}', [App\Admin\Api\OrderController::class, 'cancelOrder']);
        Route::get('/downloadPdf/{id}', [App\Admin\Api\OrderController::class, 'downloadPdf']);
        Route::get('/downloadExcel/{id}', [App\Admin\Api\OrderController::class, 'downloadExcel']);
        Route::get('/downloadExcelEinvoice', [App\Admin\Api\OrderController::class, 'downloadExcelEInvoice']);
    });
    Route::group(['prefix' => 'product'], function () {
        Route::get('/list', [App\Admin\Api\ProductController::class, 'getListProduct']);
    });
});

Route::group(['prefix' => 'robot'], function () {
    Route::get('/list-einv', [App\Admin\Api\RobotController::class, 'getListEInv']);
    Route::get('/einv', [App\Admin\Api\RobotController::class, 'getEInvDetail']);
    Route::get('/list-sale-order', [App\Admin\Api\RobotController::class, 'getListSaleOrder']);
    Route::get('/list-purchase-order', [App\Admin\Api\RobotController::class, 'getListPurchaseOrder']);
    Route::post('/update-status-einv', [App\Admin\Api\RobotController::class, 'updateStatusEInv']);
    Route::post('/update-sign-status-einv', [App\Admin\Api\RobotController::class, 'updateSignStatusEInv']);
    Route::post('/update-status-sale-order', [App\Admin\Api\RobotController::class, 'updateStatusSaleOrder']);
});
//warehouse
Route::group(['middleware' => ['auth:admin-api', 'scope:warehouse']], function () {
    Route::group(['prefix' => 'warehouse-app'], function () {
        # LOGIN + LOGOUT
        Route::post('/logout', [WarehouseAuthController::class, 'logout']);
        Route::post('/login', [WarehouseAuthController::class, 'postLogin'])->withoutMiddleware(['auth:admin-api', 'scope:warehouse']);
        # Profile
        Route::get('/getProfileAdmin', [ProfileAdminController::class, 'getProfileAdmin']);
        Route::post('/changePassWord/{id}', [ProfileAdminController::class, 'changePassword']);
        # Đơn hàng Davicorp
        Route::get('/listOrder', [AdminOrderDavicorpController::class, 'getListOrder']);
        Route::get('/detailOrder/{id}', [AdminOrderDavicorpController::class, 'detailOrder']);
        Route::post('/updateOrder/{id}', [AdminOrderDavicorpController::class, 'updateOrder']);
        Route::post('/cancelOrder/{id}', [AdminOrderDavicorpController::class, 'cancelOrder']);
        Route::post('/checkBarcodeOrder/', [AdminOrderDavicorpController::class, 'checkBarcodeOrder']);

        # Đơn hàng nhập
        Route::get('/listOrderImport', [OrderImportController::class, 'getListOrderImport']);
        Route::get('/detailOrderImport/{id}', [OrderImportController::class, 'detailOrderImport']);
        Route::get('/getProducts', [OrderImportController::class, 'getProducts']);
        Route::get('/getSuppliers', [OrderImportController::class, 'getSuppliers']);
        Route::get('/getProductForDetail/{id}', [OrderImportController::class, 'getProductForDetail']);
        Route::post('/storeNewImportOrder', [OrderImportController::class, 'storeNewImportOrder']);
        Route::post('/updateInfoImport/{id}', [OrderImportController::class, 'updateInfoImport']);
        Route::post('/updateImportDetail/{id}', [OrderImportController::class, 'updateImportDetail']);
        Route::post('/cancelOrderImport/{id}', [OrderImportController::class, 'cancelOrderImport']);
        Route::post('/cloneImportOrder/{id}', [OrderImportController::class, 'cloneImportOrder']);

        # Báo cáo nợ hàng
        Route::get('/listReportProductDept', [ReportProductDept::class, 'getReportProductDept'])->withoutMiddleware(['auth:admin-api', 'scope:warehouse']);
        Route::get('/exportExcelReportProductDept', [ReportProductDept::class, 'exportExcelReportProductDept'])->withoutMiddleware(['auth:admin-api', 'scope:warehouse']);

        # Report nhập hàng đối với đơn trả
        Route::get('/getListReport', [ReportProductReturn::class, 'getListReport']);
        Route::post('/createOrderImport', [ReportProductReturn::class, 'createOrderImport']);
    });
});
//supplier
Route::group(['middleware' => ['auth:supplier', 'scope:supplier']], function () {
    Route::group(['prefix' => 'supplier-app'], function () {
        Route::post('/logout', [WarehouseAuthController::class, 'logoutSupplier']);
        Route::post('/login', [WarehouseAuthController::class, 'loginSupplierApp'])->withoutMiddleware(['auth:supplier', 'scope:supplier']);
        Route::get('/getOrderImport', [SupplierController::class, 'getOrderImport']);
        Route::get('/getOrderImportDetail/{id}', [SupplierController::class, 'getOrderImportDetail']);
        Route::get('/confirmedOrder/{id}', [SupplierController::class, 'confirmedOrder']);
        Route::post('/saveSignature/{id}', [SupplierController::class, 'saveSignature']);
        Route::get('/getAccountSupplier', [ProfileSupllierController::class, 'getAccountSupplier']);
        Route::post('/changePassWord', [ProfileSupllierController::class, 'changePassWord']);
    });
});
//driver
Route::group(['middleware' => ['auth:driver', 'scope:driver']], function () {
    Route::group(['prefix' => 'driver-app'], function () {
        Route::post('/logout', [WarehouseAuthController::class, 'logoutDriver']);
        Route::post('/login', [WarehouseAuthController::class, 'loginDriverApp'])->withoutMiddleware(['auth:driver', 'scope:driver']);
        Route::get('/list-order', [ApiDriverController::class, 'getListDeliveryOrder']);
        Route::get('/detail-order', [ApiDriverController::class, 'getDetailDeliveryOrder']);
        Route::post('/update_status-delivery', [ApiDriverController::class, 'updateStatusDelivery']);
        Route::get('/info', [DriverInfomationController::class, 'getInfo']);
        Route::post('/update-password', [DriverInfomationController::class, 'postUpdatePassword']);
    });
});
//account
Route::post('/account-detect', [WarehouseAuthController::class, 'detect']);
