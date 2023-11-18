<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "report_warehouse_product_dept";
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
            $table->char('order_detail_id',36);
            $table->string('order_id_barcode',12);
            $table->string('order_explain',50);
            $table->string('order_object',50);
            $table->char('product_id',36);
            $table->string('product_code',50);
            $table->string('product_name',255);
            $table->string('product_unit',255);
            $table->integer('product_num')->nullable();
            $table->integer('customer_num')->nullable();
            $table->tinyInteger('department_id')->nullable();
            $table->string('customer_short_name')->nullable();
            $table->decimal('qty_export_origin',11, 2)->default(0)->nullable();
            $table->decimal('qty_dept',11, 2)->default(0)->nullable();
            $table->decimal('qty_export',11, 2)->default(0)->nullable();
            $table->decimal('qty_export_final',11, 2)->default(0)->nullable();
            $table->date('export_date');
            $table->string('customer_code',50);
            $table->string('customer_name',255);
            $table->char('customer_id',36);
            $table->string('export_code', 50);
            $table->string('export_id', 36);
            $table->char('category_id', 36);
            $table->tinyInteger('product_kind');
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
