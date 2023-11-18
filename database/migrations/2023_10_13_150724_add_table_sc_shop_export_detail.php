<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_export_detail";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('export_id',36);
            $table->string('export_code',50);
            $table->char('product_id',36);
            $table->string('product_name',255);
            $table->string('product_sku',50);
            $table->decimal('qty',11, 2);
            $table->decimal('qty_reality', 11, 2);
            $table->string('unit',255);
            $table->decimal('price',15,2)->default(0);
            $table->decimal('amount',15, 2)->default(0);
            $table->decimal('amount_reality',15, 2)->default(0);
            $table->char('order_id', 36);
            $table->string('order_id_name', 255);
            $table->string('order_explain', 500)->nullable();
            $table->char('order_object_id', 36);
            $table->date('order_delivery_date');
            $table->char('category_id', 36);
            $table->char('department_id', 36);
            $table->tinyInteger('zone_id');
            $table->tinyInteger('product_kind');
            $table->string('customer_name', 255);
            $table->string('customer_code', 50);
            $table->string('comment', 2000)->nullable();
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
