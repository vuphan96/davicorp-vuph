<?php

namespace App\Admin\Api;

use App\Front\Models\ShopOrder;
use SCart\Core\Front\Models\ShopCategory;
use SCart\Core\Front\Models\ShopCategoryDescription;
use function PHPUnit\Framework\isEmpty;

class ShopFrontController extends ApiController
{
    /**
     * display list category root (parent = 0)
     */
    public function getAllCategory()
    {
        $itemsList = ShopCategory::where('status', 1)->get();
        $category_array = [];
        foreach ($itemsList as $item) {
            $data = [
                'id' => $item->id,
                'name' => $item->name,
                'image' => $item->image ? config('app.url') . $item->image : "",
            ];
            $category_array[] = $data;
        }
        return $this->responseSuccess($category_array);
    }

    public function getCommentOrder()
    {
        $itemsList = ShopOrder::$NOTE;
        return $this->responseSuccess($itemsList);
    }

    public  function checkEnableRegisterAccount(){
        return $this->responseSuccess(config('admin.enable_register_account'));
    }

    public function getMobileAppInfo()
    {
        $data = [
            'version' => config('admin.mobile_app_latest_version'),
        ];
        return $this->responseSuccess($data);
    }
}