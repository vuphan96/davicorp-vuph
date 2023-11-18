<?php

namespace App\Admin\Models;

use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopSupplierCustomer;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminShopSupplierCustomer extends ShopSupplierCustomer
{
    use HasFactory;
    public static function destroyCurrentCustomer($id){
        $delete = AdminShopSupplierCustomer::where('supplier_id', $id)->delete();
        return $delete ? 1 :0;
    }
    public static function countCurrentRecord($id){
        return AdminShopSupplierCustomer::where('supplier_id', $id)->count();
    }
    public static function getFormatedCustomers(ShopSupplier $supplier){
        $output = [];
        if($supplier->customers &&  count($supplier->customers) > 0){
            foreach ($supplier->customers as $customer){
                $output[] = [
                    'name' => $customer->customer->name,
                    'id' => $customer->customer->id
                ];
            }
        }
        return $output;
    }
}
