<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "warehouse_config";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->string('group',100)->nullable()->comment('tiêu đề');
            $table->string('key',100)->comment('loại');
            $table->string('value',255)->nullable()->comment('giá trị');
            $table->string('description',255)->nullable()->comment('mô tả');
            $table->tinyInteger('status')->default(0)->comment('Trạng thái: 0: không hoạt động, 1: hoạt động');
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
