<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DavicookOrderStatusSeed extends Seeder
{
    public $table = SC_DB_PREFIX . 'shop_davicook_order_status';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table($this->table)->insert([
            [
                'id' => 0,
                'name' => 'Đơn nháp',
            ],
            [
                'id' => 1,
                'name' => 'Đang khả dụng',
            ],
            [
                'id' => 2,
                'name' => 'Đã xuất khô',
            ],
            [
                'id' => 7,
                'name' => 'Đã hủy',
            ],
        ]
        );
    }
}
