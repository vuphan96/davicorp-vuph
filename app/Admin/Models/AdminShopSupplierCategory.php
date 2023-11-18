<?php

namespace App\Admin\Models;

use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopSupplierCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminShopSupplierCategory extends ShopSupplierCategory
{
    use HasFactory;
    public static function destroyCurrentCategory($id){
        $delete = AdminShopSupplierCategory::where('supplier_id', $id)->delete();
        return $delete ? 1 :0;
    }
    public static function countCurrentRecord($id){
        return AdminShopSupplierCategory::where('supplier_id', $id)->count();
    }
    public static function getFormatedCategory(ShopSupplier $supplier){
        return $supplier->categories()->pluck('category_id');
    }
}
