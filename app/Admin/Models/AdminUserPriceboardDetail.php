<?php

namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopUserPriceboardDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminUserPriceboardDetail extends ShopUserPriceboardDetail
{
    use HasFactory;
    public static function getCustomerInsertList($raw_customer, $id){
        $listCustomer = explode('|', $raw_customer);
        if(count($listCustomer) > 0) {
            $insertDetailData = [];
            foreach ($listCustomer as $customer_id) {
                if(is_numeric($customer_id)){
                    continue;
                }
                $insertDetailData[] = [
                    'user_priceboard_id' => $id,
                    'customer_id' => $customer_id
                ];
            }
            return $insertDetailData;
        }
        return [];
    }
    public function customerInfo(){
        return $this->belongsTo(ShopCustomer::class,'customer_id', 'id');
    }
}
