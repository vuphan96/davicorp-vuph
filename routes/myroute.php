<?php

use App\Admin\Controllers\AdminFixMeController;
use App\Admin\Controllers\AdminRatingController;
use App\Admin\Controllers\Warehouse\AdminDriverController;
use App\Admin\Controllers\Warehouse\AdminOrderImportController;
use App\Admin\Controllers\Warehouse\AdminProductExchangeController;
use App\Admin\Controllers\Warehouse\AdminWarehouseTransferController;
use App\Admin\Controllers\Warehouse\OrderDriver\DriverOrderDavicookController;
use App\Admin\Controllers\Warehouse\OrderDriver\DriverOrderDavicorpController;
use App\Admin\Controllers\Warehouse\Report\AdminReportExportOrderController;
use App\Admin\Controllers\Warehouse\Report\AdminReportImportOrderController;
use App\Admin\Controllers\Warehouse\Report\AdminReportProductInventoryController;
use App\Admin\Controllers\Warehouse\Report\AdminReportReturnImportOrderController;
use App\Admin\Controllers\Warehouse\Report\AdminReportWarehouseCardController;
use App\Admin\Controllers\Warehouse\Report\AdminReportWarehouseProductDeptController;
use App\Admin\Controllers\Warehouse\Report\AdminReportWarehouseProductStock;
use App\Admin\Controllers\Warehouse\Report\AdminReportWarehouseProductStockController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Customize web Routes
|--------------------------------------------------------------------------
|
 */

//Route::get('category/create', function (){echo "hi";})->name('admin_category.create');

