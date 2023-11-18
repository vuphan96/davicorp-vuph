<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "admin_menu";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->dropColumn('type_menu'); // Nếu bạn muốn rollback, bạn có thể xóa cột này
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
            $table->tinyInteger('type_menu')->after('parent_id')->default(0); // Thêm cột mới với kiểu dữ liệu và tên cột của bạn
        });
    }
};
