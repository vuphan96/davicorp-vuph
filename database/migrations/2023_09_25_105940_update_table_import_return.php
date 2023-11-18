<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_import_return";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('import_id',36)->comment(' id nhập hàng');
            $table->char('import_detail_id',36)->comment('id chi tiết nhập hàng');
            $table->char('product_id',36);
            $table->string('product_name');
            $table->string('product_unit',50)->comment(' Đơn vị sản phẩm');
            $table->string('product_code',50);
            $table->char('customer_id',36)->nullable();
            $table->string('customer_name',255)->nullable();
            $table->string('customer_code',50)->nullable();
            $table->string('admin_id',36)->comment('id người nhập hàng');
            $table->decimal('qty_original',11,2)->comment('số lượng ban đầu');
            $table->decimal('qty_return',11,2)->comment(' số lượng trả lại');
            $table->decimal('product_price',15,2)->comment('giá sản phẩm');
            $table->decimal('return_amount',15,2)->comment('Tổng tiền trả lại');
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