//Route::get('category/create', )->name('admin_category.create');
Route::prefix(SC_ADMIN_PREFIX)->middleware(SC_ADMIN_MIDDLEWARE)->group(function () {

    // ------------------------------------- DAVICORP -----------------------------------------------
    // Quản lý sản phẩm
    Route::group(['prefix' => 'product'], function () {
        Route::post('export', [\App\Admin\Controllers\AdminProductController::class, 'export'])->name('admin_product.export');
        Route::get('import', [\App\Admin\Controllers\AdminProductController::class, 'import'])->name('admin_product.import');
        Route::post('import', [\App\Admin\Controllers\AdminProductController::class, 'importProduct'])->name('admin_product.import');

    });

    // Quản lý danh mục sản phẩm
    Route::group(['prefix' => 'category'], function () {
        Route::get('/import', [\App\Admin\Controllers\AdminCategoryController::class, 'import'])->name('admin_category.import');
        Route::post('/import', [\App\Admin\Controllers\AdminCategoryController::class, 'importPost'])->name('admin_category.import');
        Route::get('/export', [\App\Admin\Controllers\AdminCategoryController::class, 'export'])->name('admin_category.export');
    });

    // Quản lý đơn vị tính
    Route::group(['prefix' => 'unit'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminUnitmoneyController::class, 'index'])->name('admin_unit.index');
        Route::post('/create', [\App\Admin\Controllers\AdminUnitmoneyController::class, 'postCreate'])->name('admin_unit.create');
        Route::get('/edit/{id}', [\App\Admin\Controllers\AdminUnitmoneyController::class, 'edit'])->name('admin_unit.edit');
        Route::post('/edit/{id}', [\App\Admin\Controllers\AdminUnitmoneyController::class, 'postEdit'])->name('admin_unit.edit');
        Route::delete('/delete', [\App\Admin\Controllers\AdminUnitmoneyController::class, 'deleteList'])->name('admin_unit.delete');
    });

    // Quản lý đơn hàng
    Route::group(['prefix' => 'order'], function () {
        Route::post('create', [\App\Admin\Controllers\AdminOrderController::class, 'postCreate'])->name('admin_order.create');
        Route::post('clone', [\App\Admin\Controllers\AdminOrderController::class, 'postClone'])->name('admin_order.clone');
        Route::post('update', [\App\Admin\Controllers\AdminOrderController::class, 'postOrderUpdate'])->name('admin_order.update');
        Route::post('update-customer-info', [\App\Admin\Controllers\AdminOrderController::class, 'postOrderUpdateCustomerInfo'])->name('admin_order.update_customer_info');
        Route::post('detail-update', [\App\Admin\Controllers\AdminOrderController::class, 'postOrderDetailUpdate'])->name('admin_order.detail_update');
        Route::post('detail-delete', [\App\Admin\Controllers\AdminOrderController::class, 'postDeleteItem'])->name('admin_order.detail_delete');
        Route::post('detail-add', [\App\Admin\Controllers\AdminOrderController::class, 'postAddItem'])->name('admin_order.detail_add');
        Route::get('return/{id}', [\App\Admin\Controllers\AdminOrderController::class, 'return'])->name('admin_order.return');
        Route::post('return/{id}', [\App\Admin\Controllers\AdminOrderController::class, 'postReturn'])->name('admin_order.return');
        Route::post('undo-order-return', [\App\Admin\Controllers\AdminOrderController::class, 'undoReturnOrder'])->name('admin_order.undo_return_order');
        Route::get('print', [\App\Admin\Controllers\AdminOrderController::class, 'printOrder'])->name('admin_order.print');
        Route::get('print-combine', [\App\Admin\Controllers\AdminOrderController::class, 'printCombineOrder'])->name('admin_order.print_combine');
        Route::get('print-note', [\App\Admin\Controllers\AdminOrderController::class, 'printNote'])->name('admin_order.print_note');
        Route::get('print_return/{id}', [\App\Admin\Controllers\AdminOrderController::class, 'printReturn'])->name('admin_order.print_return');
        Route::get('export-order-return', [\App\Admin\Controllers\AdminOrderController::class, 'exportListOrderReturn'])->name('admin_order.export_order_return');
        Route::post('merge', [\App\Admin\Controllers\AdminOrderController::class, 'combineOrder'])->name('admin_order.merge');
        Route::post('sync', [\App\Admin\Controllers\AdminOrderController::class, 'syncOrderToEInvoice'])->name('admin_order.sync_to_einvoice');
        Route::get('update-price', [\App\Admin\Controllers\AdminOrderController::class, 'updateNewPriceProductOrder'])->name('admin_order.update_price.single_order');
        Route::post('update-price-order', [\App\Admin\Controllers\AdminOrderController::class, 'updatePriceMultipleOrder'])->name('admin_order.update_price.multiple_order');
        Route::post('update-supplier', [\App\Admin\Controllers\AdminOrderController::class, 'updateSupplierOrderDetail'])->name('admin_order.update_supplier');
        Route::get('product_info', [\App\Admin\Controllers\AdminOrderController::class, 'getInfoProduct'])->name('admin_order.product_info');
        Route::get('get-product-list-create-order', [\App\Admin\Controllers\AdminOrderController::class, 'getProductListCreateOrder'])->name('admin_order.product_create_order');
        Route::get('get-product-info-create-order', [\App\Admin\Controllers\AdminOrderController::class, 'getInfoProductCreateOrder'])->name('admin_order.product_info_create_order');
        Route::post('order-create', [\App\Admin\Controllers\AdminOrderController::class, 'postCreateOrder'])->name('admin_order.order_create');
        Route::get('update-price-by-bill-date', [\App\Admin\Controllers\AdminOrderController::class, 'updateProductPriceByBillDate'])->name('admin_order.update_price_by_bill_date');
        Route::get('customer_info', [\App\Admin\Controllers\AdminOrderController::class, 'getInfoCustomer'])->name('admin_order.customer_info');
        Route::get('delete', [\App\Admin\Controllers\AdminOrderController::class, 'deleteOldProductOrder'])->name('admin_order.delete_product_old');
        Route::post('export-order-report', [\App\Admin\Controllers\AdminOrderController::class, 'exportSalesInvoiceListRealOrder'])->name('admin_order.export_sales_invoice_list_real');
        Route::post('export-order-to-einvoice', [\App\Admin\Controllers\AdminOrderController::class, 'exportExcelOrderToEinvoice'])->name('admin_order.export_excel_order_to_einvoice');
        Route::get('/detail', [\App\Admin\Controllers\AdminOrderController::class, 'detail'])->name('admin_order.show_detail');
        # Quản lý nhân viên giao hàng
        Route::get('/drive', [DriverOrderDavicorpController::class, 'index'])->name('driver.list_drive_order_davicorp');
        Route::get('/drive-detail/{id}', [DriverOrderDavicorpController::class, 'detail'])->name('driver.order_davicorp_detail');
        Route::post('/change-drive', [DriverOrderDavicorpController::class, 'changeDrive'])->name('driver.change_drive_order_davicorp');
    });

    // Quản lý báo giá
    Route::group(['prefix' => 'price'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminProductPriceController::class, 'index'])->name('admin_price.index');
        Route::get('/create', [\App\Admin\Controllers\AdminProductPriceController::class, 'create'])->name('admin_price.create');
        Route::post('/create', [\App\Admin\Controllers\AdminProductPriceController::class, 'postCreate'])->name('admin_price.create');
        Route::get('/add/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'add'])->name('admin_price.add');
        Route::post('/add/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'postAdd'])->name('admin_price.add');
        Route::get('/edit/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'edit'])->name('admin_price.edit');
        Route::get('/edit', [\App\Admin\Controllers\AdminProductPriceController::class, 'edit'])->name('admin_price.detail');
        Route::post('/edit/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'postEdit'])->name('admin_price.edit');
        Route::delete('/delete', [\App\Admin\Controllers\AdminProductPriceController::class, 'deleteList'])->name('admin_price.delete');
        Route::post('/clone', [\App\Admin\Controllers\AdminProductPriceController::class, 'cloneProductPrice'])->name('admin_price.clone');
        Route::delete('/deletedetail', [\App\Admin\Controllers\AdminProductPriceController::class, 'deleteDetail'])->name('admin_price.delete_detail');
        Route::post('/editprice1/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'postEditPrice1'])->name('admin_price.edit_price1');
        Route::post('/editprice2/{id}', [\App\Admin\Controllers\AdminProductPriceController::class, 'postEditPrice2'])->name('admin_price.edit_price2');
        Route::get('/export', [\App\Admin\Controllers\AdminProductPriceController::class, 'exportExcel'])->name('admin_price.export');
        Route::get('/import', [\App\Admin\Controllers\AdminProductPriceController::class, 'importExcel'])->name('admin_price.import');
        Route::post('/import', [\App\Admin\Controllers\AdminProductPriceController::class, 'importExcelPost'])->name('admin_price.import');
    });

    // Quản lý nhóm báo giá
    Route::group(['prefix' => 'priceboard'], function () {
        // CRUD Route
        Route::get('/', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'index'])->name('admin_priceboard.index');
        Route::get('create', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'create'])->name('admin_priceboard.create');
        Route::post('create', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'postCreate'])->name('admin_priceboard.create');
        Route::get('edit/{id}', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'edit'])->name('admin_priceboard.edit');
        Route::post('edit/{id}', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'postEdit'])->name('admin_priceboard.edit');
        Route::delete('delete', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'deleteList'])->name('admin_priceboard.delete');
        Route::post('export', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'export'])->name('admin_priceboard.export');
        Route::get('import', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'import'])->name('admin_priceboard.import');
        Route::post('import', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'postImport'])->name('admin_priceboard.import');
        // Data search route
        Route::get('dynamic_product_price', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'getDynamicPriceBoard'])->name('admin_priceboard.dynamic_search.product_price');
        Route::get('dynamic_customer', [\App\Admin\Controllers\AdminUserPriceboardController::class, 'getDynamicCustomer'])->name('admin_priceboard.dynamic_search.customer');
    });

    // Quản lý báo giá nhập NCC
    Route::group(['prefix' => 'import_priceboard'], function () {
        //Master
        Route::get('/', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'index'])->name('admin.import_priceboard.index');
        Route::get('/create', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'create'])->name('admin.import_priceboard.create');
        Route::post('/create', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postCreate'])->name('admin.import_priceboard.create');
        Route::get('/edit/{id}', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'edit'])->name('admin.import_priceboard.edit');
        Route::post('/edit/{id}', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postEdit'])->name('admin.import_priceboard.edit');
        Route::post('/delete', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'deleteList'])->name('admin.import_priceboard.delete');
        Route::post('/clone', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postClone'])->name('admin.import_priceboard.clone');
        Route::get('/import', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'import'])->name('admin.import_priceboard.import');
        Route::post('/import', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postImport'])->name('admin.import_priceboard.import');
        Route::post('/export', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'export'])->name('admin.import_priceboard.export');
        Route::get('/export-notify', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'exportNotification'])->name('admin.import_priceboard.export_notify');
        //Detail
        Route::post('/add_product', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postAddProduct'])->name('admin.import_priceboard.add_product');
        Route::post('/edit_product', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postEditProduct'])->name('admin.import_priceboard.edit_product');
        Route::post('/delete_product', [\App\Admin\Controllers\AdminImportPriceboardController::class, 'postDeleteProduct'])->name('admin.import_priceboard.delete_product');
        Route::get('/copy-data/{id}', [\App\Admin\Controllers\AdminReportController::class, 'admin.import_priceboard.copy_order']);
    });

    // Quản lý khách hàng
    Route::group(['prefix' => 'customer'], function () {
        Route::get('/import', [\App\Admin\Controllers\AdminCustomerController::class, 'import'])->name('admin_customer.import');
        Route::post('/import', [\App\Admin\Controllers\AdminCustomerController::class, 'importCustomer'])->name('admin_customer.import');
        Route::post('/export', [\App\Admin\Controllers\AdminCustomerController::class, 'export'])->name('admin_customer.export');
        Route::post('/clone', [\App\Admin\Controllers\AdminCustomerController::class, 'clone'])->name('admin_customer.clone');
        Route::post('/product-change/{id}', [\App\Admin\Controllers\AdminCustomerController::class, 'productPost'])->name('admin_customer.product_edit');
        Route::post('/product-add/{id}', [\App\Admin\Controllers\AdminCustomerController::class, 'productAddPost'])->name('admin_customer.product_add');
        Route::get('/product-remove/{id}', [\App\Admin\Controllers\AdminCustomerController::class, 'productRemovePost'])->name('admin_customer.product_remove');
    });
    // Quản lý nhà cung cấp
    Route::group(['prefix' => 'supplier'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminShopSupplierController::class, 'index'])->name('admin_supplier.index');
        Route::get('/create', [\App\Admin\Controllers\AdminShopSupplierController::class, 'create'])->name('admin_supplier.create');
        Route::post('/create', [\App\Admin\Controllers\AdminShopSupplierController::class, 'postCreate'])->name('admin_supplier.create');
        Route::get('/edit/{id}', [\App\Admin\Controllers\AdminShopSupplierController::class, 'edit'])->name('admin_supplier.edit');
        Route::post('/edit/{id}', [\App\Admin\Controllers\AdminShopSupplierController::class, 'postEdit'])->name('admin_supplier.edit');
        Route::get('/import', [\App\Admin\Controllers\AdminShopSupplierController::class, 'import'])->name('admin_supplier.import');
        Route::post('/import', [\App\Admin\Controllers\AdminShopSupplierController::class, 'importPost'])->name('admin_supplier.import');
        Route::get('/export', [\App\Admin\Controllers\AdminShopSupplierController::class, 'export'])->name('admin_supplier.export');
        Route::delete('/delete', [\App\Admin\Controllers\AdminShopSupplierController::class, 'deleteList'])->name('admin_supplier.delete');
    });

    // Quản lý khu vực
    Route::group(['prefix' => 'zone'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminZoneController::class, 'index'])->name('admin_zone.index');
        Route::post('/', [\App\Admin\Controllers\AdminZoneController::class, 'process'])->name('admin_zone.process');
        Route::delete('/delete', [\App\Admin\Controllers\AdminZoneController::class, 'deleteList'])->name('admin_zone.delete');
    });

    // Quản lý khóa ngày đặt hàng trong kỳ nghỉ lễ.
    Route::group(['prefix' => 'holiday'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminHolidayController::class, 'index'])->name('admin_holiday.index');
        Route::post('/update', [\App\Admin\Controllers\AdminHolidayController::class, 'update'])->name('admin_holiday.update');
        Route::post('/create', [\App\Admin\Controllers\AdminHolidayController::class, 'store'])->name('admin_holiday.create');
        Route::post('/change-status', [\App\Admin\Controllers\AdminHolidayController::class, 'changeStatus'])->name('admin_holiday.change_status');
        Route::delete('/delete', [\App\Admin\Controllers\AdminHolidayController::class, 'delete'])->name('admin_holiday.delete');
        Route::get('/test', [\App\Admin\Controllers\AdminHolidayController::class, 'checkngaythu7cn']);
    });

    // Quản lý hóa đơn điện tử
    Route::group(['prefix' => 'einvoice'], function () {
        //Master
        Route::get('/', [\App\Admin\Controllers\AdminEInvoiceController::class, 'index'])->name('admin.einvoice.index');
        Route::delete('/', [\App\Admin\Controllers\AdminEInvoiceController::class, 'multipleDeleteInvoice'])->name('admin.einvoice.delete');
        Route::post('/export-virtual-report-list', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportSalesInvoiceListVirtualOrder'])->name('admin.einvoice.export_sales_invoice_list_virtual');
        Route::post('/print-virtual-report-list', [\App\Admin\Controllers\AdminEInvoiceController::class, 'printSalesInvoiceListVirtualOrder'])->name('admin.einvoice.print_sales_invoice_list_virtual');
        Route::get('/detail/{id}', [\App\Admin\Controllers\AdminEInvoiceController::class, 'detailEInvoice'])->name('admin.einvoice.detail');
        Route::delete('/delete', [\App\Admin\Controllers\AdminEInvoiceController::class, 'multipleDeleteInvoice'])->name('admin.einvoice.delete');
        Route::post('/merge', [\App\Admin\Controllers\AdminEInvoiceController::class, 'combineEinvoice'])->name('admin.einvoice.merge');
        Route::post('/send-robot', [\App\Admin\Controllers\AdminEInvoiceController::class, 'sendRobot'])->name('admin.einvoice.send_robot');
        Route::post('/cancel-sync', [\App\Admin\Controllers\AdminEInvoiceController::class, 'cancelSync'])->name('admin.einvoice.cancel_sync');
        Route::post('/acceptance-report', [\App\Admin\Controllers\AdminEInvoiceController::class, 'acceptanceReport'])->name('admin.einvoice.acceptance_report');
        Route::post('/payment-offer-report', [\App\Admin\Controllers\AdminEInvoiceController::class, 'paymentOfferReport'])->name('admin.einvoice.payment_offer_report');
        Route::post('/payment-offer-report-print', [\App\Admin\Controllers\AdminEInvoiceController::class, 'paymentOfferReportPrint'])->name('admin.einvoice.payment_offer_report_print');
        Route::post('/acceptance-report-print', [\App\Admin\Controllers\AdminEInvoiceController::class, 'acceptanceReportPrint'])->name('admin.einvoice.acceptance_report_print');
        Route::post('/determine', [\App\Admin\Controllers\AdminEInvoiceController::class, 'postDetermineVolume'])->name('admin.davicook.determine_finished_volume');
        Route::post('/intro-davicorp', [\App\Admin\Controllers\AdminEInvoiceController::class, 'postIntrodavicorpForm'])->name('admin.davicook.intro_davicorp_form');
        Route::get('/export-pdf', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportPdfDetermineVolume'])->name('admin.einvoice.determine.volume.pdf');
        Route::get('/export-excel', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportExcelDetermineVolume'])->name('admin.einvoice.determine.volume.excel');
        Route::get('/intro-davicorp-excel', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportExcelIntrodavicorp'])->name('admin.einvoice.intro_davicorp_form.excel');
        Route::get('/intro-davicorp-pdf', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportPdfIntrodavicorp'])->name('admin.einvoice.intro_davicorp_form.pdf');
        Route::post('/print-virtual-report-detail', [\App\Admin\Controllers\AdminEInvoiceController::class, 'printSalesInvoiceDetailVirtualOrder'])->name('admin.einvoice.print_sales_invoice_detail_virtual');
        Route::post('export-virtual-report-detail', [\App\Admin\Controllers\AdminEInvoiceController::class, 'exportSalesInvoiceDetailVirtualOrder'])->name('admin.einvoice.export_sales_invoice_detail_virtual');
        Route::get('/import', [\App\Admin\Controllers\AdminEInvoiceController::class, 'import'])->name('admin.einvoice.import');
        Route::post('/import', [\App\Admin\Controllers\AdminEInvoiceController::class, 'importPost'])->name('admin.einvoice.import');
        Route::post('/delete-detail', [\App\Admin\Controllers\AdminEInvoiceController::class, 'deleteItemDetail'])->name('admin.einvoice.delete_item_detail');
        Route::post('/update', [\App\Admin\Controllers\AdminEInvoiceController::class, 'updateEinvoice'])->name('admin.einvoice.update');
        Route::post('/create', [\App\Admin\Controllers\AdminEInvoiceController::class, 'createItemEinvoice'])->name('admin.einvoice.create_item');
        Route::get('/einvoice-product-info', [\App\Admin\Controllers\AdminEInvoiceController::class, 'getInfoProduct'])->name('admin_einvoice.product_info');
    });


    // ------------------------------------- DAVICOOK -----------------------------------------------
    Route::group(['prefix' => 'davicook'], function () {
        // Quản lý món ăn
        route::group(['prefix' => 'dish'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminDishController::class, 'index'])->name('admin.davicook_dish.index');
            Route::get('/create', [\App\Admin\Controllers\AdminDishController::class, 'create'])->name('admin.davicook_dish.create');
            Route::post('/create', [\App\Admin\Controllers\AdminDishController::class, 'postCreate'])->name('admin.davicook_dish.create');
            Route::get('/edit/{id}', [\App\Admin\Controllers\AdminDishController::class, 'edit'])->name('admin.davicook_dish.edit');
            Route::put('/edit/{id}', [\App\Admin\Controllers\AdminDishController::class, 'postEdit'])->name('admin.davicook_dish.edit');
            Route::delete('/delete', [\App\Admin\Controllers\AdminDishController::class, 'deleteList'])->name('admin.davicook_dish.delete');
            Route::get('/export', [\App\Admin\Controllers\AdminDishController::class, 'exportExcel'])->name('admin.davicook_dish.export_excel');
            Route::get('/import', [\App\Admin\Controllers\AdminDishController::class, 'importExcel'])->name('admin.davicook_dish.import_excel');
            Route::post('/import', [\App\Admin\Controllers\AdminDishController::class, 'postImportExcel'])->name('admin.davicook_dish.import_excel');
        });

        // Quản lý khách hàng
        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'index'])->name('admin.davicook_customer.index');
            Route::get('/create', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'create'])->name('admin.davicook_customer.create');
            Route::post('/create', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'postCreate'])->name('admin.davicook_customer.create');
            Route::get('/edit/{id}', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'edit'])->name('admin.davicook_customer.edit');
            Route::put('/edit/{id}', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'postEdit'])->name('admin.davicook_customer.edit');
            Route::post('/add/{id}', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'postAddProductSupplier'])->name('admin.davicook_customer.add_product_supplier');
            Route::post('/update/{id}', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'postUpdateProductSupplier'])->name('admin.davicook_customer.update_product_supplier');
            Route::get('/list-ingredient-dish', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'getListIngredientDish'])->name('admin.davicook_customer.get_list_ingredient_dish');
            Route::delete('/delete-customer', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'deleteList'])->name('admin.davicook_customer.delete_list_customer');
            Route::delete('/delete-dish-customer', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'deleteListDishCustomer'])->name('admin.davicook_customer.delete_list_dish_customer');
            Route::get('/remove-product/{id}', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'productCustomerRemove'])->name('admin.davicook_customer.remove_product');
            Route::get('/clone-customer', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'cloneCustomer'])->name('admin.davicook_customer.clone_customer');
            Route::post('/create-dish-customer', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'createMenuDishCustomer'])->name('admin.davicook_customer.add_ingredient_dish');
            Route::post('/clone-dish-customer', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'cloneMenuDishCustomer'])->name('admin.davicook_customer.clone_ingredient_dish');
            Route::post('/update-ingredient-dish', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'updateListIngredientDish'])->name('admin.davicook_customer.update_list_ingredient_dish');
            Route::get('/export', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'exportExcelListCustomer'])->name('admin.davicook_customer.export_list_customer');
            Route::get('/import', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'importExcel'])->name('admin.davicook_customer.import_list_customer');
            Route::post('/import', [\App\Admin\Controllers\AdminDavicookCustomerController::class, 'handelImport'])->name('admin.davicook_customer.import_list_customer');
        });

        // Quản lý đơn hàng cook
        Route::group(['prefix' => 'order'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'index'])->name('admin.davicook_order.index');
            Route::get('/create', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'create'])->name('admin.davicook_order.create');
            Route::get('print_return/{id}', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'printOrderReturn'])->name('admin_davicook_order.print_return');
            Route::get('export-order-return', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'exportListOrderReturn'])->name('admin_davicook_order.export_order_return');
            Route::get('/create-essential-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'createEssentialOrder'])->name('admin.davicook_order.create_essential_order');
            Route::post('/order-create', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postCreate'])->name('admin.davicook_order.order_create');
            Route::post('/essential-order-create', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postCreateEssentialOrder'])->name('admin.davicook_order.essential_order_create');
            Route::get('/print-multiple', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'printMultipleOrderDavicook'])->name('admin.davicook_order.print_multiple');
            Route::get('/print-combine-multiple', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'printCombineMultipleOrderDavicook'])->name('admin.davicook_order.print_combine_multiple');
            Route::get('/detail/{id}', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'detail'])->name('admin.davicook_order.detail');
            Route::get('/detail', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'detail'])->name('admin.davicook_order.show_detail');
            Route::get('/essentail-order-detail/{id}', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'essentialOrderDetail'])->name('admin.davicook_order.essential_order_detail');
            Route::get('/essentail-order-detail', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'essentialOrderDetail'])->name('admin.davicook_order.show_essential_order_detail');
            Route::post('/update', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postOrderUpdate'])->name('admin.davicook_order.order_update');
            Route::get('/customer_info', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getInfoCustomer'])->name('admin.davicook_order.customer_info');
            Route::get('/update_customer_info', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'updateInfoCustomer'])->name('admin.davicook_order.update_customer_info');
            Route::post('/detail-update', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postOrderDetailUpdate'])->name('admin.davicook_order.detail_update');
            Route::get('/dish_info', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getInfoDish'])->name('admin.davicook_order.dish_info');
            Route::get('/get_product_dish', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getProductDishCreateOrder'])->name('admin.davicook_order.get_product_dish_create_order');
            Route::get('/get-product-dish-extra-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getProductDishExtraOrder'])->name('admin.davicook_order.get_product_dish_create_extra_order');
            Route::get('/get-dish', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getDishByCutomerCreateOrder'])->name('admin.davicook_order.get_dish_by_customer_create_order');
            Route::get('/get-product-essential-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getProductEssentialOrder'])->name('admin.davicook_order.get_product_create_essential_order');
            Route::get('/get-dish-extra-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getDishByCutomerCreateExtraOrder'])->name('admin.davicook_order.get_dish_by_customer_create_extra_order');
            Route::get('/add-product-extra-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'addProductExtraOrder'])->name('admin.davicook_order.add_product_create_extra_order');
            Route::get('/get-product-info-extra-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'getProductInfoExtraOrder'])->name('admin.davicook_order.get_product_info_create_extra_order');
            Route::post('/detail-add', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postAddItem'])->name('admin.davicook_order.detail_add');
            Route::post('/essential-order-detail-add', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postAddItemEssentialOrder'])->name('admin.davicook_order.essential_order_detail_add');
            Route::post('/detail-add-extra-order', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postAddItemExtraOrder'])->name('admin.davicook_order.detail_add_extra_order');
            Route::post('/detail-delete', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postDeleteItem'])->name('admin.davicook_order.detail_delete');
            Route::post('/essential-order-detail-delete', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postDeleteItemEssentialOrder'])->name('admin.davicook_order.essential_order_detail_delete');
            Route::post('/all-detail-delete', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postDeleteAllItem'])->name('admin.davicook_order.delete_all_detail');
            Route::get('/update-price', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'updateNewPriceProductOrder'])->name('admin.davicook_order.update_price');
            Route::post('/update-supplier', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'updateSupplierOrderDetail'])->name('admin.davicook_order.update_supplier');
            Route::get('/update-price-by-delivery-time', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'updateImportPriceByDeliveryTime'])->name('admin.davicook_order.update_price_by_delivery_time');
            Route::get('return/{id}', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'returnOrder'])->name('admin.davicook_order.return');
            Route::delete('/delete', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'deleteDavicookOrder'])->name('admin.davicook_order.delete');
            Route::post('return/{id}', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'postReturn'])->name('admin.davicook_order.returnPost');
            Route::post('undo-order-essential-return', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'undoReturnEssentialOrder'])->name('admin.davicook_order.undoReturnEssentialOrder');
            Route::post('undo-order-return', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'undoReturnOrder'])->name('admin.davicook_order.undoReturnOrder');
            Route::post('change-status-product-dry', [\App\Admin\Controllers\AdminDavicookOrderController::class, 'changeStatusOrderDavicookProductDry'])->name('admin.davicook_order.change_status_order_product_dry');
        });
        Route::group(['prefix' => 'menu-card'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'index'])->name('admin.davicook_menu_card.index');
            Route::delete('/delete', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'delete'])->name('admin.davicook_menu_card.delete');
            Route::delete('/delete-details', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'deleteMenuCardDetail'])->name('admin.davicook_menu_card.detail_card_delete');
            Route::delete('/delete-dish-of-details', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'deleteDishOfMenuCardDetail'])->name('admin.davicook_menu_card.dish_of_menu_card_delete');
            Route::get('/create-for-student', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'createMenuCardForStudent'])->name('admin.davicook_menu_card.create_for_student');
            Route::get('/create-for-teacher', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'createMenuCardForTeacher'])->name('admin.davicook_menu_card.create_for_teacher');
            Route::get('/get-dish-by-customer', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'getDishByCustomer'])->name('admin.davicook_menu_card.get_dish_by_customer');
            Route::get('/get-product-by-dish', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'getProductByDish'])->name('admin.davicook_menu_card.get_product_by_dish');
            Route::post('/store', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'storeMenuCard'])->name('admin.davicook_menu_card.store_menu_card');
            Route::get('/edit', [App\Admin\Controllers\AdminDavicookMenuCardController::class, 'editMenuCard'])->name('admin.davicook_menu_card.edit_menu_card');
            Route::post('/store-dish-for-detail', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'storeDishForMenuCardDetail'])->name('admin.davicook_menu_card.store_dish_for_menu_card');
            Route::post('/store-menu-card-for-detail', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'storeNewMenuCardForDetail'])->name('admin.davicook_menu_card.store_new_menu_card_for_display_edit');
            Route::post('/change-number-of-servings', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'updateNumberOfServings'])->name('admin.davicook_menu_card.change_number_of_servings');
            Route::post('/change-dish-for-menu-card', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'updateChangDishForMenuCard'])->name('admin.davicook_menu_card.change_dish_for_menu_card');
            Route::post('/update-item-detail', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'updateItemMenuCardDetail'])->name('admin.davicook_menu_card.update_item_menu_card_detail');
            Route::post('/order-davicook-sync', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'orderDavicookSync'])->name('admin.davicook_menu_card.create_order_davicook');
            Route::post('/combine', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'combineMenuCard'])->name('admin.davicook_menu_card.combine');
            Route::get('/update-price/{id}', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'updateImportPriceProduct'])->name('admin.davicook_menu_card.update_import_price');
            Route::post('/clone', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'cloneMenuCard'])->name('admin.davicook_menu_card.clone');
            Route::get('/export-excel', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'exportExcelMenuCard'])->name('admin.davicook_menu_card.export_excel');
            Route::get('/preview-pdf', [\App\Admin\Controllers\AdminDavicookMenuCardController::class, 'previewPdfMenuCard'])->name('admin.davicook_menu_card.preview_pdf');
        });
        # Quản lý giao hàng daviook
        Route::get('/drive', [DriverOrderDavicookController::class, 'index'])->name('driver.list_drive_order_davicook');
        Route::get('/drive-detail/{id}', [DriverOrderDavicookController::class, 'detail'])->name('driver.order_davicook_detail');
        Route::post('/change-drive', [DriverOrderDavicookController::class, 'changeDrive'])->name('driver.change_drive_order_davicook');
    });


    // ------------------------------------- BÁO CÁO THỐNG KÊ -----------------------------------------------
    Route::group(['prefix' => 'report'], function () {
        // Báo cáo doanh thu
        Route::group(['prefix' => 'revenue'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportController::class, 'index'])->name('admin_report_revenue.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportController::class, 'exportExcel'])->name('admin_report_revenue.export_excel');
            Route::get('/savePdf', [\App\Admin\Controllers\AdminReportController::class, 'saveRevenuePdf'])->name('admin_report_revenue.export_pdf');
        });

        // Báo cáo 2 chỉ tiêu
        Route::group(['prefix' => '2target'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportTargetController::class, 'target'])->name('admin_report_2target.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportTargetController::class, 'exportExcelTarget'])->name('admin_report_2target.export_excel');
            Route::get('/savePdf', [\App\Admin\Controllers\AdminReportTargetController::class, 'saveTargetPdf'])->name('admin_report_2target.export_pdf');
            Route::get('/save-file-to-storage', [\App\Admin\Controllers\AdminReportTargetController::class, 'storeStorageFileExcel'])->name('admin_report_2target.save_file_to_storage');
            Route::get('/download-file', [\App\Admin\Controllers\AdminReportTargetController::class, 'downloadFileZip'])->name('admin_report_2target.download_file_zip');
            Route::post('/get-and-chunk-data', [\App\Admin\Controllers\AdminReportTargetController::class, 'getAndChunkData'])->name('admin_report_2target.get_and_chunk_data');
            Route::post('/export-all-item', [\App\Admin\Controllers\AdminReportTargetController::class, 'exportAllItem'])->name('admin_report_2target.create_order_export_all_item');
            Route::post('/get-list-data-modal', [\App\Admin\Controllers\AdminReportTargetController::class, 'getListDataExportModal'])->name('admin_report_2target.get_list_data_modal');
        });

        Route::group(['prefix' => '2target-extra'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportTargetExtraController::class, 'index'])->name('admin_report_2target_extra.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportTargetExtraController::class, 'export'])->name('admin_report_2target_extra.export_excel');
            Route::get('/savePdf', [\App\Admin\Controllers\AdminReportTargetExtraController::class, 'printPdf'])->name('admin_report_2target_extra.print_pdf');
            Route::post('/update-detail', [\App\Admin\Controllers\AdminReportTargetExtraController::class, 'postUpdateDetail'])->name('admin_report_2target_extra.update_detail');
        });

        // Báo cáo ghi chú
        Route::group(['prefix' => 'note'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportNoteController::class, 'index'])->name('admin_report_note.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportNoteController::class, 'exportExcelNote'])->name('admin_report_note.export_excel');
            Route::get('/savePdf', [\App\Admin\Controllers\AdminReportNoteController::class, 'saveNotePdf'])->name('admin_report_note.export_pdf');
        });

        // Chênh lệch bếp ăn
        Route::group(['prefix' => 'quantity_diference'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'reportMealDifference'])->name('admin_report_quantity_diference.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'exportExcelReportMealDifference'])->name('admin_report_quantity_diference.export_excel');
            Route::get('/savePdf', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'exportPDFReportMealDifference'])->name('admin_report_quantity_diference.export_pdf');
            Route::get('/export-detail/{id}', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'exportExcelReportMealDifferenceDetail'])->name('admin_report_quantity_diference.detail.export_excel');
            Route::get('/savePdf-detail/{id}', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'exportPDFReportMealDifferenceDetail'])->name('admin_report_quantity_diference.detail.export_pdf');
            Route::get('/detail/{id}', [\App\Admin\Controllers\AdminReportMealDifferenceController::class, 'reportMealDifferenceDetail'])->name('admin_report_quantity_diference.detail');
        });

        // In tem
        Route::group(['prefix' => 'print_stamp'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportController::class, 'printStampReport'])->name('admin_report_print_stamp.index');
            Route::get('/preview-pdf', [\App\Admin\Controllers\AdminReportController::class, 'previewStampPdf'])->name('admin_report_print_stamp.preview_pdf');
            Route::get('/export', [\App\Admin\Controllers\AdminReportController::class, 'exportExcelStamp'])->name('admin_report_print_stamp.export_excel');
            Route::get('/download-file-pdf', [\App\Admin\Controllers\AdminReportController::class, 'downloadFileStampPdf'])->name('admin_report_print_stamp.download_file_pdf');
        });
        // In tem bổ sung
        Route::group(['prefix' => 'print_stamp_extra'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminStampAddReportController::class, 'printStampExtraReport'])->name('admin_report_print_stamp_extra.index');
            Route::get('/preview-pdf', [\App\Admin\Controllers\AdminStampAddReportController::class, 'previewStampExtraPdf'])->name('admin_report_print_stamp_extra.preview_pdf');
            Route::get('/export', [\App\Admin\Controllers\AdminStampAddReportController::class, 'exportExcelStampExtra'])->name('admin_report_print_stamp_extra.export_excel');
            Route::get('/download-file-pdf', [\App\Admin\Controllers\AdminStampAddReportController::class, 'downloadFileStampExtraPdf'])->name('admin_report_print_stamp_extra.download_file_pdf');
        });

        // Trả hàng
        Route::group(['prefix' => 'return_order'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminReportReturnOrderController::class, 'index'])->name('admin_report_return_order.index');
            Route::get('/export', [\App\Admin\Controllers\AdminReportReturnOrderController::class, 'exportExcelReturnOrder'])->name('admin_report_return_order.export_excel');
        });

        // Nhập hàng 2 chỉ tiêu
        Route::group(['prefix' => 'import_2target'], function () {
            // Mẫu 1
            Route::group(['prefix' => 'template_1'], function () {
                Route::get('/', [\App\Admin\Controllers\AdminReportController::class, 'importGoodsReport'])->name('admin_report_import_2target.template_1.index');
                Route::get('/savePdf', [\App\Admin\Controllers\AdminReportController::class, 'exportPdfImportPriceReport'])->name('admin_report_import_2target.template_1.export_pdf');
                Route::get('/export', [\App\Admin\Controllers\AdminReportController::class, 'exportExcelImportPriceReport'])->name('admin_report_import_2target.template_1.export_excel');
            });


            // Mẫu 2
            Route::group(['prefix' => 'template_2'], function () {
                Route::get('/', [\App\Admin\Controllers\AdminReportController::class, 'importDetailReport'])->name('admin_report_import_2target.template_2.detail');
                Route::get('/savePdf', [\App\Admin\Controllers\AdminReportController::class, 'exportExcelImportDetailReport'])->name('admin_report_import_2target.template_2.export_excel');
                Route::get('/export', [\App\Admin\Controllers\AdminReportController::class, 'exportPdfImportDetailReport'])->name('admin_report_import_2target.template_2.export_pdf');
            });
        });
    });

    // ------------------------------------- ĐIỂM THƯỞNG -----------------------------------------------git
    Route::group(['prefix' => 'point'], function () {
        Route::group(['prefix' => 'view'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminRewardController::class, 'index'])->name('admin_point_view.index');
            Route::get('/export_excel', [\App\Admin\Controllers\AdminRewardController::class, 'exportExcel'])->name('admin_point_view.export_excel');
            Route::get('/export_pdf', [\App\Admin\Controllers\AdminRewardController::class, 'exportPDF'])->name('admin_point_view.export_pdf');
            Route::get('get-history', [\App\Admin\Controllers\AdminRewardController::class, 'getHistory'])->name('admin_point_view.history');
            Route::get('export-point-history', [\App\Admin\Controllers\AdminRewardController::class, 'exportPointHistory'])->name('admin_point_view.export_history');
            Route::post('update-actual-point', [\App\Admin\Controllers\AdminRewardController::class, 'updateActualPoint'])->name('admin_point_view.update_actual_point');
            Route::get('test-point', [\App\Admin\Controllers\AdminRewardController::class, 'testPoint'])->name('admin_point_setting.principle.index.test');
        });

        Route::group(['prefix' => 'setting'], function () {
            //Reward tier
            Route::group(['prefix' => 'tier'], function () {
                Route::get('/', [\App\Admin\Controllers\AdminRewardTierController::class, 'index'])->name('admin_point_setting.tier.index');
                Route::post('/', [\App\Admin\Controllers\AdminRewardTierController::class, 'process'])->name('admin_point_setting.tier.create');
                Route::delete('/', [\App\Admin\Controllers\AdminRewardTierController::class, 'deleteList'])->name('admin_point_setting.tier.delete');
            });
            //Reward principle
            Route::group(['prefix' => 'principle'], function () {
                Route::get('/for-day', [\App\Admin\Controllers\AdminRewardPrincipleController::class, 'principle'])->name('admin_point_setting.principle.index');
                Route::get('/for-weekend', [\App\Admin\Controllers\AdminRewardPrincipleController::class, 'showPointForWeekend'])->name('admin_point_setting.principle.for_weekend');
                Route::post('update', [\App\Admin\Controllers\AdminRewardPrincipleController::class, 'postPrinciple'])->name('admin_point_setting.principle.edit');
            });
        });
    });
