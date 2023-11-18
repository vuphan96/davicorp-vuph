<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_import_detail";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('import_id',36)->comment('id nhập hàng');
            $table->string('import_id_name',12)->comment('tên id nhập hàng');
            $table->char('product_id',36);
            $table->string('product_code',50);
            $table->string('product_name')->nullable();
            $table->char('category_id', 36)->nullable(); #Thêm bs lọc báo cáo nhập
            $table->bigInteger('department_id')->nullable(); #Thêm bs lọc báo cáo nhập
            $table->bigInteger('zone_id')->nullable(); #Thêm bs lọc báo cáo nhập
            $table->tinyInteger('product_kind')->nullable()->comment("0: hàng khô; 1:hàng tươi"); #Thêm bs lọc báo cáo nhập
            $table->char('customer_id',36)->nullable();
            $table->string('customer_code', 50)->nullable();
            $table->string('customer_name', 255)->nullable();
            $table->decimal('qty_order',11,2)->nullable()->comment('số lượng nhập kho');
            $table->decimal('qty_reality',11,2)->nullable()->comment('số lượng nhập thực tế');
            $table->bigInteger('unit_id')->nullable();
            $table->string('unit_name')->nullable();
            $table->decimal('product_price',15,2)->comment(' giá sản phẩm');
            $table->decimal('amount',15,2)->comment(' tổng tiền');
            $table->decimal('amount_reality',15,2)->nullable();
            $table->string('comment',2000)->nullable();
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
