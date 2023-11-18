<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_warehouse_transfer_detail";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('warehouse_transfer_id',36)->comment('tên id nhập hàng');
            $table->char('product_id',36);
            $table->string('product_code',50);
            $table->string('product_name', 255);
            $table->tinyInteger('product_kind')->default(0)->comment("0:khô, 1:tươi");
            $table->string('unit_name')->nullable();
            $table->decimal('qty',11,2)->nullable()->comment('Số lượng chuyển kho');
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
