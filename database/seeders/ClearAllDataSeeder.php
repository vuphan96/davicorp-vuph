<?php

namespace Database\Seeders;

use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminNotificationTemplate;
use App\Admin\Models\AdminNotifyMessage;
use App\Admin\Models\AdminProductPrice;
use App\Admin\Models\AdminProductPriceDetail;
use App\Admin\Models\AdminUnit;
use App\Front\Models\ShopCategory;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopOrderReturnHistory;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopRewardPrinciple;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopUserPriceboardDetail;
use App\Front\Models\ShopZone;
use App\Http\Models\ShopProductDescription;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SCart\Core\Front\Models\ShopCategoryDescription;

class ClearAllDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $listTable = [];

        //Thanh
        $listTable[] = (new ShopProduct)->getTable();
        $listTable[] = (new ShopProductDescription)->getTable();
        $listTable[] = (new ShopProductSupplier)->getTable();
        $listTable[] = (new ShopCustomer)->getTable();
        $listTable[] = (new ShopUserPriceboard)->getTable();
        $listTable[] = (new ShopUserPriceboardDetail)->getTable();
        $listTable[] = (new ShopCategory)->getTable();
        $listTable[] = (new ShopCategoryDescription())->getTable();
        $listTable[] = (new ShopOrder)->getTable();
        $listTable[] = (new ShopOrderDetail)->getTable();
        $listTable[] = (new ShopOrderHistory)->getTable();
        $listTable[] = (new ShopOrderReturnHistory)->getTable();
        $listTable[] = (new ShopPointHistory)->getTable();
        $listTable[] = (new ShopPoint)->getTable();
        $listTable[] = (new ShopZone)->getTable();
        $listTable[] = (new ShopRewardPrinciple)->getTable();
        $listTable[] = (new ShopRewardTier)->getTable();
        // Vũ
        $listTable[] = (new AdminProductPrice)->getTable();
        $listTable[] = (new AdminProductPriceDetail)->getTable();
        $listTable[] = (new AdminUnit)->getTable();
        $listTable[] = (new AdminNotification)->getTable();
        $listTable[] = (new AdminNotificationCustomer)->getTable();
        $listTable[] = (new AdminNotificationTemplate)->getTable();
        $listTable[] = (new AdminNotifyMessage)->getTable();
        $listTable[] = (new ShopSupplier)->getTable();
        
        DB::connection(SC_CONNECTION)->beginTransaction();
        $error = 0;
        foreach ($listTable as $table){
            $truncate = DB::table($table)->truncate();
            if(!$truncate){
                $error = 1;
            }
            echo "\nTruncate $table: $error";
        }
        if($error){
            echo "\nFailed";
        } else {
            echo "\nAll success!";
        }
    }
}
