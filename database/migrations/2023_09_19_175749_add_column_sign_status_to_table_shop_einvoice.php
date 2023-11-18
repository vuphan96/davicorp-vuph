<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public $table = SC_DB_PREFIX . "shop_einvoice";
    public function up()
    {
        //
        Schema::table($this->table, function (Blueprint $table) {
            $table->tinyInteger('sign_status')->after('process_status')->default(0)->comment('0-Chưa làm 1-Đã gởi 2-Đang làm 3-Thất bại 4-Thành công');
            $table->timestamp('plan_sign_date')->after('plan_start_date')->nullable();
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
            $table->dropColumn('sign_status'); // Nếu bạn muốn rollback, bạn có thể xóa cột này
            $table->dropColumn('plan_sign_date');
        });
    }
};
