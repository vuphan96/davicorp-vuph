<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_driver";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_name',50)->comment('Mã nhân viên');
            $table->string('full_name',255)->comment('Tên nhân viên');
            $table->string('email',255)->nullable();
            $table->string('address',255)->nullable();
            $table->string('phone',20)->nullable();
            $table->string('login_name',50)->nullable()->comment('Tên đăng nhập của driver');
            $table->string('password',50)->nullable()->comment('Mật khẩu của driver');
            $table->string('location_Ing',30)->nullable()->comment('Vị trí hiện tại - vĩ độ');
            $table->string('location_at',30)->nullable()->comment('Vị trí hiện tại - kinh độ');
            $table->text('customer_of_driver')->nullable()->comment('Danh sách khách hàng phụ thuộc');
            $table->tinyInteger('status')->comment('0: Không hoạt động, 1: Hoạt động');
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
