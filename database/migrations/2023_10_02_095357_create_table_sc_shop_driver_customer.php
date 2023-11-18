<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_driver_customer";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->char('staff_id',36)->comment('ID của Driver');
            $table->char('customer_id',36)->nullable()->comment('ID của khách hàng');
            $table->tinyInteger('type_customer')->nullable()->comment('1: Davicorp, 2: Davicook');
            $table->tinyInteger('type_order')->comment('1: Đơn hàng đợt 1, 2: Đơn hàng đợt 2');
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
