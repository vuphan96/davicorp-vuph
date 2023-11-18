<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_supplier";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table($this->table, function (Blueprint $table) {
            $table->string('name_login',255)->nullable()->after('name')->comment('Tên đăng nhập của supplier');
            $table->string('password',255)->after('name_login')->comment('Mật khẩu của supplier');
            $table->tinyInteger('type_form_report')->after('password')->default(1)->comment('Chọn mẫu nhập hàng: 1: Mẫu hàng 1, 2: Mẫu hàng 2');
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
            $table->dropColumn('name_login'); // Nếu bạn muốn rollback, bạn có thể xóa cột này
            $table->dropColumn('password'); // Nếu bạn muốn rollback, bạn có thể xóa cột này
            $table->dropColumn('type_form_report'); // Nếu bạn muốn rollback, bạn có thể xóa cột này
        });
    }
};
