<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminHoliday;
use App\Admin\Models\AdminProduct;
use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopZone;
use App\Http\Requests\Admin\AdminZoneRequest;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminHolidayController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $editOrderWithWeekend = AdminHoliday::find(1);
        $listHolidays = AdminHoliday::where('type', 'holiday')->orderBy('id', 'desc')->get();
        $getBlockByDateRange = AdminHoliday::where('type', 'date_range')->first();

        return view($this->templatePathAdmin . 'screen.holidays.index')->with(
            [
                "title" => 'Thiết lập kỳ nghỉ lễ',
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "editOrderWithWeekend" => $editOrderWithWeekend,
                "listHolidays" => $listHolidays,
                "getBlockByDateRange" => $getBlockByDateRange,
            ]
        );
    }

    /**
     * Tạo mới Kỳ nghĩ lễ
     * @return \Illuminate\Http\JsonResponse
     */
    public function store() {
        $names = request('name');
        $start_dates = request('start_date');
        $end_dates = request('end_date');
        DB::beginTransaction();
        try {
            foreach ($names as $key => $name) {
                $start_date =  convertVnDateObject($start_dates[$key])->toDateString();
                $end_date =  convertVnDateObject($end_dates[$key])->toDateString();
                $edit_date = convertVnDateObject($end_dates[$key])->addDay(1)->toDateString();
                if ($start_date > $end_date) {
                    return response()->json(['error' => 1, 'msg' => "Ngày kết thúc nhỏ hơn ngày bắt đầu. Vui lòng kiểm tra lại"]);
                }
                $dataInsert = [
                    'name' => $name,
                    'status' => 1,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'edit_date' => $edit_date,
                    'type' => 'holiday',
                ];
                AdminHoliday::create($dataInsert);
            }
        } catch(\Throwable $e) {
            DB::rollBack();
            Log::debug($e);

            return response()->json(['error' => 1, 'msg' => "Lỗi!. Thêm mới không thành công"]);
        }
        DB::commit();

        return response()->json([
            'error' => 0,
            'msg' => "Thêm mới thành công"
        ]);
    }

    /**
     * Update data
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update() {
        $data = request()->all();
        DB::beginTransaction();
        try {
            $holiday = AdminHoliday::find($data['pk']);
            if (!$holiday) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $data['pk']]), 'detail' => '']);
            }
            if ($data['name'] == 'end_date') {
                $holiday->end_date = $data['value'];
                $holiday->edit_date = Carbon::createFromFormat('Y-m-d', $data['value'])->addDay(1);
                if (!$holiday->save()) {
                    throw new \Exception('Lỗi cập nhật kỳ nghỉ lễ, vui lòng kiểm tra lại!');
                }
            }
            if (!$holiday->update([$data['name'] => $data['value']])) {
                throw new \Exception('Lỗi cập nhật kỳ nghỉ lễ, vui lòng kiểm tra lại!');
            }
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'error' => 1,
                'msg' => 'Thay đổi ngày lỗi!'
            ]);
        }
        DB::commit();

        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);
    }

    public function changeStatus() {
        $id = request('id');
        try {
            $holiday = AdminHoliday::find($id);
            if (!$holiday) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $id]), 'detail' => '']);
            }
            if ($holiday->status == 1) {
                $holiday->status = 2;
                $holiday->save();
            } else {
                $holiday->status = 1;
                $holiday->save();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'error' => 1,
                'msg' => sc_language_render('action.update_fail')
            ]);
        }
        DB::commit();

        return response()->json([
            'error' => 0,
            'msg' => sc_language_render('action.update_success')
        ]);

    }

    /**
     * handle delete
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete()
    {
        try {
            $data = request()->all();
            $pId = $data['pId'] ?? "";
            $itemHoliday = AdminHoliday::find($pId);
            if (!$itemHoliday) {
                return response()->json(['error' => 1, 'msg' => sc_language_render('admin.data_not_found_detail', ['msg' => 'detail#' . $pId]), 'detail' => '']);
            }
            $itemHoliday->delete(); //Remove item from shop order detail

            return response()->json(['error' => 0, 'msg' => sc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function checkngaythu7cn() {
        $id = 'O-wFDmg-7yhNA';
        $order = ShopOrder::find($id);
        $deliveryTime = $order->delivery_time;
        $now = Carbon::now();
        $startOfWeek = Carbon::now()->startOfWeek()->addDay(1)->addHour(11);
        $endOfWeek = Carbon::now()->endOfWeek();
        $checkBlockOrderSatAndSun = AdminHoliday::where('type', 'everyday')->first();

        if ($checkBlockOrderSatAndSun->status == 1) {
            if ($now >= $startOfWeek && $now <= $endOfWeek) {
                if ($deliveryTime >= $startOfWeek && $deliveryTime <= $endOfWeek->addDay(1)) {
                    dd('Không thể sửa đơn hàng vào Chủ nhật và Thú 2 tuần tới');
                    return $this->responseError([], Response::HTTP_BAD_REQUEST,
                        "Không thể sửa đơn hàng vào Chủ nhật và Thú 2 tuần tới");
                } else {
                    dd('không cần check 1', $endOfWeek->addDay(1), $deliveryTime);
                }
            } else {
                dd('không cần check 2');
            }
        }


//        Carbon::now()->endOfWeek()->format('Y-m-d H:i')
//        dd($now->endOfWeek()->format('Y-m-d H:i:s'), $startOfWeek->format('Y-m-d H:i:s'));
//        dd($now->endOfWeek()->format('Y-m-d H:i'));
    }


}
