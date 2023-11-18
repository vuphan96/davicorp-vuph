<?php
namespace App\Admin\Models;

use App\Front\Models\ShopDavicookMenu;
use App\Front\Models\ShopDish;
use Illuminate\Database\Eloquent\Model;

class AdminDish extends ShopDish
{
    public function getListDish($dataSearch, array $ids = null) {

        $keyword = $dataSearch['keyword'] ?? '';
        $dataListDish = new AdminDish();

        if(!empty($ids)) {
            $dataListDish = $dataListDish->whereIn('id', $ids);
            return $dataListDish->orderBy('name', 'ASC')->get();
        }
        if ($keyword) {
            $dataListDish = $dataListDish->where('name', 'like', '%' . $keyword . '%')->orWhere('code', 'like', '%' . $keyword . '%');
        }

        $dataListDish = $dataListDish->orderBy('name', 'ASC')->get();
        return $dataListDish;
    }

    public function getListDishCustomers($id, $dish_name, $dish_code)
    {
        $dataMenuCustomer = ShopDavicookMenu::with('dish', 'details');
        if ($dish_name) {
            $dataMenuCustomer = $dataMenuCustomer->whereHas('dish', function ($query) use ($dish_name) {
                $query->where('name', 'like', '%' . $dish_name . '%');
            });
        }
        if ($dish_code) {
            $dataMenuCustomer = $dataMenuCustomer->whereHas('dish', function ($query) use ($dish_code) {
                $query->Where('code', 'like', '%' . $dish_code . '%');
            });
        }
        $dataMenuCustomer = $dataMenuCustomer->where('customer_id',$id)
            ->orderBy('id', 'DESC')
            ->paginate(15, ['*'], 'page');
        return $dataMenuCustomer;
    }

}
