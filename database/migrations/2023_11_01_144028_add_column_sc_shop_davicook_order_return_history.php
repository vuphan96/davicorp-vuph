<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_davicook_order_return_history";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->tinyInteger('product_kind')->after('product_code')->comment('Loại sản phẩm: 0: hàng khô, 1: hàng tươi sống');
            $table->decimal('qty_import',11,2)->default(0)->after('product_kind')->comment('số lượng đã nhập lại vào kho');
            $table->decimal('qty_not_import',11,2)->after('qty_import')->comment('số lượng chưa nhập');
            $table->char('category_id',36)->after('qty_not_import')->comment('id danh mục');
            $table->char('customer_id',36)->after('category_id')->comment('id khách hàng');
            $table->string('customer_name',255)->after('customer_id')->comment('tên khách hàng');
            $table->string('order_id_name',50)->after('order_id')->comment('mã đơn hàng');
            $table->char('supplier_id',36)->after('customer_name')->comment('id nhà cung cấp');
            $table->string('customer_code',50)->after('customer_id')->comment('mã khách hàng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('product_kind');
            $table->dropColumn('qty_import');
            $table->dropColumn('qty_not_import');
            $table->dropColumn('category_id');
            $table->dropColumn('customer_id');
            $table->dropColumn('customer_name');
            $table->dropColumn('order_id_name');
            $table->dropColumn('supplier_id');
        });
    }
};
