<?php

namespace App\Admin\Api;

use App\Admin\Controllers\AdminCustomerController;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminOrder;
use App\Admin\Models\AdminProduct;
use App\Admin\Models\AdminUnit;
use App\Admin\Models\AdminUser;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopRating;
use App\Front\Models\ShopRewardPrinciple;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopSupplier;
use App\Http\Models\ShopProductDescription;
use App\Http\Requests\Api\ApiRatingEditRequest;
use App\Http\Requests\Api\ApiRatingRequest;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;
use Nette\Utils\Random;

class RatingController extends ApiController
{
    /**
     * Get authenticated user
     */
    public function getRating(Request $request){
        try {
            $user = $request->user();

            if(!$user){
                throw new \Exception("Không tìm thấy người dùng!");
            }
            $year = $request["year"] ?? null;

            $listRating = ShopRating::where("customer_id", $user->id);
            if($year){
                $listRating = $listRating->where("year", $year);
            }
            $listRating= $listRating
                ->orderBy("year", "DESC")
                ->orderBy("month", "DESC")
                ->select(["id", "point", "content", "month", "year","created_at"])->get();
            $arrayPoint = [];
            $data = [];

            if($listRating && (count($listRating) > 0)){
               $arrayPoint = data_get($listRating, "*.point");
               $avg = array_sum($arrayPoint) / count($arrayPoint);
                $prepareData = [
                    "avg" => $avg,
                    "list" => $listRating
                ];
                $data = $prepareData;
            } else {
                $prepareData = [
                    "avg" => 0,
                    "list" => []
                ];
                $data = $prepareData;
            }
            return $this->responseSuccess($data);
        } catch (\Throwable $e){
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function edit(ApiRatingEditRequest $request, $id){
        try {
            $data = [];
            $user = $request->user();
            if(!$user){
                throw new \Exception("Không tìm thấy người dùng!");
            }
            $rating = ShopRating::find($id);
            if(!$rating){
                return $this->responseError([], Response::HTTP_BAD_REQUEST, "Không tìm thấy");
            }
            $data = $request->only(["point", "content"]);
            $rating->fill($data);
            if(!$rating->save()){
                return $this->responseError([], Response::HTTP_BAD_REQUEST, "Lỗi không xác định");
            };
            return $this->responseSuccess();
        } catch (\Throwable $e){
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function delete(Request $request, $id){
        try {
            $data = [];
            $user = $request->user();
            if(!$user){
                throw new \Exception("Không tìm thấy người dùng!");
            }
            $rating = ShopRating::find($id);
            if(!$rating){
                return $this->responseError([], Response::HTTP_BAD_REQUEST, "Không tìm thấy");
            }
            if(!$rating->delete()){
                return $this->responseError([], Response::HTTP_BAD_REQUEST, "Lỗi không xác định");
            };
            return $this->responseSuccess();
        } catch (\Throwable $e){
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function create(ApiRatingRequest $request){
        try {
            $data = [];
            $user = $request->user();
            $customer_data = ShopCustomer::find($user->id);
            if(!$user){
                throw new \Exception("Không tìm thấy người dùng!");
            }
            $ratingData = $request->validated();
            //Check existing item
            $rating = ShopRating::
                where("customer_id", $user->id)
                ->where("month", $ratingData["month"])
                ->where("year", $ratingData["year"])
                ->first();

            //Update if exist
            if($rating){
                throw new \Exception("Bạn đã đánh giá mức độ hài lòng dịch vụ của Davicorp vào tháng "
                    . $ratingData["month"] . '/' . $ratingData["year"] . ",  vui lòng đánh giá vào tháng sau");
            } else { // Create new
                $rating = new ShopRating($ratingData);
                $rating->customer_id = $user->id;
            }
            //Check end return response
            if($rating->save()){
                //SEND NOTIFICATION
                $notification_amdin = new AdminNotification();
                $notification_amdin->title = "Đánh giá tháng " . $ratingData["month"];
                $notification_amdin->content = 'Bạn có đánh giá mới từ hiệu trưởng trường ' . $customer_data->getCustomerName();
                $notification_amdin->link = '/sc_admin/rating?filter_month=' . $ratingData["month"] . '/' . $ratingData["year"];
                $notification_amdin->display = 0;
                $notification_amdin->is_admin = 1;
                $notification_amdin->save();
                AdminNotificationCustomer::sendNotifyToAdmin($notification_amdin->content, $notification_amdin->title);
                // RETURN RESPONSE
                return $this->responseSuccess();
            }
            return $this->responseError([], Response::HTTP_BAD_REQUEST, "Lỗi không xác định");
        } catch (\Throwable $e){
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

}
