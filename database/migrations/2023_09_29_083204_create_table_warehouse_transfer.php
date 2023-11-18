<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_warehouse_transfer";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('id_name',50)->unique()->comment('tên id nhập hàng');
            $table->string('title',255)->nullable()->comment('Tên phiếu chuyển kho');
            $table->string('reason',1000)->nullable()->comment('Lý do chuyển hàng');
            $table->char('warehouse_id_to', 36)->comment('id kho chuyển hàng');
            $table->string('warehouse_name_to',255)->comment('Tên kho chuyển hàng');
            $table->string('warehouse_code_to',50)->comment('Mã kho chuyển hàng');
            $table->char('warehouse_id_from', 36)->comment('id kho nhận hàng');
            $table->string('warehouse_name_from',255)->comment('Tên kho nhận hàng');
            $table->string('warehouse_code_from',50)->comment('Mã kho nhận hàng');
            $table->date('date_export')->nullable()->comment('Ngày chuyển thành công');
            $table->tinyInteger('edit')->default(0)->comment('1 - edited; 0 - No edit');
            $table->tinyInteger('status')->default(1)->comment('1:Chờ chuyển;  2:Đã chuyển;  3: Đã nhận;  4: Đã hủy');
            $table->text('note')->nullable();
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
