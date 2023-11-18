<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    public $table = SC_DB_PREFIX . 'shop_holidays';
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table($this->table)->insert([
                [
                    'id' => 1,
                    'name' => 'Khóa t7, chủ nhật',
                    'status' => 1,
                    'type' => 'everyday',
                ],

            ]
        );
    }
}
