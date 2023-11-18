<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_product_exchange";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->comment('Id ');
            $table->char('product_id',36)->comment('Id của sản phẩm cơ sở');
            $table->char('product_exchange_id',36)->comment('Id của sản phẩm qui đổi');
            $table->integer('qty_exchange')->comment('Số lượng qui đổi');
            $table->tinyInteger('status')->default(1)->comment('Trạng thái : 0 -OFF; 1 -ON');
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
