<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "report_warehouse_card";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table($this->table, function (Blueprint $table) {
            $table->char('detail_id',36)->nullable()->after('supplier_id')->comment('id chi tiet don hang');
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
            $table->dropColumn('detail_id');
        });
    }
};
