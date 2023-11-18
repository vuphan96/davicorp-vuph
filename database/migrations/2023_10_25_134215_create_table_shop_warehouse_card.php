<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_warehouse_card";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('order_id',36);
            $table->string('order_id_name',50);
            $table->char('product_id',36);
            $table->string('product_code',50);
            $table->string('product_name',255);
            $table->string('explain',50);
            $table->decimal('qty_import',11, 2)->default(0)->nullable();
            $table->decimal('qty_export',11, 2)->default(0)->nullable();
            $table->decimal('qty_stock',11, 2)->default(0);
            $table->date('bill_date');
            $table->string('customer_code',50);
            $table->string('object_name',255);
            $table->bigInteger('warehouse_id');
            $table->string('warehouse_name',255);
            $table->tinyInteger('type_order');
            $table->string('supplier_name',255)->nullable();
            $table->char('supplier_id',36)->nullable();
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
        //
        Schema::dropIfExists($this->table);
    }
};
