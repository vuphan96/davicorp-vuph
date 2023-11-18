<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "report_warehouse_product_stock";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('product_id',36)->comment('id sản phẩm');
            $table->string('product_code',50)->comment('mã sản phẩm');
            $table->string('product_name',255)->comment('tên sản phẩm');
            $table->decimal('price_import',11,2)->nullable()->comment('Giá nhập');
            $table->decimal('price_sale',11,2)->nullable()->comment('Giá bán');
            $table->tinyInteger('product_kind')->default(0)->comment('Loại mặt hàng: 0: Khô, 1: Tươi');
            $table->decimal('qty_import',11,2)->nullable()->comment('Số lượng nhập');
            $table->decimal('qty_export',11,2)->nullable()->comment('Số lượng xuất ');
            $table->decimal('qty_stock',11,2)->nullable()->comment('Số tượng tồn kho ');
            $table->date('date_action')->comment('Ngày ghi nhận thao tác nhập xuất tồn');
            $table->bigInteger('warehouse_id')->comment('id kho');
            $table->string('warehouse_name', 255)->comment('ten kho');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
};
