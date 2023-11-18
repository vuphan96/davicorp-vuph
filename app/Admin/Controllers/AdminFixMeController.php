<?php

namespace App\Admin\Controllers;
use App\Admin\Models\AdminDavicookMenuCardChildren;
use App\Admin\Models\AdminDavicookMenuCardChilldenDetail;
use App\Admin\Models\AdminHoliday;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderHistory;
use App\Front\Models\ShopPointHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminFixMeController extends RootAdminController
{
    //    FIXME: DELETE LATE
    public function fakeDateMenuCard()
    {
        $menuCard = AdminDavicookMenuCardChildren::with('children')->get();
        foreach ($menuCard as $detail) {
            $detail->bill_date = $detail->date;
            foreach ($detail->children as $value) {
                $value->date_for_dish = $detail->date;
                $value->save();
            }
            $detail->save();
        }

        return 'OK';

    }

    //    FIXME: DELETE LATE
    public function fakeRealityPriceOrder()
    {
        $order = ShopOrder::with('details')->orderByDesc('created_at')->where('actual_total_price', null)->take(350)->get();
        foreach ($order as $item) {
            $total = 0;
            foreach ($item->details as $detail) {
                $detail->reality_total_price = $detail->price * $detail->qty_reality;
                $total += $detail->price * $detail->qty_reality;
                $detail->save();
            }
            $item->actual_total_price = $total;
            $item->save();
        }

        return 'OK';

    }

    //    FIXME: DELETE LATE
    public function createDateRange()
    {
        $names = 'Khóa đặt hàng theo khoản ngày cho trước';
        DB::beginTransaction();
        try {
            $start_date =  convertVnDateObject('1/3/2023')->toDateString();
            $end_date =  convertVnDateObject('1/3/2023')->toDateString();
            $edit_date = convertVnDateObject('1/3/2023')->addDay(1)->toDateString();
            $dataInsert = [
                'name' => $names,
                'status' => 1,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'edit_date' => $edit_date,
                'type' => 'date_range',
            ];
            AdminHoliday::create($dataInsert);
        } catch(\Throwable $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['error' => 1, 'msg' => "Lỗi!. Thêm mới không thành công"]);
        }
        DB::commit();

        return 'OK';
    }

    //    FIXME: DELETE LATE
    # Fake số lượng thực tế
    public function updateRealityPoint()
    {
        DB::beginTransaction();
        try {
            $data = ShopPointHistory::whereColumn('change_point', '<>', 'actual_point')->limit(2000)->get();
            foreach ($data as $key => $item ){
                $item->actual_point = $item->change_point;
                $item->save();
            }
        } catch(\Throwable $e) {
            DB::rollBack();
            dd($e);
        }
        DB::commit();

        return 'OK';
    }

    //    FIXME: DELETE LATE
    # Fake số lượng thực tế
    public function updateActionPoint()
    {
        DB::beginTransaction();
        try {
            $data = ShopPointHistory::where('actual_point', 0)->orderBy('created_at', 'DESC')->get();
            foreach ($data as $key => $item ){
                $item->action = 0;
                $item->save();
            }
        } catch(\Throwable $e) {
            DB::rollBack();
            dd($e);
        }
        DB::commit();

        return 'OK';
    }

    public function updateOrderAdminDavicorp()
    {
        DB::beginTransaction();
        try {
            $data = ShopOrder::whereDate('delivery_time', '>=', '2023-3-1')->where('is_order_admin', null)->limit(1000)->orderBy('created_at', 'desc')->get();
            foreach ($data as $item) {
                $history = ShopOrderHistory::where('order_id', $item->id)->where('title', 'Tạo đơn hàng mới')->orderBy('add_date', 'desc')->first();
                if ($history) {
                    $is_admin = 1 ;
                } else {
                    $is_admin = 0;
                }
                $item->timestamps = false;
                $item->is_order_admin = $is_admin;
                $item->save(['timestamps' => false]);

            }
        } catch(\Throwable $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['error' => 1, 'msg' => "Lỗi!. Thêm mới không thành công"]);
        }
        DB::commit();

        return 'OK';
    }

    public function updateDateForProductMenuCard()
    {
        DB::beginTransaction();
        try {
            $data = AdminDavicookMenuCardChilldenDetail::where('date_for_product', null)->get();
            foreach ($data as $item) {
                $item->date_for_product = $item->date_for_dish;
                $item->save(['timestamps' => false]);
            }
        } catch(\Throwable $e) {
            DB::rollBack();
            dd($e);
            return response()->json(['error' => 1, 'msg' => "Lỗi!. Thêm mới không thành công"]);
        }
        DB::commit();

        return 'OK';
    }

    public function TESTLOGDAVICORP()
    {
        $id = [910921, 910920, 910916, 910912, 910909, 910826, 910348, 910344, 910322];
        $arr = [];
        $test = DB::table('sc_admin_log')->whereIn('id',$id)->select('created_at', 'input')->get();
        foreach ($test as $key => $item) {
            $arr[$key]['time'] = $item->created_at;
            $arr[$key]['input'] = json_decode($item->input, true);
        }
        dd($arr);
    }
}
