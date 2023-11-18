<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public $table = SC_DB_PREFIX . "shop_import";
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
            $table->char('supplier_id',36)->nullable()->comment('id nhà cung cấp');
            $table->string('supplier_name')->nullable()->comment('tên nhà cung cấp');
            $table->bigInteger('warehouse_id')->nullable()->comment('id kho');
            $table->string('warehouse_code',50)->nullable()->comment('mã kho');
            $table->string('warehouse_name')->nullable()->comment('tên kho');
            $table->string('address')->nullable();
            $table->string('email')->nullable();
            $table->string('phone',20)->nullable();
            $table->date('delivery_date')->nullable()->comment('ngày giao hàng');
            $table->dateTime('reality_delivery_date')->nullable()->comment('thời gian giao thực tế');
            $table->decimal('total',15,2)->nullable();
            $table->decimal('total_reality',15, 2)->comment('Tổng tiền thực tế');
            $table->tinyInteger('edit')->default(0)->comment('1 - edited; 0 - No edit');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('type_import')->nullable()->default(0)->comment('0: nhập tay; 1-Mẫu rp 1; 2-Mẫu rp 2');
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
