<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ProductPriceSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(SC_DB_PREFIX.'product_price')->insert([
            'name'      =>'Bảng giá cho khách A',
            'user_id'   =>1,
            'status'    =>0
        ]);
        DB::table(SC_DB_PREFIX.'product_price')->insert([
            'name'      =>'Bảng giá cho khách B',
            'user_id'   =>1,
            'status'    =>0
        ]);
    }
}
