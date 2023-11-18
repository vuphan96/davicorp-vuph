<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_order";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->char('drive_id', 36)->after('shipping')->nullable();
            $table->string('drive_code', 50)->after('shipping')->nullable();
            $table->string('drive_address', 255)->after('shipping')->nullable();
            $table->string('drive_name', 255)->after('shipping')->nullable();
            $table->string('drive_phone', 20)->after('shipping')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('drive_id');
            $table->dropColumn('drive_code');
            $table->dropColumn('drive_address');
            $table->dropColumn('drive_phone');
            $table->dropColumn('drive_name');
        });
    }
};