// ------------------------------------- ĐIỂM THƯỞNG -----------------------------------------------
    Route::controller(AdminRatingController::class)->prefix("rating")->group(function () {
        Route::get("/", "index")->name("admin.rating.index");
        Route::get("detail", "detail")->name("admin.rating.detail");
        Route::get("export_excel", "exportExcel")->name("admin.rating.export_excel");
        Route::get("export_pdf", "exportPDF")->name("admin.rating.export_pdf");
    });
    // ------------------------------------- QUẢN LÝ THÔNG BÁO -----------------------------------------------
    Route::group(['prefix' => 'notify'], function () {
        // Lịch sử thông báo
        Route::group(['prefix' => 'history'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminNotifyController::class, 'index'])->name('admin_notify_history.index');
            Route::get('/notification/{id}/seen', [\App\Admin\Controllers\AdminNotifyController::class, 'customerRead'])->name('admin_notify_history.read');
            Route::get('/detail/{id}', [\App\Admin\Controllers\AdminNotifyController::class, 'detail'])->name('admin_notify_history.detail');
            Route::delete('/delete', [\App\Admin\Controllers\AdminNotifyController::class, 'deleteList'])->name('admin_notify_history.delete');
            Route::post('/read-tick', [\App\Admin\Controllers\AdminNotifyController::class, 'readTick'])->name('admin_notify_history.readtick');
            Route::post('/load-more', [\App\Admin\Controllers\AdminNotifyController::class, 'getListPaging'])->name('admin_notify_history.get_list_paging');
            Route::get('/export', [\App\Admin\Controllers\AdminNotifyController::class, 'exportNotification'])->name('admin_notify_history.export');
            Route::get('/export-change-import-price', [\App\Admin\Controllers\AdminNotifyController::class, 'exportChangeImportPrice'])->name('admin_notify_history.export_import_price');
        });

        // Thông báo thủ công
        Route::group(['prefix' => 'manual'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminNotifyController::class, 'create'])->name('admin_notify_manual.index');
            Route::post('/create', [\App\Admin\Controllers\AdminNotifyController::class, 'doCreate'])->name('admin_notify_manual.create');
            Route::get('/get/search', [\App\Admin\Controllers\AdminNotifyController::class, 'search'])->name('admin_notify_manual.load_customer');
        });

        // Thông báo tự động
        Route::group(['prefix' => 'automatic'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'index'])->name('admin_notify_automatic.index');
            Route::get('/create', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'createMessages'])->name('admin_notify_automatic.create');
            Route::post('/create', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'postcreateMessages'])->name('admin_notify_automatic.create');
            Route::get('/detail/{id}', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'detail'])->name('admin_notify_automatic.detail');
            Route::put('/detail/{id}', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'updateDetail'])->name('admin_notify_automatic.detail');
            Route::delete('/delete', [\App\Admin\Controllers\AdminNotifyMessagesController::class, 'deleteList'])->name('admin_notify_automatic.delete');
        });

        // Mẫu thông báo
        Route::group(['prefix' => 'template'], function () {
            Route::get('/', [\App\Admin\Controllers\AdminNotifyController::class, 'notifyTemplates'])->name('admin_notify_template.index');
            Route::get('/create', [\App\Admin\Controllers\AdminNotifyController::class, 'createTemplates'])->name('admin_notify_template.create');
            Route::post('/create', [\App\Admin\Controllers\AdminNotifyController::class, 'postCreateTemplates'])->name('admin_notify_template.create');
            Route::get('/edit/{id}', [\App\Admin\Controllers\AdminNotifyController::class, 'editTemplates'])->name('admin_notify_template.edit');
            Route::put('/edit/{id}', [\App\Admin\Controllers\AdminNotifyController::class, 'postEditTemplates'])->name('admin_notify_template.edit');
            Route::delete('/delete', [\App\Admin\Controllers\AdminNotifyController::class, 'deleteListTemplates'])->name('admin_notify_template.delete');
        });
        // Extend tài khoản và phân quyền
        Route::group(['prefix' => 'user'], function () {
            Route::post('/edit-order-lock-time', [\App\Admin\Controllers\Auth\UsersController::class, "editLockTimePost"])->name('admin_user.lock-time-edit');
        });


    });
    // ------------------------------------- QUẢN LÝ KHO-----------------------------------------------
    # Báo cáo kho

    Route::group(['prefix' => 'warehouse-report'], function () {
        # Đơn nhập
        Route::group(['prefix' => 'import'], function () {
            Route::get('/', [AdminReportImportOrderController::class, 'index'])->name('warehouse_report_import.index');
            Route::get('/print-pdf', [AdminReportImportOrderController::class, 'printPdf'])->name('warehouse_report_import.print');
            Route::get('/export-excel', [AdminReportImportOrderController::class, 'exportExcel'])->name('warehouse_report_import.export');
        });

        #Đơn xuất
        Route::group(['prefix' => 'export'], function () {
            Route::get('/', [AdminReportExportOrderController::class, 'index'])->name('warehouse_report_export.index');
            Route::get('/print-pdf', [AdminReportExportOrderController::class, 'printPdf'])->name('warehouse_report_export.print');
            Route::get('/export-excel', [AdminReportExportOrderController::class, 'exportExcel'])->name('warehouse_report_export.export');
        });

        Route::group(['prefix' => 'card'], function () {
            Route::get('/', [AdminReportWarehouseCardController::class, 'index'])->name('warehouse_card_report.index');
            Route::get('/print-pdf', [AdminReportWarehouseCardController::class, 'printPdf'])->name('warehouse_card_report.print');
            Route::get('/export-excel', [AdminReportWarehouseCardController::class, 'exportExcel'])->name('warehouse_card_report.export');
        });
        //Báo cáo nợ hàng
        Route::group(['prefix' => 'product-dept'], function () {
            Route::get('/', [AdminReportWarehouseProductDeptController::class, 'index'])->name('warehouse_report_product_dept.index');
            Route::get('/print-pdf', [AdminReportWarehouseProductDeptController::class, 'printPdf'])->name('warehouse_report_product_dept.print');
            Route::get('/export-excel', [AdminReportWarehouseProductDeptController::class, 'exportExcel'])->name('warehouse_report_product_dept.export');
            Route::get('/print-tem', [AdminReportWarehouseProductDeptController::class, 'previewStampPdf'])->name('warehouse_report_product_dept.print_tem');
            Route::post('/update-dept', [AdminReportWarehouseProductDeptController::class, 'updateDept'])->name('warehouse_report_product_dept.update_dept');
            Route::post('/create-export', [AdminReportWarehouseProductDeptController::class, 'createExportOrder'])->name('warehouse_report_product_dept.create_export_order');
            Route::get('/get-data-history', [AdminReportWarehouseProductDeptController::class, 'getDataShowHistory'])->name('warehouse_report_product_dept.getDataShowHistory');
        });
        #Báo cáo nhập xuất tồn
        Route::group(['prefix' => 'product-stock'], function () {
            Route::get('/', [AdminReportWarehouseProductStockController::class, 'index'])->name('warehouse_product_stock.index');
            Route::get('/print-pdf', [AdminReportWarehouseProductStockController::class, 'printPdf'])->name('warehouse_product_stock.print');
            Route::get('/export-excel', [AdminReportWarehouseProductStockController::class, 'exportExcel'])->name('warehouse_product_stock.export');
        });
         #Báo cáo nhập hàng với hàng trả
         Route::group(['prefix' => 'return-import'], function () {
            Route::get('/', [AdminReportReturnImportOrderController::class, 'index'])->name('warehouse_report_return_import.index');
            Route::get('/print-pdf', [AdminReportReturnImportOrderController::class, 'printPdf'])->name('warehouse_report_return_import.print');
            Route::get('/export-excel', [AdminReportReturnImportOrderController::class, 'exportExcel'])->name('warehouse_report_return_import.export');
            Route::post('/getDataShowPopup', [AdminReportReturnImportOrderController::class, 'getDataShowPopup'])->name('warehouse_report_return_import.getDataShowPopup');
            Route::post('/createImportOrderByReportReturn', [AdminReportReturnImportOrderController::class, 'createImportOrderByReportReturn'])->name('warehouse_report_return_import.create_order_import');
        });
    });

    //    Danh sách kho
    Route::group(['prefix' => 'warehouse-list'], function () {
        Route::get('/', [\App\Admin\Controllers\AdminWarehouseController::class, 'index'])->name('admin_warehouse.index');
        Route::post('/create', [\App\Admin\Controllers\AdminWarehouseController::class, 'postCreate'])->name('admin_warehouse.create');
        Route::get('/edit/{id}', [\App\Admin\Controllers\AdminWarehouseController::class, 'edit'])->name('admin_warehouse.edit');
        Route::post('/edit/{id}', [\App\Admin\Controllers\AdminWarehouseController::class, 'postEdit'])->name('admin_warehouse.edit');
        Route::delete('/delete', [\App\Admin\Controllers\AdminWarehouseController::class, 'deleteList'])->name('admin_warehouse.delete');
    });
    Route::group(['prefix' => 'export'], function () {
        Route::get('/', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "index"])->name('admin_warehouse_export.index');
        Route::get('/create', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "create"])->name('admin_warehouse_export.create');
        Route::post('/create', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "postCreate"])->name('admin_warehouse_export.create');
        Route::get('/get-list-product', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "getListProduct"])->name('admin_warehouse_export.getListProduct');
        Route::get('/edit/{id}', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "edit"])->name('admin_warehouse_export.edit');
        Route::post('/edit/{id}', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, "postEdit"])->name('admin_warehouse_export.edit');
        Route::post('/clone-order', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'cloneExportOrder'])->name('admin_warehouse_export.clone_order');
        Route::post('/update', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'update'])->name('order_export.update');
        Route::post('/update-customer', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'updateCustomer'])->name('admin_warehouse_export.update_customer');
        Route::delete('/delete', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'deleteExportOrder'])->name('order_export.delete');
        Route::post('/delete-detail', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'deleteExportOrderDetail'])->name('order_export.delete_detail');
        Route::get('/printPdf', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'printPdf'])->name('admin_warehouse_export.print');
        Route::get('/export-excel', [\App\Admin\Controllers\Warehouse\AdminOrderExportController::class, 'exportExcel'])->name('admin_warehouse_export.export_excel');
    });
    // Tạo đơn nhập kho
    Route::group(['prefix' => 'order-import'], function () {
        Route::get('/', [AdminOrderImportController::class, 'index'])->name('order_import.index');
        Route::get('/create', [AdminOrderImportController::class, 'create'])->name('order_import.create');
        Route::get('/edit/{id}', [AdminOrderImportController::class, 'edit'])->name('order_import.edit');
        Route::get('/printImportOrder', [AdminOrderImportController::class, 'printImportOrder'])->name('order_import.print');
        Route::get('/exportImportOrder', [AdminOrderImportController::class, 'exportExcelImportOrder'])->name('order_import.export');
        Route::get('/return/{id}', [AdminOrderImportController::class, 'return'])->name('order_import.return');
        Route::get('/get-productInfo-and-price', [AdminOrderImportController::class, 'getProductInfoAndImportPrice'])->name('order_import.get_productInfo_price');
        Route::post('/clone', [AdminOrderImportController::class, 'cloneImportOrder'])->name('order_import.clone');
        Route::post('/add-new-item', [AdminOrderImportController::class, 'addItemProductDetail'])->name('order_import.add_new_item');
        Route::post('/store', [AdminOrderImportController::class, 'store'])->name('order_import.store');
        Route::post('/update', [AdminOrderImportController::class, 'update'])->name('order_import.update');
        Route::post('/storeReturn/{id}', [AdminOrderImportController::class, 'storeReturn'])->name('order_import.store_return');
        Route::post('/undoImportDetail', [AdminOrderImportController::class, 'undoImportDetail'])->name('order_import.undo_import_detail');
        Route::post('/update-price-detail/{id}', [AdminOrderImportController::class, 'updatePriceProduct'])->name('order_import.update_import_price');
        Route::post('/getDataChangeProduct', [AdminOrderImportController::class, 'getDataChangeProduct'])->name('order_import.get_data_change_product');
        Route::post('/storeChangeProduct', [AdminOrderImportController::class, 'storeChangeProduct'])->name('order_import.store_change_product');
        Route::post('/sync-order-import', [AdminOrderImportController::class, 'syncImportByReport'])->name('order_import.sync_order_import_by_report');
        Route::post('/create-order-import-by-report', [AdminOrderImportController::class, 'createImportByReport'])->name('order_import.create_import_by_report');
        Route::post('/delete', [AdminOrderImportController::class, 'deleteImportOrder'])->name('order_import.delete');
        Route::post('/delete-detail', [AdminOrderImportController::class, 'deleteImportOrderDetail'])->name('order_import.delete_detail');

        #test
        Route::get ('/test/{id}', [AdminOrderImportController::class, 'testData']);
    });
    Route::group(['prefix' => 'product-exchange'], function (){
        Route::get('/', [AdminProductExchangeController::class, 'index'])->name('product_exchange.index');
        Route::get('/edit/{id}', [AdminProductExchangeController::class, 'edit'])->name('product_exchange.edit');
        Route::get('/create', [AdminProductExchangeController::class, 'create'])->name('product_exchange.create');
        Route::get('/view-import', [AdminProductExchangeController::class, 'viewImport'])->name('product_exchange.view_import');
        Route::get('/export', [AdminProductExchangeController::class, 'export'])->name('product_exchange.export');
        Route::post('/store', [AdminProductExchangeController::class, 'store'])->name('product_exchange.store');
        Route::post('/update/{id}', [AdminProductExchangeController::class, 'update'])->name('product_exchange.update');
        Route::post('/import', [AdminProductExchangeController::class, 'import'])->name('product_exchange.import');
        Route::post('/delete', [AdminProductExchangeController::class, 'deleteList'])->name('product_exchange.delete');
    });
    Route::group(['prefix' => 'warehouse-transfer'], function (){
        Route::get('/', [AdminWarehouseTransferController::class, 'index'])->name('warehouse_transfer.index');
        Route::get('/edit/{id}', [AdminWarehouseTransferController::class, 'edit'])->name('warehouse_transfer.edit');
        Route::get('/create', [AdminWarehouseTransferController::class, 'create'])->name('warehouse_transfer.create');
        Route::get('/print', [AdminWarehouseTransferController::class, 'export'])->name('warehouse_transfer.export');
        Route::get('/getDataImportOrder', [AdminWarehouseTransferController::class, 'getDataImportOrder'])->name('warehouse_transfer.get_data_order_import');
        Route::get('/getDataImportOrderDetail', [AdminWarehouseTransferController::class, 'getDataImportOrderDetail'])->name('warehouse_transfer.get_data_order_import_detail');
        Route::post('/store', [AdminWarehouseTransferController::class, 'store'])->name('warehouse_transfer.store');
        Route::post('/update', [AdminWarehouseTransferController::class, 'update'])->name('warehouse_transfer.update');
        Route::post('/add-item-detail', [AdminWarehouseTransferController::class, 'addItemProductDetail'])->name('warehouse_transfer.add_item_detail');
        Route::post('/delete', [AdminWarehouseTransferController::class, 'delete'])->name('warehouse_transfer.delete');
        Route::post('/delete-detail', [AdminWarehouseTransferController::class, 'deleteDetail'])->name('warehouse_transfer.delete_detail');
    });

    // Quản lý nhân viên giao hàng
    Route::group(['prefix' => 'driver'], function (){
        Route::get('/', [AdminDriverController::class, 'index'])->name('driver.index');
        Route::get('/create', [AdminDriverController::class, 'create'])->name('driver.create');
        Route::post('/create', [AdminDriverController::class, 'PostCreate'])->name('driver.create');
        Route::get('/edit/{id}', [AdminDriverController::class, 'edit'])->name('driver.edit');
        Route::post('/edit/{id}', [AdminDriverController::class, 'postEdit'])->name('driver.edit');
        Route::post('/delete', [AdminDriverController::class, 'deleteDriver'])->name('driver.delete');
        Route::get('/import', [AdminDriverController::class, 'import'])->name('driver.import');
        Route::post('/import', [AdminDriverController::class, 'importDriver'])->name('driver.import');
        Route::get('/export', [AdminDriverController::class, 'export'])->name('driver.export');
    });

    Route::group(['prefix' => 'sync-supplier'], function (){
        Route::get('/', [\App\Admin\Controllers\Warehouse\AdminSyncSupplierController::class, 'sync'])->name('sync-supplier.index');
        Route::post('/change-status', [\App\Admin\Controllers\Warehouse\AdminSyncSupplierController::class, 'changeStatus'])->name('sync_supplier.change_status');
        Route::post('/change-time', [\App\Admin\Controllers\Warehouse\AdminSyncSupplierController::class, 'changeTime'])->name('sync_supplier.change_time');
    });

    // Đồng bộ hóa đơn đặt nhac cung cấp
});

