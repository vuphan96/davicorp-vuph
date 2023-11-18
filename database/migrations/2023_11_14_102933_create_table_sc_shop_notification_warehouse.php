<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_notification_warehouse";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->string('title', 255);
            $table->text('content')->nullable();
            $table->string('icon', 255)->nullable();
            $table->char('order_id',36)->nullable();
            $table->string('order_code',50)->nullable();
            $table->char('user_id',36)->comment('id nhan vien giao hang hoac nha cung cap');
            $table->tinyInteger('user_type')->default(1)->comment('1: nhan vien giao hang, 2: nha cung cap');
            $table->tinyInteger('seen')->default(0)->comment('1:da xem');
            $table->tinyInteger('display')->default(1)->comment('1:hien thi');
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
