<?php

namespace App\Admin\Api;

use App\Admin\Models\AdminNotificationCustomer;
use App\Front\Models\ReceiverNotify;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopPointHistory;
use App\Front\Models\ShopRewardTier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerController extends ApiController
{

    /**
     * Get authenticated user
     */
    public function getInfo(Request $request)
    {
        $user = $request->user();
        $customer = ShopCustomer::where('id', $user->id)->first();
        $shopRewardTier = ShopRewardTier::find($user->tier_id);
        $shopPoint = ShopPoint::where('customer_id', $user->id)->where('month', Carbon::now()->month)->where('year', Carbon::now()->year)->first();
        if ($shopPoint) {
            $userPoint = $shopPoint->point;
        } else {
            $userPoint = 0;
        }

        $pointExchange = (float)$shopRewardTier->rate * (float)$userPoint;

        $user['point'] = $userPoint;
        $user['pointExchange'] = $pointExchange;
        $user['customer_name'] = $customer->name;

        if (is_null($user)) {
            return $this->responseError([], Response::HTTP_UNAUTHORIZED, 'Vui lòng kiểm tra lại!');
        }

        return $this->responseSuccess($user);
    }

    public function updateInfo(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $request->user();
            $customer = ShopCustomer::find($user->id);

            $data = $request->except('_method');

            if ($request->name == null) {
                $data['name'] = "";
            }
            if ($request->phone == null) {
                $data['phone'] = "";
            }
            if ($request->email == null) {
                $data['email'] = "";
            }
            if ($request->tax_code == null) {
                $data['tax_code'] = "";
            }
            $customer->update($data);

            DB::commit();
            return $this->responseSuccess($customer);
        } catch (\PDOException $error) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $error->getMessage());
        }
    }

    /**
     * Change password
     */
    public function checkPassword(Request $request)
    {
        $rules = [
            'password' => 'required|string',
        ];

        $data = $request->all();
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            return $this->responseError($v->errors());
        }

        $dataUser = Auth::user();
        $password = $request->get('password');

        if (!Hash::check($password, $dataUser->password)) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Mật khẩu cũ không chính xác!');
        }
        return $this->responseSuccess($dataUser);
    }

    public function updatePassword(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validation
            $rules = [
                'password' => 'required|string',
                'password_confirm' => 'required|string|min:6'
            ];

            $data = $request->all();
            $v = Validator::make($data, $rules);
            if ($v->fails()) {
                return $this->responseError($v->errors());
            }

            $dataUser = Auth::user();
            $password = $request->get('password');
            $password_confirm = $request->get('password_confirm');

            if ($password != $password_confirm) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Nhập lại mật khẩu không khớp!');
            }

            $dataUser->password = bcrypt($password);
            $dataUser->save();
            DB::commit();
            return $this->responseSuccess($dataUser);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function updateShoolmasterPassword(Request $request)
    {
        try {
            DB::beginTransaction();
            // Validation
            $rules = [
                'schoolmaster_password' => 'required|string',
                'schoolmaster_password_confirm' => 'required|string|min:6'
            ];

            $data = $request->all();
            $v = Validator::make($data, $rules);
            if ($v->fails()) {
                return $this->responseError($v->errors());
            }

            $dataUser = Auth::user();
            $password = $request->get('schoolmaster_password');
            $password_confirm = $request->get('schoolmaster_password_confirm');

            if ($password != $password_confirm) {
                return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Nhập lại mật khẩu không khớp!');
            }

            $dataUser->schoolmaster_password = bcrypt($password);
            $dataUser->save();
            DB::commit();
            return $this->responseSuccess($dataUser);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function rewardHistories(Request $request)
    {
        $user = $request->user();
        $shopRewards = ShopPoint::where('customer_id', $user->id)->get();
        $ShopPointHistory_array = [];
        foreach ($shopRewards as $shopReward) {
            foreach ($shopReward->histories as $ShopPointHistory) {
                array_push($ShopPointHistory_array, $ShopPointHistory);
            }
        }
        return $this->responseSuccess(collect($ShopPointHistory_array)->sortByDesc('product_name')->reverse()->values()->toArray());
    }

    public function pointHistories(Request $request)
    {
        try {
            $month = (int)$request->query('month');
            $now = Carbon::now();
            $currentYear = $now->year;

            $user = $request->user();

            if ($month) {
                $shopPoint = ShopPoint::where('customer_id', '=', $user->id)
                    ->where('year', '=', $currentYear)
                    ->where('month', '=', $month)
                    ->first();

                $pointHistories = [];
                if ($shopPoint) {
                    $pointHistory = ShopPointHistory::with('order')->where('point_id', '=', $shopPoint->id)->orderBy('created_at', 'DESC')->get()
                        ->groupBy(function ($row) {
                            return Carbon::parse($row->order->delivery_time)->format('d/m/Y'); // grouping by years
                            //return Carbon::parse($date->created_at)->format('m'); // grouping by months
                        });
                    if ($pointHistory) {
                        foreach ($pointHistory as $key => $value) {
                            array_push($pointHistories, (object)[
                                'day' => $key,
                                'details' => $value,
                                'points' => array_sum(data_get($value, '*.actual_point'))
                            ]);
                        }
                    }

                }

                $data = [
                    'totalPoint' => $shopPoint ? $shopPoint->point : 0,
                    'histories' => $pointHistories,
                    'points' => 0
                ];

                return $this->responseSuccess($data);
            } else {
                $shopPoints = ShopPoint::where('customer_id', '=', $user->id)
                    ->where('year', '=', $currentYear)
                    ->orderBy('month', 'DESC')->get();

                $totalPoint = 0;
                if (!$shopPoints) {
                    $data = [
                        'totalPoint' => $totalPoint,
                        'histories' => []
                    ];
                    return $this->responseSuccess($data);
                }

                $totalPoint = 0;
                $pointHistories = [];
                foreach ($shopPoints as $shopPoint) {
                    $totalPoint += $shopPoint->point;
                    $pointHistory = ShopPointHistory::with('order')->where('point_id', '=', $shopPoint->id)
                        ->orderBy('created_at', 'DESC')->get()
                        ->groupBy(function ($row) {
                            return Carbon::parse($row->order->delivery_time)->format('d/m/Y'); // grouping by years
                            //return Carbon::parse($date->created_at)->format('m'); // grouping by months
                        });
                    if ($pointHistory) {
                        foreach ($pointHistory as $key => $value) {
                            array_push($pointHistories, (object)[
                                'day' => $key,
                                'details' => $value,
                                'points' => array_sum(data_get($value, '*.actual_point'))
                            ]);
                        }
                    }
                }
                $data = [
                    'totalPoint' => $totalPoint,
                    'histories' => $pointHistories
                ];

                return $this->responseSuccess($data);

            }

        } catch (\Exception $e) {
            return $this->responseError($e->getMessage(), 'Có lỗi xảy ra, vui lòng liên hệ admin!');
        }
    }

    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $data = [];
        $notifications = AdminNotificationCustomer::with('notification')->where('customer_id', $user->id)
            ->orderBy('created_at', 'DESC')->paginate(config('pagination.notification'));
        $unreadNotifcations = AdminNotificationCustomer::where('customer_id', $user->id)->where(function ($query) {
            $query->whereNull('seen')
                ->orWhere('seen', '=', 0);
        })->get();
        $unread = count($unreadNotifcations);
        foreach ($notifications as $notification) {
            $notificationDescription = $notification->notification;
            if (!empty($notificationDescription)) {
                $data[] = [
                    'id' => $notification->id,
                    'title' => $notificationDescription->title ?? "",
                    'content' => $notificationDescription->content ?? "",
                    'icon' => $notificationDescription->icon ?? "",
                    'seen' => $notification->seen,
                    'created_at' => $notification->created_at,
                ];
            }
        }
        $responseData = [
            'page' => $notifications->currentPage(),
            'more' => $notifications->hasMorePages(),
            'notifications' => $data,
            'unread' => $unread
        ];
        return $this->responseSuccess($responseData);
    }

    public function readNotification($id)
    {
        $notification = AdminNotificationCustomer::find($id);
        $notification->seen = 1;
        $notification->update();

        return $this->responseSuccess();
    }

    public function readAllNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = AdminNotificationCustomer::where('customer_id', $user->id)->get();
        foreach ($notifications as $notification) {
            $notification->seen = 1;
            $notification->update();
        }

        return $this->responseSuccess();
    }

    /**
     * Remove account
     */
    public  function removeAccount(Request $request){
        DB::beginTransaction();
        try{
            $dataUser = $request->user();
            $dataUser->status = 0;
            $dataUser->email = 'removed-'. Carbon::now()->timestamp . '-' . $dataUser->email;
            $dataUser->customer_code = 'removed-'. Carbon::now()->timestamp . '-' . $dataUser->customer_code;
            $dataUser->save();

            DB::commit();
            return $this->responseSuccess();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseError($e->getMessage(), 'Error while remove account!');
        }
    }

}