Route::prefix(SC_ADMIN_PREFIX)->group(function () {
    // ------------------------------------- TÌM KIẾM NHANH -----------------------------------------------
    Route::group(['prefix' => 'dynamic_search'], function () {
        Route::get('product', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicProduct'])->name('admin_search.product');
        Route::get('product-customer', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicProductCustomer'])->name('admin_search.product_customer');
        Route::get('supplier', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicSuppplier'])->name('admin_search.supplier');
        Route::get('zone', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicZone'])->name('admin_search.zone');
        Route::get('department', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicDepartment'])->name('admin_search.department');
        Route::get('dish', [\App\Admin\Controllers\AdminDynamicSearchController::class, 'getDynamicDish'])->name('admin_search.dish');
    });
});


// ------------------------------------- Menu -----------------------------------------------
Route::group(['prefix' => 'menu'], function (){
    Route::get('/', [\App\Admin\Controllers\AdminMenuController::class, 'index'])->name('admin_menu.index');
    Route::post('/create', [\App\Admin\Controllers\AdminMenuController::class, 'postCreate'])->name('admin_menu.create');
    Route::get('/edit/{id}', [\App\Admin\Controllers\AdminMenuController::class,'edit'])->name('admin_menu.edit');
    Route::post('/edit/{id}', [\App\Admin\Controllers\AdminMenuController::class,'postEdit'])->name('admin_menu.edit');
    Route::post('/delete', [\App\Admin\Controllers\AdminMenuController::class,'deleteList'])->name('admin_menu.delete');
    Route::post('/update_sort', [\App\Admin\Controllers\AdminMenuController::class,'updateSort'])->name('admin_menu.update_sort');
});



//FIXME
Route::controller(AdminFixMeController::class)->prefix("fake")->group(function () {
    Route::get("/date-menu-card", "fakeDateMenuCard");
    Route::get("/test-log", "TESTLOGDAVICORP");
    Route::get("/price-order", "fakeRealityPriceOrder");
    Route::get("/create-block-date", "createDateRange");
    Route::get("/reality-point", "updateRealityPoint");
    Route::get("/action-point", "updateActionPoint");
    Route::get("/update-order-is-admin-davicorp", "updateOrderAdminDavicorp");
    Route::get("/update-date-for_product", "updateDateForProductMenuCard");
});