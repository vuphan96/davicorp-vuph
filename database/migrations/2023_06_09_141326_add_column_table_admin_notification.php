<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "admin_notification";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table, function (Blueprint $table) {
            $table->text('content_change_import_price')->nullable()->after('content')->comment('Nội dung thay đổi giá nhập');
            $table->tinyInteger('is_import_price')->default(0)->nullable()->after('id_order')->comment('0-thông báo bình thường; 1-giá nhập');
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
            $table->dropColumn('content_change_import_price');
            $table->dropColumn('is_import_price');
        });
    }
};
