<?php

namespace App\Admin\Api\Warehouse\Admin;

use App\Admin\Api\ApiController;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopOrderHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminOrderDavicorpController extends ApiController
{
    const DEFAULT_PAGE_NUM = 1;
    const DEFAULT_PAGE_SIZE = 20;
    /**
     * Get authenticated user
     */
    public function getListOrder(Request $request)
    {
        $pageNum = $request->get("page_num");
        if (!isset($pageNum)){
            $pageNum = self::DEFAULT_PAGE_NUM;
        }

        $pageSize = $request->get("page_size");
        if (!isset($pageSize)){
            $pageSize = self::DEFAULT_PAGE_SIZE;
        }

        try {
            $date_start = $request->date_start ? date($request->date_start) : '';
            $date_end = $request->date_end ? date($request->date_end) : '';
            $object_id = $request->object_id;
            $explain = $request->explain;
            $keyword = $request->keyword;
            $orders = ShopOrder::where('status', '!=', 7);
            if ($request->date_from){
                $orders = $orders->whereDate('delivery_time', '>=', $date_start);
            }
            if ($request->date_to){
                $orders = $orders->whereDate('delivery_time', '<=', $date_end);
            }
            if ($explain != ''){
                $orders = $orders->where('explain', '=', $request->explain);
            }
            if ($object_id != ''){
                $orders = $orders->where('object_id', '=', $request->object_id);
            }
            if ($keyword) {
                $orders = $orders->where(function ($q) use ($keyword){
                    $q->where('id_name', 'like', '%' . $keyword . '%');
                    $q->orWhere('name', 'like', '%' . $keyword . '%');
                    $q->orWhere('customer_code', 'like', '%' . $keyword . '%');
                });
            }
            $orders = $orders->whereDate('delivery_time', '>=', '2023-3-1');
            $orders = $orders->orderBy('delivery_time', 'DESC')->orderBy('created_at', 'DESC');
            $orders = $orders->paginate($perPage = $pageSize, $columns = ['*'], $pageName = 'page_num', $page = $pageNum);

            return $this->responseSuccess($orders);
        } catch (\Throwable $e) {
            Log::error($e);
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    public function detailOrder($id)
    {
        $orders = ShopOrder::with('details', 'history')->find($id);
        if ($orders) {
            return $this->responseSuccess($orders);
        }
        return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Không tìm thấy đơn hàng này!');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request, $id)
    {
        $user = $request->user('admin-api');
        $data = $request->all('edit_items');
        $order = ShopOrder::find($id);
        $contentHistory = [];
        DB::beginTransaction();
        try {
            foreach ($data as $item) {
                $detail = ShopOrderDetail::find($item['detail_id']);
                if ($detail) {
                    if ($item->qty_reality != $item['qty_reality']) {
                        $detail->qty_reality = $item['qty_reality'];
                        $detail->reality_total_price = $item['qty_reality'] * $item->price;
                        $contentHistory[] = '- Sản phẩm :'.$detail->product_name.'. Thay đổi từ \"'.$detail->qty_reality.'\" -> \"'.$item['qty_reality'];
                        $detail->save();
                    }
                }
            }
            $title = 'Thủ kho sửa số lượng thực tế';
            $content = implode("<br>", $contentHistory);
            $this->storeHistoryOrder($user, $order, $title, $content);
            $this->updateTotalOrder($id);
            DB::commit();

            return $this->responseSuccess([], Response::HTTP_OK, 'Cập nhập thành công!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Lỗi mạng. Vui lòng thử lại!');
        }
    }

    /**
     * @param Request $request
     */
    public function checkBarcodeOrder(Request $request)
    {
        $id_barcode = $request->id_barcode;
        $detail = ShopOrderDetail::where('id_barcode', $id_barcode)->first();
        $order = ShopOrder::find($detail->order_id);
        $data = [
            'qty_reality' => $detail->qty_reality,
            'qty' => $detail->qty,
            'id_name' => $order->id_name,
            'customer_name' => $order->customer_name,
            'delivery_date' => $order->delivery_time,
        ];

        return $this->responseSuccess($data);
    }

    /**
     * @param $user
     * @param $order
     * @param $title
     * @param $content
     */
    public function storeHistoryOrder($user, $order, $title, $content)
    {
        $dataHistory = [
            'order_id' => $order->id,
            'order_code' => $order->id_name,
            'title' => $title,
            'content' => $content,
            'admin_id' => $user->id,
            'user_name' => $user->name,
            'is_admin' => 1,
            'order_status_id' => 4,
            'add_time' => now(),
        ];
        ShopOrderHistory::create($dataHistory);
    }

    private function updateTotalOrder($idOrder)
    {
        $order = ShopOrder::with('details')->find($idOrder);
        $order->total = $order->details->sum('total_price');
        $order->actual_total_price = $order->details->sum('reality_total_price');
        $order->edit = 1;
        $order->save();
    }

}