<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_export";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_name',50)->unique();
            $table->char('customer_id',36);
            $table->string('customer_code',50);
            $table->string('customer_name',255);
            $table->string('customer_addr',255);
            $table->string('email', 255);
            $table->string('phone',20);
            $table->char('warehouse_id',36);
            $table->string('warehouse_name',255);
            $table->string('warehouse_code',50);
            $table->date('date_export');
            $table->tinyInteger('type_order')->default('0');
            $table->tinyInteger('status')->default('1');
            $table->decimal('total',15, 2)->default(0);
            $table->decimal('total_reality',15, 2)->default(0);
            $table->tinyInteger('edit')->default('0');
            $table->string('note', 2000)->nullable();
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
