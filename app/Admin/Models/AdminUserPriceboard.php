<?php

namespace App\Admin\Models;

use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Front\Models\ShopUserPriceboard;
use SCart\Core\Front\Models\ShopCategory;
use SCart\Core\Front\Models\ShopCategoryDescription;
use SCart\Core\Front\Models\ShopProductCategory;
use SCart\Core\Front\Models\ShopProductDescription;
use SCart\Core\Front\Models\ShopProductStore;

class AdminUserPriceboard extends ShopUserPriceboard
{
    use HasFactory;

    public static function getUserPriceBoard(array $dataSearch = null, bool $all = null)
    {
        $keyword          = $dataSearch['keyword'] ?? '';
        $sort_order       = $dataSearch['sort_order'] ?? '';
        $arrSort          = $dataSearch['arrSort'] ?? '';

        $userPriceBoardList = (new AdminUserPriceboard());
        if ($keyword) {
            $userPriceBoardList = $userPriceBoardList->where('name', 'like', '%' . $keyword . '%');
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $userPriceBoardList = $userPriceBoardList->orderBy($field, $sort_field);
        } else {
            $userPriceBoardList = $userPriceBoardList->orderBy('start_date', 'desc');
        }
        $userPriceBoardList->groupBy('id');
        if($all){
            return $userPriceBoardList->get();
        }
        return $userPriceBoardList->paginate(config('pagination.admin.medium'));
    }
    public function getPriceboardAdmin($id){
        return $this->find($id);
    }

    public static function getFormatedCustomers($id){
        $rawPriceboard = (new AdminUserPriceboard)->getPriceboardAdmin($id);
        $output = [];
        if($rawPriceboard->customers){
            foreach ($rawPriceboard->customers as $customer){
                $output[] = [
                    'name' => $customer->customerInfo->name ?? '',
                    'id' => $customer->customerInfo->id ?? ''
                ];
            }
        }
        return $output;
    }
}
