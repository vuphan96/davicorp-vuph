<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "report_warehouse_product_dept_history";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('report_product_dept_id', 36);
            $table->char('user_id', 36)->nullable();
            $table->string('user_name', 36)->nullable();
            $table->decimal('qty_dept_origin',11, 2)->default(0)->nullable();
            $table->decimal('qty_export_current',11, 2)->default(0)->nullable();
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
