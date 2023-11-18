<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_product_warehouse";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Id của kho hàng sản phẩm');
            $table->bigInteger('warehouse_id')->comment('Id của danh sách kho hàng');
            $table->char('product_id',36)->comment('Id của sản phẩm');
            $table->decimal('count',11,2)->default(0)->nullable()->comment('Số lượng tồn kho');
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
