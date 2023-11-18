<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_order_return_history";
    
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropPrimary();
            $table->uuid('id')->change()->primary();
        });
    }
    
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropPrimary();
            $table->bigIncrements('id')->change()->primary();
        });
    }
};