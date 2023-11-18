<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_warehouse_transfer_history";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->char('warehouse_transfer_id',36)->comment('tên id nhập hàng');
            $table->string('title',255)->nullable()->comment('Loại thao tác');
            $table->text('content')->nullable()->comment('nội dung thao tác');
            $table->char('admin_id',36)->nullable()->comment('id người thao tác');
            $table->string('user_name')->nullable();
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
