<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_order_change_extra";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('note',500)->nullable()->comment('ghi chu don hang');
            $table->tinyInteger('status')->comment('Trang thai don hang');
            $table->tinyInteger('type_order')->comment('1: davicorp , 2: davicook');
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
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('note');
            $table->dropColumn('status');
            $table->dropColumn('type_order');
        });
    }
};
