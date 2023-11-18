<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class ProductPriceDetailSeeders extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table(SC_DB_PREFIX.'product_price_detail')->insert([
            'product_price_id'   =>1,
            'price_1'   =>200000,
            'price_2'   =>190000,
            'product_id'    =>'abc xyz'
        ]);
        DB::table(SC_DB_PREFIX.'product_price_detail')->insert([
            'product_price_id'   =>1,
            'price_1'   =>200000,
            'price_2'   =>190000,
            'product_id'    =>'abc def'
        ]);
    }
}
