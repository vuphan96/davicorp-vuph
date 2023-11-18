<?php

namespace Database\Seeders;

use App\Admin\Models\AdminShopOrderChangeExtra;
use App\Front\Models\ShopDavicookOrderDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use SCart\Core\Front\Models\ShopOrderDetail;

class GenerateBarcode extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = ShopOrderDetail::whereDate('created_at', '>=', '2023-10-20')->get();
        echo count($list);
        foreach($list as $item){
            $update = $item->update([
                'id_barcode' => str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT)
            ]);
            if(!$update){
                echo "Fail";
            }
        }

        $list = ShopDavicookOrderDetail::whereDate('created_at', '>=', '2023-10-20')->get();
        echo count($list);
        foreach($list as $item){
            $update = $item->update([
                'id_barcode' => str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT)
            ]);
            if(!$update){
                echo "Fail";
            }
        }

        $list = AdminShopOrderChangeExtra::whereDate('created_at', '>=', '2023-10-20')->get();
        echo count($list);
        foreach($list as $item){
            $update = $item->update([
                'id_barcode' => str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT)
            ]);
            if(!$update){
                echo "Fail";
            }
        }
    }
}
