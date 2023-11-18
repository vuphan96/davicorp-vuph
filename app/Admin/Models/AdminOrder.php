<?php

namespace App\Admin\Models;

use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookOrderDetail;
use App\Front\Models\ShopImportPriceboard;
use App\Front\Models\ShopImportPriceboardDetail;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopProductSupplier;
use App\Front\Models\ShopOrderHistory;
use Cache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Sodium\library_version_minor;

class AdminOrder extends ShopOrder
{
    public static $mapStyleStatus = [
        '1' => 'success', //Đang khả dụng
        '2' => 'warning', //Đơn nháp
        '7' => 'danger', //Đã hủy
    ];

    /**
     * Get order detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getOrderAdmin($id, $storeId = null)
    {
        $data = self::with(['returnHistory', 'customer'])
            ->with(['details' => function ($query) {
                $query->orderBy('created_at', 'ASC');
            }])
            ->where('id', $id)
            ->orWhere('id_name', $id);
        return $data->first();
    }

    /**
     * Get list order in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getOrderListAdmin(array $dataSearch, $all = null)
    {
        $department = $dataSearch['order_department'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $start_day = $dataSearch['start_day'] ?? '';
        $end_day = $dataSearch['end_day'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $limit = $dataSearch['limit'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $order_object = $dataSearch['order_object'];
        $order_explain = $dataSearch['order_explain'];
        $order_status = $dataSearch['order_status'];
        $order_purchase_priority_level = $dataSearch['order_purchase_priority_level'];
        $code = $dataSearch['code'];
        $customer_id = $dataSearch['customer'];
        $option_date = $dataSearch['option_date'] == 2 ? 'bill_date' : 'delivery_time';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();


        $orderList = (new ShopOrder)->with('object', 'status', 'details', 'customer', 'returnHistory');
        if ($code) {
            $orderList = $orderList->where(function ($sql) use ($code) {
                $sql->where('id_name', 'like', '%'.$code.'%')
                    ->orWhere('name', 'like', '%'.$code.'%')
                    ->orWhere('customer_code', 'like', '%'.$code.'%');
            });
        }

        if ($order_status != '') {
            $orderList = $orderList->where('status', (int)$order_status);
        }

        if ($customer_id) {
            $orderList = $orderList->whereIn('customer_code', $customer_id);
        }

        if ($order_purchase_priority_level != '') {
            $order_ids = ShopOrderDetail::where('product_priority_level', 1)->distinct()->get(['order_id'])->pluck('order_id');
            ($order_purchase_priority_level == 1) 
            ?
            $orderList = $orderList->whereIn('id', $order_ids)
            :
            $orderList = $orderList->whereNotIn('id', $order_ids);
        }

        if ($order_object != '') {
            $orderList = $orderList->where('object_id', (int)$order_object);
        }

        if ($order_explain != '') {
            $orderList = $orderList->where('explain', $order_explain);
        }
        
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($from_to, $option_date) {
                $sql->where($option_date, '>=', $from_to);
            });
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($end_to, $option_date) {
                $sql->where($option_date, '<=', $end_to);
            });
        }
        if ($start_day) {
            $orderList = $orderList->where(function ($sql) use ($startToDay) {
                $sql->where('created_at', '>=', $startToDay);
            });
        }
        if ($end_day) {
            $orderList = $orderList->where(function ($sql) use ($endToDay) {
                $sql->where('created_at', '<=', $endToDay);
            });
        }

        if ($department) {
            $orderList = $orderList->whereHas('customer', function ($query) use ($department) {
                $query->where('department_id', '=', $department);
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $orderList = $orderList->orderBy($field, $sort_field);
        } else {
            $orderList = $orderList->orderBy('created_at', 'desc');
        }

        $sum = $orderList->sum('actual_total_price');

        if ($limit) {
            return $data = [
                'sum' => $sum,
                'orderList' => $orderList->paginate($limit),
            ];
        }

        return $data = [
            'sum' => $sum,
            'orderList' => $orderList->paginate(config('pagination.admin.order')),
        ];
    }

    /**
     * @param array $dataSearch
     * @param null $all
     * @return Builder[]|Collection
     */
    public static function getReturnOrderDavicorp(array $dataSearch, $all = null)
    {
        $department = $dataSearch['order_department'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_object = $dataSearch['order_object'];
        $order_explain = $dataSearch['order_explain'];
        $order_status = $dataSearch['order_status'];
        $order_purchase_priority_level = $dataSearch['order_purchase_priority_level'];
        $code = $dataSearch['code'];
        $customer_id = $dataSearch['customer'];
        $option_date = $dataSearch['option_date'] == 2 ? 'bill_date' : 'delivery_time';

        $orderList = (new ShopOrder)->with('customer', 'returnHistory', 'returnHistory.detail');
        if ($code) {
            $orderList = $orderList->where(function ($sql) use ($code) {
                $sql->where('id_name', 'like', '%'.$code.'%')
                    ->orWhere('name', 'like', '%'.$code.'%')
                    ->orWhere('customer_code', 'like', '%'.$code.'%');
            });
        }

        if ($order_status != '') {
            $orderList = $orderList->where('status', (int)$order_status);
        }

        if ($customer_id) {
            $orderList = $orderList->whereIn('customer_code', $customer_id);
        }

        if ($order_purchase_priority_level != '') {
            $order_ids = ShopOrderDetail::where('product_priority_level', 1)->distinct()->get(['order_id'])->pluck('order_id');
            ($order_purchase_priority_level == 1)
                ?
                $orderList = $orderList->whereIn('id', $order_ids)
                :
                $orderList = $orderList->whereNotIn('id', $order_ids);
        }

        if ($order_object != '') {
            $orderList = $orderList->where('object_id', (int)$order_object);
        }

        if ($order_explain != '') {
            $orderList = $orderList->where('explain', $order_explain);
        }

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($from_to, $option_date) {
                $sql->where($option_date, '>=', $from_to);
            });
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($end_to, $option_date) {
                $sql->where($option_date, '<=', $end_to);
            });
        }

        if ($department) {
            $orderList = $orderList->whereHas('customer', function ($query) use ($department) {
                $query->where('department_id', '=', $department);
            });
        }

        return $orderList->orderBy('delivery_time', 'asc')->orderBy('name', 'asc')->get();
    }

    /**
     * Update new sub total
     * @param  [int] $orderId [description]
     * @return [type]           [description]
     */
    public static function updateSubTotal($orderId)
    {
        try {
            $order = self::getOrderAdmin($orderId);
            $details = $order->details;
            $tax = $subTotal = $actualTotal = 0;
            if ($details->count()) {
                foreach ($details as $detail) {
                    $tax += $detail->tax;
                    $subTotal += $detail->total_price;
                    $actualTotal += $detail->reality_total_price;
                }
            }
            $order->subtotal = $subTotal;
            $order->tax = $tax;
            $total = $subTotal + $tax + $order->discount + $order->shipping;
            $balance = $total + $order->received;
            $payment_status = 0;
            $order->payment_status = $payment_status;
            $order->total = $total;
            $order->actual_total_price = $actualTotal;
            $order->balance = $balance;
            $order->save();
            return 1;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * Update purchase priority level order
     * @param $orderId 
     */
    public static function UpdatePurchasePriorityLevel($orderId)
    {
        $item_detail = ShopOrderDetail::where('order_id', $orderId)->where('product_priority_level', 1)->first();
        $order = AdminOrder::getOrderAdmin($orderId);
        if (isset($item_detail)) {
            $order->purchase_priority_level = 1;
        } else {
            $order->purchase_priority_level = 0;
        }
        $order->save();
    }

    /**
     * Update order status when order has product price = 0
     * @param $orderId 
     */
    public static function UpdateStatusOrder($orderId) {
        $items = ShopOrderDetail::where('order_id', $orderId)->where('price', 0)->get();
        if(count($items)>0) {
            ShopOrder::findOrFail($orderId)->update(['status' => 2]);
        } else {
            ShopOrder::findOrFail($orderId)->update(['status' => 1]);
        }
    }

    /**
     * Load history when update detail order
     */
    public static function loadHistory($id) {
        $order_history = ShopOrderHistory::where('order_id',$id)->orderBy('add_date', 'Desc')->get();
        $arrayReturn = '';
        if ($order_history) {
            foreach($order_history as $v) {
                $arrayReturn .= '<tr>
                                    <td>'.$v->add_date.'</td>
                                    <td>'.$v->getEditor().'</td>
                                    <td>'.$v->content.'</td>
                                 </tr>';
            }
            $arrayReturn = str_replace("\n", '', $arrayReturn);
            $arrayReturn = str_replace("\t", '', $arrayReturn);
            $arrayReturn = str_replace("\r", '', $arrayReturn);
            $arrayReturn = str_replace("'", '"', $arrayReturn);
        }
        return $arrayReturn;       
    }

    /**
     * Get country order in year
     *
     * @return  [type]  [return description]
     */
    public static function getCountryInYear()
    {
        return self::selectRaw('country, count(id) as count')
            ->whereRaw('DATE(created_at) >=  DATE_SUB(DATE(NOW()), INTERVAL 12 MONTH)')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get device order in year
     *
     * @return  [type]  [return description]
     */
    public static function getDeviceInYear()
    {
        return self::selectRaw('device_type, count(id) as count')
            ->whereRaw('DATE(created_at) >=  DATE_SUB(DATE(NOW()), INTERVAL 12 MONTH)')
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    /**
     * Get Sum order total In Year
     *
     * @return  [type]  [return description]
     */
    public static function getSumOrderTotalInYear()
    {
        return self::selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, SUM(total/exchange_rate) AS total_amount')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH), "%Y-%m")')
            ->groupBy('ym')->get();
    }

    /**
     * Get count order in Year
     *
     * @return  [type]  [return description]
     */
    public static function getCountOrderTotalInYear()
    {
        return self::selectRaw('DATE_FORMAT(created_at, "%Y-%m") AS ym, count(*) AS count')
            ->whereRaw('DATE_FORMAT(created_at, "%Y-%m") >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH), "%Y-%m")')
            ->groupBy('ym')->get();
    }

    /**
     * Get Sum order total In month
     *
     * @return  [type]  [return description]
     */
    public static function getSumOrderTotalInMonth()
    {
        return self::selectRaw('DATE_FORMAT(delivery_time, "%m-%d") AS md,
        SUM(total) AS total_amount, count(id) AS total_order')
            ->where('status', 1)
            ->whereRaw('delivery_time >=  DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH), "%Y-%m-%d")')
            ->groupBy('md')->get();
    }


    /**
     * Get total order of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalOrder()
    {
        return self::count();
    }


    /**
     * Get count order new
     *
     * @return  [type]  [return description]
     */
    public static function getCountOrderNew()
    {
        return self::where('status', 1)
            ->count();
    }

    /**
     * Get total order of system
     *
     * @return  [type]  [return description]
     */
    public static function getTopOrder()
    {
        return self::with('orderStatus')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    /**
     * Sum amount order
     *
     * @param   [type]  $storeId  [$storeId description]
     *
     * @return  [type]            [return description]
     */
    public static function getSumAmountOrder($storeId = null)
    {
        $data = (new AdminOrder)
            ->selectRaw('sum(total) as total_sum, currency')
            ->where('status', 5);//Only process order completed
        if ($storeId) {
            $data = $data->where('store_id', $storeId);
        }
        $data = $data->groupBy('currency')
            ->get()
            ->toArray();
        return $data;
    }

    public static function getOrerPrintData($id)
    {
        $order = AdminOrder::findOrFail($id);
        $details = $order->details;

        return [
            'order' => $order ?? (object)[],
            'details' => $details ?? []
        ];
    }

    //Báo cáo bán hàng nhóm theo 2 chỉ tiêu
    public function getSumQtyProductOrder(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objProduct = new ShopProduct();
        $dataProduct = $objProduct->rightjoin(SC_DB_PREFIX . "shop_order_detail as sod", function ($join) {
            $join->on("sod.product_id", SC_DB_PREFIX . "shop_product.id");
        })
            ->join(SC_DB_PREFIX . "shop_order as so", function ($join) {
                $join->on("so.id", "sod.order_id");
            })
            ->leftjoin(SC_DB_PREFIX . "shop_product_description as spd", function ($join) {
                $join->on("spd.product_id", SC_DB_PREFIX . "shop_product.id")->where("spd.lang", sc_get_locale());
            })
            ->leftjoin(SC_DB_PREFIX . "shop_category as sc", function ($join) {
                $join->on("sc.id", SC_DB_PREFIX . "shop_product.category_id");
            });
        if ($from_to) {
            $from_to = Carbon::createFromFormat('Y-m-d', $from_to)->startOfDay()->toDateTimeString();
            $dataProduct = $dataProduct->where('so.delivery_time', '>=', $from_to);
        }
        if ($end_to) {
            $end_to = Carbon::createFromFormat('Y-m-d', $end_to)->endOfDay()->toDateTimeString();
            $dataProduct = $dataProduct->where('so.delivery_time', '<=', $end_to);
        }
        if (empty($from_to) && empty($end_to)) {
            $dataProduct = $dataProduct->where('so.delivery_time', ">=", $startToDay)
                ->where('so.delivery_time', "<=", $endToDay);
        }
        if ($category) {
            $dataProduct = $dataProduct->where(SC_DB_PREFIX . "shop_product.category_id", $category);
        }
        if ($keyword) {
            $dataProduct = $dataProduct->where("spd.name", "like", "%" . $keyword . "%");

        }
        $dataProduct = $dataProduct->where('so.status', '=', 1)
            ->select("sod.product_id", SC_DB_PREFIX . "shop_product.id", DB::raw('SUM(sod.qty) as qty'))
            ->with("description_native")
            ->groupBy("sod.product_id")->orderBy('sc.sort', 'ASC')->orderBy('spd.name', 'ASC');

        return $dataProduct;
    }

    public function getDetailOrder(array $dataSearch, $productId)
    {

        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminOrder();
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_order_detail as sod", function ($join) {
            $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
        })
            ->leftjoin(SC_DB_PREFIX . "shop_customer as sc", function ($join) {
                $join->on("sc.id", SC_DB_PREFIX . "shop_order.customer_id");
            })
            ->leftjoin(SC_DB_PREFIX . "shop_product as sp", function ($join) {
                $join->on("sod.product_id", "sp.id");
            });

        if ($from_to) {
            $from_to = Carbon::createFromFormat('Y-m-d', $from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_order.delivery_time", ">=", $from_to);
        }
        if ($end_to) {
            $end_to = Carbon::createFromFormat('Y-m-d', $end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_order.delivery_time", "<=", $end_to);
        }
        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_order.delivery_time", ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_order.delivery_time", "<=", $endToDay);
        }
        if ($category) {
            $dataTmp = $dataTmp->where("sp.category_id", $category);
        }
        $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_order.status", 1)
            ->where("sod.product_id", $productId)
            ->select(SC_DB_PREFIX . "shop_order.name", "sc.customer_code", "sod.product_id", "sod.comment", 'sod.qty')
            ->orderBy("sod.id", 'DESC');
        return $dataTmp;
    }

    // báo cáo ghi chú order
    public function getNoteReportOrder(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $name = $dataSearch['name'] ?? '';
        $dataTmp = AdminOrder::whereIn("status", [1, 2]);
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("delivery_time", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("delivery_time", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where("delivery_time", ">=", $startToDay)
                ->where("delivery_time", "<=", $endToDay);
        }

        if ($name) {
            $dataTmp = $dataTmp->where(function ($sql) use ($name) {
                $sql->where('name', 'like', '%' . $name . '%')
                    ->orWhere('customer_code', 'like', '%' . $name . '%')
                    ->orWhere('comment', 'like', '%' . $name . '%');
            });
        }

        $dataTmp = $dataTmp->with('details')->orderBy('name', 'ASC');

        return $dataTmp;
    }


    // báo cáo dah thu khách hàng
    public function getRevenueReportOrder(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $department = $dataSearch['department'] ?? '';
        $object = $dataSearch['object'] ?? '';
        $note = $dataSearch['note'] ?? '';
        $nameTableOrder = (new ShopOrder())->table;
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = (new ShopOrder());

        $dataTmp = $dataTmp->where($nameTableOrder . ".status", '!=', 7);

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrder . ".delivery_time", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrder . ".delivery_time", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where($nameTableOrder . ".delivery_time", ">=", $startToDay)
                ->where($nameTableOrder . ".delivery_time", "<=", $endToDay);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrder) {
                $sql->where($nameTableOrder . '.name', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrder.'.customer_code', 'like', '%' . $keyword . '%');
            });
        }

        if ($department) {
            $dataTmp = $dataTmp->where($nameTableOrder.".department_id", $department);
        }

        if ($object) {
            $dataTmp = $dataTmp->where($nameTableOrder . ".object_id", $object);
        }

        if ($note) {
            $dataTmp = $dataTmp->where($nameTableOrder . ".explain", $note);
        }

        $dataTmp = $dataTmp->select($nameTableOrder . '.name',
            $nameTableOrder . '.id_name',
            $nameTableOrder . '.delivery_time',
            $nameTableOrder . '.explain',
            $nameTableOrder . '.object_id',
            $nameTableOrder . '.customer_code',
            $nameTableOrder . '.actual_total_price');
        $dataTmp = $dataTmp->orderBy($nameTableOrder . ".id_name", 'DESC')->get();
        $dataReturn = new Collection();
        foreach ($dataTmp as $data) {
            $dataReturn->push([
                'delivery_date' => $data->delivery_time,
                'id_name' => $data->id_name,
                'customer_code' => $data->customer_code,
                'customer_name' => ($data->object_id == 1) ? $data->name . ' - GV' : $data->name,
                'explain' => $data->explain,
                'amount' => $data->actual_total_price ?? 0,
            ]);
        }

        return $dataReturn;
    }

    public function getStampList($dataSearch, array $ids = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $zone = $dataSearch['zone'] ?? [];
        $department = $dataSearch['department'] ?? [];
        $nameTableDetail = SC_DB_PREFIX . 'shop_order_detail';
//        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
//        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = ShopOrderDetail::with("product")->join(SC_DB_PREFIX . 'shop_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_detail.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_detail.category_id');
            })
            ->leftjoin(SC_DB_PREFIX . 'shop_department as ssd', function ($join) {
                $join->on('ssd.id', 'so.department_id');
            }) ->join(SC_DB_PREFIX . "shop_customer as scc", function ($join) {
                $join->on('so.customer_id', '=', 'scc.id');
            });
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.delivery_time", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.delivery_time", "<=", $end_to);
        }

//        if (empty($from_to) && empty($end_to)) {
//            $dataTmp = $dataTmp->where("so.delivery_time", ">=", $startToDay)
//                ->where("so.delivery_time", "<=", $endToDay);
//        }

        // Order date
        if ($order_date_from) {
            $order_date_from = convertStandardDate($order_date_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", ">=", $order_date_from);
        }
        if ($order_date_to) {
            $order_date_to = convertStandardDate($order_date_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", "<=", $order_date_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableDetail . ".category_id", $category);
        }
        if ($department) {
            $dataTmp = $dataTmp->whereIn('ssd.id', $department);
        }
        if ($zone) {
            $dataTmp = $dataTmp->whereIn('scc.zone_id', $zone);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableDetail) {
                $sql->where($nameTableDetail. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableDetail. '.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_detail.id', $ids);
        }
        $dataTmp = $dataTmp->with("product")->whereIn('so.status', [1, 2])->with('order.customer.department')
            ->select($nameTableDetail . '.product_id', $nameTableDetail . '.order_id', $nameTableDetail . '.id_barcode', $nameTableDetail . '.qty_reality', $nameTableDetail . '.id', 'so.delivery_time', $nameTableDetail . '.product_short_name as product_name',
                'so.customer_short_name as customer_name', $nameTableDetail. '.product_num', $nameTableDetail . '.product_unit as name_unit', 'so.customer_num',
                'ssd.short_name', 'so.name', 'so.object_id', $nameTableDetail . '.product_code as sku','ssd.id as department_id','scc.zone_id as zone_id')->get();
        $arrListStampDetails = [];
        $qrList = new \Illuminate\Support\Collection([]);
        foreach ($dataTmp as $item) {
            $url = $item->product ? ($item->product->qr_code ?? "") : "";
            if($url){
                $qrSearch = $qrList->where("url", $url)->first();
                if(!$qrSearch){
                    $qrList->push(ShopProduct::generateQr($url));
                }
            };
            $arrListStampDetails[] = [
                'id' => $item->id,
                'order_id' => $item->order_id,
                'id_barcode' => $item->id_barcode,
                'qty' => $item->qty_reality,
                'product_id' => $item->product_id,
                'delivery_time' => $item->delivery_time,
                'product_name' => $item->product_name,
                'customer_name' => $item->customer_name,
                'order_num' => $item->product_num,
                'name_unit' => $item->name_unit,
                'customer_num' => $item->customer_num,
                'short_name' => $item->short_name,
                'object_id' => $item->object_id,
                'product_sku' => $item->sku,
                'qr_code' => $item->product ? ($item->product->qr_code ?? "") : "",
                'customer_fullname' => $item->name ?? ""
            ];
        }

        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }

    /**
     * Lấy hàng Tươi davicook đổ về báo cáo intem
     * @param $dataSearch
     * @param array|null $ids
     * @param null $select_warehouse
     * @return array
     */
    public function getProductFreshDavicookToReportStamp($dataSearch, array $ids = null, $select_warehouse = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $nameTableDetail = SC_DB_PREFIX . 'shop_davicook_order_detail';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = ShopDavicookOrderDetail::join(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_davicook_order_detail.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id', SC_DB_PREFIX . 'shop_davicook_order_detail.category_id');
            });

        // Delivery date
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.delivery_date", ">=", $from_to);
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.delivery_date", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where("so.delivery_date", ">=", $startToDay)
                ->where("so.delivery_date", "<=", $endToDay);
        }

        // Order date
        if ($order_date_from) {
            $order_date_from = convertStandardDate($order_date_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", ">=", $order_date_from);
        }
        if ($order_date_to) {
            $order_date_to = convertStandardDate($order_date_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", "<=", $order_date_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableDetail . ".category_id", $category);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableDetail) {
                $sql->where($nameTableDetail.'.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableDetail.'.product_code', 'like', '%' . $keyword . '%');
            });
        }

        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn($nameTableDetail . '.id', $ids);
        }

        if (!empty($select_warehouse)) {
            if ($select_warehouse == 2) {
                $dataTmp = $dataTmp->where($nameTableDetail . '.product_type', 0)->where('so.status', 2);
            } else {
                $dataTmp = $dataTmp->where($nameTableDetail . '.product_type', 1)->whereIn('so.status', [0, 1, 2]);
            }
        }
        $dataTmp = $dataTmp->with("product")
            ->where('so.status', '!=', 7)
            ->where($nameTableDetail. ".total_bom", '>', 0)
            ->select(
            $nameTableDetail . '.product_id',
            $nameTableDetail . '.order_id',
            $nameTableDetail . '.id_barcode',
            $nameTableDetail . '.real_total_bom',
            $nameTableDetail . '.id',
            $nameTableDetail . '.product_short_name as product_name',
            $nameTableDetail . '.product_code as sku',
            $nameTableDetail . '.product_num',
            $nameTableDetail . '.product_unit as name_unit',
            $nameTableDetail . '.type',
            'so.delivery_date',
            'so.export_date',
            'so.type as type_order',
            'so.customer_short_name as customer_name',
            'so.customer_name as fullname',
            'so.customer_num'
        )->get()->groupBy('order_id');

        $arrListStampDetails = [];
        $qrList = new Collection([]);
        foreach ($dataTmp as $value) {
            foreach ($value->where('type_order', 0)->where('type', 0)->groupBy('product_id') as $item) {
                try {
                    $url = $item->first()->product ? ($item->first()->product->qr_code ?? "") : "";
                    if($url){
                        $qrSearch = $qrList->where("url", $url)->first();
                        if(!$qrSearch){
                            $qrList->push(ShopProduct::generateQr($url));
                        }
                    };
                } catch (\Throwable $e){

                }
                $arrListStampDetails[] = [
                    'id' => $item->first()->id,
                    'order_id' => $item->first()->order_id,
                    'id_barcode' => $item->first()->id_barcode,
                    'qty' => $item->sum('real_total_bom'),
                    'product_id' => $item->first()->product_id,
                    'delivery_time' => $item->first()->delivery_date,
                    'product_name' => $item->first()->product_name,
                    'customer_name' => $item->first()->customer_name,
                    'order_num' => $item->first()->product_num,
                    'name_unit' => $item->first()->name_unit,
                    'customer_num' => $item->first()->customer_num,
                    'short_name' => 'CTCP DAVICOOK HN',
                    'object_id' => $item->first()->object_id,
                    'product_sku' => $item->first()->sku,
                    'qr_code' => $item->first() ? ($item->first()->product->qr_code ?? "") : "",
                    'customer_fullname' => $item->first()->fullname ?? "",
                ];
            }
            foreach ($value->where('type_order', 0)->where('type', 1)->groupBy('product_id') as $itemExtra) {
                try {
                    $url = $itemExtra->first()->product ? ($itemExtra->first()->product->qr_code ?? "") : "";
                    if($url){
                        $qrSearch = $qrList->where("url", $url)->first();
                        if(!$qrSearch){
                            $qrList->push(ShopProduct::generateQr($url));
                        }
                    };
                } catch (\Throwable $e){

                }
                $arrListStampDetails[] = [
                    'id' => $itemExtra->first()->id,
                    'order_id' => $itemExtra->first()->order_id,
                    'id_barcode' => $itemExtra->first()->id_barcode,
                    'qty' => $itemExtra->sum('real_total_bom'),
                    'product_id' => $itemExtra->first()->product_id,
                    'delivery_time' => $itemExtra->first()->delivery_date,
                    'product_name' => $itemExtra->first()->product_name,
                    'customer_name' => $itemExtra->first()->customer_name,
                    'order_num' => $itemExtra->first()->product_num,
                    'name_unit' => $itemExtra->first()->name_unit,
                    'customer_num' => $itemExtra->first()->customer_num,
                    'short_name' => 'CTCP DAVICOOK HN',
                    'object_id' => $itemExtra->first()->object_id,
                    'product_sku' => $itemExtra->first()->sku,
                    'qr_code' => $itemExtra->first() ? ($itemExtra->first()->product->qr_code ?? "") : "",
                    'customer_fullname' => $itemExtra->first()->fullname ?? "",
                ];
            }
            foreach ($value->where('type_order', 1) as $itemNecc) {
                try {
                    $url = $itemNecc->first()->product ? ($itemNecc->first()->product->qr_code ?? "") : "";
                    if($url){
                        $qrSearch = $qrList->where("url", $url)->first();
                        if(!$qrSearch){
                            $qrList->push(ShopProduct::generateQr($url));
                        }
                    };
                } catch (\Throwable $e){

                }
                $arrListStampDetails[] = [
                    'id' => $itemNecc->id,
                    'order_id' => $itemNecc->order_id,
                    'id_barcode' => $itemNecc->id_barcode,
                    'qty' => $itemNecc->real_total_bom,
                    'product_id' => $itemNecc->product_id,
                    'delivery_time' => $itemNecc->delivery_date,
                    'product_name' => $itemNecc->product_name,
                    'customer_name' => $itemNecc->customer_name,
                    'order_num' => $itemNecc->product_num,
                    'name_unit' => $itemNecc->name_unit,
                    'customer_num' => $itemNecc->customer_num,
                    'short_name' => 'CTCP DAVICOOK HN',
                    'object_id' => $itemNecc->object_id,
                    'product_sku' => $itemNecc->sku,
                    'qr_code' => $itemNecc ? ($itemNecc->product->qr_code ?? "") : "",
                    'customer_fullname' => $itemNecc->fullname ?? "",
                ];
            }
        }
        $arrListStampDetails = collect($arrListStampDetails)->sortBy('order_num')->toArray();

        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }

    /**
     * Lấy hàng KHÔ davicook đổ về báo cáo intem
     * @param $dataSearch
     * @param array|null $ids
     * @param null $select_warehouse
     * @return array
     */
    public function getProductDryDavicookToReportStamp($dataSearch, array $ids = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $nameTableDetail = SC_DB_PREFIX . 'shop_davicook_order_detail';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = ShopDavicookOrderDetail::join(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_davicook_order_detail.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id', SC_DB_PREFIX . 'shop_davicook_order_detail.category_id');
            });

        // Delivery date
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.export_date", ">=", $from_to);
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("so.export_date", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where("so.export_date", ">=", $startToDay)
                ->where("so.export_date", "<=", $endToDay);
        }

        // Order date
        if ($order_date_from) {
            $order_date_from = convertStandardDate($order_date_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", ">=", $order_date_from);
        }
        if ($order_date_to) {
            $order_date_to = convertStandardDate($order_date_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("so.created_at", "<=", $order_date_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableDetail . ".category_id", $category);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableDetail) {
                $sql->where($nameTableDetail.'.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableDetail.'.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn($nameTableDetail . '.id', $ids);
        }

        $dataTmp = $dataTmp->where($nameTableDetail . '.product_type', 0)->where('so.status', 2);
        $dataTmp = $dataTmp->with("product")
            ->where('so.status', '!=', 7)
            ->where($nameTableDetail. ".total_bom", '>', 0)
            ->select(
                $nameTableDetail . '.product_id',
                $nameTableDetail . '.order_id',
                $nameTableDetail . '.id_barcode',
                $nameTableDetail . '.real_total_bom',
                $nameTableDetail . '.id',
                $nameTableDetail . '.product_short_name as product_name',
                $nameTableDetail . '.product_code as sku',
                $nameTableDetail . '.product_num',
                $nameTableDetail . '.product_unit as name_unit',
                $nameTableDetail . '.type',
                'so.delivery_date',
                'so.export_date',
                'so.type as type_order',
                'so.customer_short_name as customer_name',
                'so.customer_name as fullname',
                'so.customer_code',
                'so.customer_num'
            )->get();
        # Đối với các mặt hàng khô davicook -> ko theo từng đơn mà cộng gộp lại theo customer và ngày xuất khô.
        $dataTmp = $dataTmp->groupBy(['customer_code', 'export_date', 'product_id']);
        $arrListStampDetails = [];
        $qrList = new Collection([]);
        foreach ($dataTmp as $keyCustomer => $customers) {
            foreach ($customers as $keyExportDate => $exportDates) {
                foreach ($exportDates as $keyProduct => $product) {
                    try {
                        $url = $product->first()->product ? ($product->first()->product->qr_code ?? "") : "";
                        if($url){
                            $qrSearch = $qrList->where("url", $url)->first();
                            if(!$qrSearch){
                                $qrList->push(ShopProduct::generateQr($url));
                            }
                        };
                    } catch (\Throwable $e){

                    }
                    $arrListStampDetails[] = [
                        'id' => $product->first()->id,
                        'order_id' => $product->first()->order_id,
                        'id_barcode' => $product->first()->id_barcode,
                        'qty' => $product->sum('real_total_bom'),
                        'product_id' => $product->first()->product_id,
                        'delivery_time' => $product->first()->export_date,
                        'product_name' => $product->first()->product_name,
                        'customer_name' => $product->first()->customer_name,
                        'order_num' => $product->first()->product_num,
                        'name_unit' => $product->first()->name_unit,
                        'customer_num' => $product->first()->customer_num,
                        'short_name' => 'CTCP DAVICOOK HN',
                        'object_id' => $product->first()->object_id,
                        'product_sku' => $product->first()->sku,
                        'qr_code' => $product->first() ? ($product->first()->product->qr_code ?? "") : "",
                        'customer_fullname' => $product->first()->fullname ?? "",
                    ];
                }
            }
        }

        $arrListStampDetails = collect($arrListStampDetails)->sortBy('order_num')->toArray();

        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }

    public function getNewPriceListProduct($id)
    {
        $dataOrder = AdminOrder::findOrfail($id);
        $customer_id = $dataOrder->customer_id;
        $billDateSt = $dataOrder->bill_date;
        $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $billDateSt);
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'priceBoard.prices', 'customers')
            ->whereHas('customers', function (Builder $query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->whereDate('start_date', '<=', $billDate)
            ->whereDate('due_date', '>=', $billDate)
            ->orderBy('start_date', 'DESC')
            ->first();
        return $shopUserPriceBoard;
    }

    public function getOldPriceListProduct($id)
    {
        $dataOrder = AdminOrder::findOrfail($id);
        $customer_id = $dataOrder->customer_id;
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'priceBoard.prices', 'customers')
            ->whereHas('customers', function (Builder $query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->orderBy('start_date', 'DESC')
            ->first();
        return $shopUserPriceBoard;
    }

    /**
     * Lấy bảng giá sản phẩm (Tạo mới đơn)
     * @param $customer_id, $bill_date
     */
    public function getProductPriceListCreateOrder($customer_id, $bill_date)
    {
        $customerId = $customer_id;
        $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $bill_date);
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'priceBoard.prices', 'customers')
            ->whereHas('customers', function (Builder $query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->whereDate('start_date', '<=', $bill_date)
            ->whereDate('due_date', '>=', $bill_date)
            ->orderBy('start_date', 'DESC')
            ->first();
        return $shopUserPriceBoard;
    }

    /**
     * Lấy giá chi tiết cho từng sản phẩm (Tạo mới đơn)
     * @param $product_id, $customer_id, $bill_date, $object_id
     */
    public function getProductPriceCreateOrder($product_id, $customer_id, $bill_date, $object_id)
    {
        $customerId = $customer_id;
        $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $bill_date);
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'priceBoard.prices', 'customers')
            ->whereHas('customers', function (Builder $query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->whereDate('start_date', '<=', $bill_date)
            ->whereDate('due_date', '>=', $bill_date)
            ->orderBy('start_date', 'DESC')->first();
        $dataPriceProduct = $shopUserPriceBoard->priceBoard->prices ?? [];
            $product_price = 0;
            foreach($dataPriceProduct as $item) {
                if($item->product_id==$product_id) {
                    ($object_id === 1) ?  ($product_price = $item->price_1 ?? 0) : ($product_price = $item->price_2 ?? 0);
                    break;
                }
            }
        return $product_price;
    }
    
    /**
     * Get all order detail
     * @param array $dataSearch
     * @return mixed
     */
    public function getAllDetailOrderProduct(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $keyDepartment = $dataSearch['key_department'] ?? '';
        $keyZone= $dataSearch['key_zone'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();

        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = AdminOrder::join(SC_DB_PREFIX . "shop_order_detail as sod", function ($join) {
                        $join->on(SC_DB_PREFIX . "shop_order.id", "sod.order_id");
                    })
                    ->join(SC_DB_PREFIX . "shop_customer as sc", function ($join) {
                        $join->on(SC_DB_PREFIX . "shop_order.customer_id", "sc.id");
                    })
                    ->join(SC_DB_PREFIX . "shop_product as sp", function ($join) {
                        $join->on("sod.product_id", "sp.id");
                    });

        if ($keyDepartment) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyDepartment) {
                $sql->where('sc.department_id', $keyDepartment);
            });
        }
        if ($keyZone) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyZone) {
                $sql->where('sc.zone_id', $keyZone);
            });
        }
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(function ($sql) use ($from_to) {
                $sql->where('delivery_time', '>=', $from_to);
            });
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(function ($sql) use ($end_to) {
                $sql->where('delivery_time', '<=', $end_to);
            });
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_order.delivery_time", ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_order.delivery_time", "<=", $endToDay);
        }
        if ($category) {
            $dataTmp = $dataTmp->where("sod.category_id", $category);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
                $sql->where("sod.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sod.product_code", "like", "%" . $keyword . "%")
                    ->orWhere("sc.customer_code", "like", "%" . $keyword . "%")
                    ->orWhere("sc.name", "like", "%" . $keyword . "%");
            });
        }

        $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . "shop_order.status" , [1,2])
            ->select(SC_DB_PREFIX . "shop_order.name as customer_name",
                SC_DB_PREFIX . "shop_order.id_name as order_code",
                SC_DB_PREFIX . "shop_order.delivery_time as delivery_date",
                SC_DB_PREFIX . "shop_order.explain",
                SC_DB_PREFIX . "shop_order.id as order_id",
                SC_DB_PREFIX . "shop_order.object_id",
                SC_DB_PREFIX . "shop_order.customer_id",
                SC_DB_PREFIX . "shop_order.customer_code",
                SC_DB_PREFIX . "shop_order.customer_short_name",
                "sod.product_name",
                "sod.product_id",
                "sod.comment" ,
                "sod.id_barcode",
                'sod.qty_reality',
                'sod.id as detail_id',
                'sod.product_code',
                'sp.category_id',
                'sp.kind',
                'sc.department_id',
                'sc.order_num as customer_num',
                'sc.zone_id',
                'sod.product_unit')
            ->orderBy("sod.product_name", 'ASC')->get();
        $dataMerge = new Collection();
        foreach ($dataTmp as $keyTmp => $item) {
            $dataMerge->push(
                [
                    'product_id' => $item->product_id,
                    'product_code' => $item->product_code,
                    'product_unit' => $item->product_unit,
                    'product_name' => $item->product_name ?? 'Sản phẩm bị xóa',
                    'customer_code' => $item->customer_code,
                    'customer_short_name' => $item->customer_short_name,
                    'customer_num' => $item->customer_num,
                    'customer_name' => $item->object_id == 1 ? $item->customer_name . ' - GV' : $item->customer_name,
                    'qty' => $item->qty_reality,
                    'note' => $item->comment,
                    'detail_id' => $item->detail_id,
                    'order_id' => $item->order_id,
                    'order_code' => $item->order_code,
                    'id_barcode' => $item->id_barcode,
                    'object_id' => $item->object_id ?? '',
                    'delivery_date' => $item->delivery_date,
                    'explain' => $item->explain ?? '',
                    'product_kind'=>$item->kind,
                    'department_id'=>$item->department_id,
                    'zone_id'=>$item->zone_id,
                    'category_id'=>$item->category_id
                ]
            );
        }

        return $dataMerge;
    }

    /**
     * @param array $dataSearch
     * @return mixed
     */
    public function getListOrderDavicook(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $note = $dataSearch['note'] ?? '';
        $nameTableOrder = (new ShopDavicookOrder())->table;
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();


        $dataTmp = ShopDavicookOrder::leftjoin(SC_DB_PREFIX . 'shop_davicook_customer as sdc', function ($join) {
            $join->on(SC_DB_PREFIX . "shop_davicook_order.customer_id", "sdc.id");
        })
            ->where(SC_DB_PREFIX . "shop_davicook_order.status", '!=', 7)
//            ->whereIn(SC_DB_PREFIX . "shop_davicook_order.status", [0, 1])
            ->select($nameTableOrder . '.customer_name', $nameTableOrder . '.id_name',
                $nameTableOrder . '.delivery_date', $nameTableOrder . '.explain', $nameTableOrder . '.customer_code',
                DB::raw($nameTableOrder . '.number_of_reality_servings' . '*' . $nameTableOrder . '.price_of_servings as amount'));
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", "<=", $endToDay);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword,$nameTableOrder) {
                $sql->where($nameTableOrder.".customer_name", 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrder.'.customer_code', 'like', '%' . $keyword . '%');
            });
        }
        if ($note) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.explain", $note);
        }

        $dataTmp = $dataTmp->orderBy(SC_DB_PREFIX . "shop_davicook_order.id_name", 'DESC')->get()->toArray();
        return $dataTmp;
    }

    public static function getListProductForCustomerByBillDate($bill_date, $customer_id) {

        if (empty($bill_date) || empty($customer_id)) {
            return ([
                'price_teacher_array' => [],
                'price_student_array' => [],
                'price_product_not_update' => 1
            ]);
        }

        $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $bill_date);
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'customers')
            ->whereHas('customers', function (Builder $query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->whereDate('start_date', '<=', $billDate)
            ->orderBy('due_date', 'DESC')
            ->first();

        if (empty($shopUserPriceBoard)){
            return ([
                'price_teacher_array' => [],
                'price_student_array' => [],
                'price_product_not_update' => 1
            ]);
        }

        $shopProductSupplierTable = (new ShopProductSupplier())->table . ' AS ps';
        $shopProductTable = (new ShopProduct())->table . ' AS p';
        $shopProductUnitTable = (new AdminUnit())->table . ' AS u';
        $shopProductPriceDetailTable = (new AdminProductPriceDetail())->table . ' AS ppd';

        $products = DB::table($shopProductSupplierTable)
            ->select(DB::raw('p.*, p.name AS product_name, u.name AS unit_name, u.type AS unit_type, ppd.price_1 AS price_teacher, ppd.price_2 AS price_student'))
            ->join($shopProductTable, 'p.id', '=', 'ps.product_id')
            ->join($shopProductPriceDetailTable, 'p.id', '=', 'ppd.product_id')
            ->leftJoin($shopProductUnitTable, 'p.unit_id', '=', 'u.id')
            ->where('ps.customer_id', '=', $customer_id)
            ->where('ppd.product_price_id', '=', $shopUserPriceBoard->product_price_id)
            ->get();
        $price_product_not_update = 0;
        $has_price = true;
        $priceBoardDueDate = \Carbon\Carbon::createFromFormat('Y-m-d', $shopUserPriceBoard->due_date);
        if ($billDate > $priceBoardDueDate){
            $price_product_not_update = 1;
            $has_price = false;
        }

        $price_teacher_array = [];
        $price_student_array = [];

        foreach ($products as $product) {
            if (!$product){
                continue;
            }

            $productStatus = $product->status;
            $productDisabled = false;
            $productNote = '';
            if ($productStatus != 1){
                $productNote = 'Sản phẩm tạm hết hàng!';
                $productDisabled = true;
            }

            $productDescriptionName = $product->product_name;
            if (isset($request->keyword) && $productDescriptionName) {
                $keyword = trim(strtolower($request->keyword));
                if (!str_contains(strtolower($productDescriptionName), $keyword)) {
                    continue;
                }
            }
            $product_unit_name = $product->unit_name ?? "";
            $product_unit_type = $product->unit_type ?? "0";

            $price_teacher = $product->price_teacher;
            if ($price_teacher && $price_teacher > 0){
                // Đối với bảng giá có giá sản phẩm = 0. Sản phẩm không hiển thị trên Web/app đặt hàng
                $data_teacher = [
                    'product_id' => $product->id,
                    'product_code' => $product->sku,
                    'product_name' => $productDescriptionName ?? "",
                    'product_price' => $has_price ? ($price_teacher ?? 0) : 0,
                    'unit' => $product_unit_name ?? "",
                    'unit_type' => $product_unit_type ?? "0",
                    'status' => $productStatus,
                    'product_note' => $productNote,
                    'disabled' => $productDisabled,
                    'minimum_qty' => $product->minimum_qty_norm
                ];
                $price_teacher_array[] = $data_teacher;
            }

            $price_student = $product->price_student;
            if ($price_student && $price_student > 0) {
                $data_student = [
                    'product_id' => $product->id,
                    'product_code' => $product->sku,
                    'product_name' => $productDescriptionName ?? "",
                    'product_price' => $has_price ? ($price_student ?? 0) : 0,
                    'unit' => $product_unit_name ?? "",
                    'unit_type' => $product_unit_type ?? "0",
                    'status' => $productStatus,
                    'product_note' => $productNote,
                    'disabled' => $productDisabled,
                    'minimum_qty' => $product->minimum_qty_norm
                ];
                $price_student_array[] = $data_student;
            }
        }

        $data['price_teacher_array'] = collect($price_teacher_array)->sortByDesc('product_name')->reverse()->values()->toArray();
        $data['price_student_array'] = collect($price_student_array)->sortByDesc('product_name')->reverse()->values()->toArray();
        $data['price_product_not_update'] = $price_product_not_update;
        return $data;
    }

    /**
     * Lấy danh sách sản phẩm có trong bảng giá gán theo khách hàng
     * @param $bill_date, $customer_id, $object_id
     */
    public static function getProductByCustomerPriceBoard($bill_date, $customer_id, $object_id)
    {
        if ($bill_date != null){
            $billDate = \Carbon\Carbon::createFromFormat('Y-m-d', $bill_date);
        }
        $shopUserPriceBoard = ShopUserPriceboard::with('priceBoard', 'customers')
        ->whereHas('customers', function (Builder $query) use ($customer_id) {
            $query->where('customer_id', $customer_id);
        });
        if (isset($billDate)) {
            $shopUserPriceBoard = $shopUserPriceBoard->whereDate('start_date', '<=', $billDate);
        }
        $shopUserPriceBoard = $shopUserPriceBoard->orderBy('due_date', 'DESC')
            ->first();

        if (empty($shopUserPriceBoard)) {
            return [];
        }

        $shopProductSupplierTable = (new ShopProductSupplier())->table . ' AS ps';
        $shopProductTable = (new ShopProduct())->table . ' AS p';
        $shopProductUnitTable = (new AdminUnit())->table . ' AS u';
        $shopProductPriceDetailTable = (new AdminProductPriceDetail())->table . ' AS ppd';

        $products = DB::table($shopProductSupplierTable)
            ->select(DB::raw('p.*, p.name AS product_name, u.name AS unit_name, u.type AS unit_type, ppd.price_1 AS price_teacher, ppd.price_2 AS price_student'))
            ->join($shopProductTable, 'p.id', '=', 'ps.product_id')
            ->join($shopProductPriceDetailTable, 'p.id', '=', 'ppd.product_id')
            ->leftJoin($shopProductUnitTable, 'p.unit_id', '=', 'u.id')
            ->where('ps.customer_id', '=', $customer_id)
            ->where('ppd.product_price_id', '=', $shopUserPriceBoard->product_price_id ?? '')
            ->get();
        $has_price = true;
        $priceBoardDueDate = \Carbon\Carbon::createFromFormat('Y-m-d', $shopUserPriceBoard->due_date);
        if (isset($billDate)) {
            if ($billDate > $priceBoardDueDate) {
                $has_price = false;
            }
        }

        $price_teacher_array = [];
        $price_student_array = [];
        foreach ($products as $product) {
            if (!$product) {
                continue;
            }
            $productStatus = $product->status;
            $productDisabled = false;
            $productNote = '';
            if ($productStatus != 1) {
                $productNote = 'Sản phẩm tạm hết hàng!';
                $productDisabled = true;
            }

            $productDescriptionName = $product->product_name;
            $product_unit_name = $product->unit_name ?? "";
            $product_unit_type = $product->unit_type ?? "0";
            $price_teacher = $product->price_teacher;
            if ($price_teacher && $price_teacher > 0) {
                $data_teacher = [
                    'product_code' => $product->sku,
                    'product_id' => $product->id,
                    'product_name' => $productDescriptionName ?? "",
                    'product_price' => $has_price ? ($price_teacher ?? 0) : 0,
                    'unit' => $product_unit_name ?? "",
                    'unit_type' => $product_unit_type ?? "0",
                    'status' => $productStatus,
                    'product_note' => $productNote,
                    'disabled' => $productDisabled
                ];
                $price_teacher_array[] = $data_teacher;
            }

            $price_student = $product->price_student;
            if ($price_student && $price_student > 0) {
                $data_student = [
                    'product_code' => $product->sku,
                    'product_id' => $product->id,
                    'product_name' => $productDescriptionName ?? "",
                    'product_price' => $has_price ? ($price_student ?? 0) : 0,
                    'unit' => $product_unit_name ?? "",
                    'unit_type' => $product_unit_type ?? "0",
                    'status' => $productStatus,
                    'product_note' => $productNote,
                    'disabled' => $productDisabled
                ];
                $price_student_array[] = $data_student;
            }
        }

        if ($object_id == 1) {
            $data = collect($price_teacher_array)->sortByDesc('product_name')->reverse()->values()->toArray();
        } else {
            $data = collect($price_student_array)->sortByDesc('product_name')->reverse()->values()->toArray();
        }
        return $data;
    }

//    lấy giá nhập sản phẩm theo khách hàng
    public function getListPriceImportProductDavicorp(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $search_supplier = $dataSearch['search_supplier'] ?? '';
        $department = $dataSearch['department'] ?? '';

        $objectOrderDetail = new ShopOrderDetail();
        $tableOrderDetail = SC_DB_PREFIX . 'shop_order_detail';
        $tableOrder = SC_DB_PREFIX . 'shop_order';
        $tableImportPriceBoard = SC_DB_PREFIX . 'shop_import_priceboard';
        $tableImportPriceBoardDetail = SC_DB_PREFIX . 'shop_import_priceboard_detail';
        $tableCategory = SC_DB_PREFIX . 'shop_category';

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
        }

        $startToDay = $from_to ? $from_to : (Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString());
        $endToDay = $end_to ? $end_to : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataPriceImport = $objectOrderDetail->leftjoin($tableOrder . ' as so', function ($join) use ($tableOrderDetail) {
            $join->on($tableOrderDetail . '.order_id', 'so.id');
        })
            ->leftjoin($tableImportPriceBoard . ' as ip', function ($join) use ($tableOrderDetail) {
                $join->on($tableOrderDetail . '.supplier_id', 'ip.supplier_id');
                $join->whereBetweenColumns("so.delivery_time", ["ip.start_date", "ip.end_date"]);
            })
            ->leftjoin($tableImportPriceBoardDetail . ' as ipd', function ($join) use ($tableOrderDetail) {
                $join->on($tableOrderDetail . '.product_id', 'ipd.product_id');
                $join->on('ip.id', 'ipd.priceboard_id');
            })
            ->leftjoin($tableCategory . ' as cat', function ($join) use ($tableOrderDetail) {
                $join->on('cat.id', $tableOrderDetail . '.category_id');
            })
            ->where("so.delivery_time", ">=", $startToDay)
            ->where("so.delivery_time", "<=", $endToDay)
            ->whereIn("so.status", [1, 2]);
//            ->whereBetweenColumns("so.delivery_time", ["ip.start_date", "ip.end_date"]);
        if ($keyword) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($keyword, $tableOrderDetail) {
                $sql->where('so.name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($tableOrderDetail.'.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableOrderDetail.'.product_code', 'like', '%' . $keyword . '%')
                ;
            });
        }
        if ($search_supplier) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($search_supplier, $tableOrderDetail) {
                $sql->where($tableOrderDetail . '.supplier_name', 'like', '%' . $search_supplier . '%')
                    ->orWhere($tableOrderDetail . '.supplier_code', 'like', '%' . $search_supplier . '%');
            });
        }
        if ($department) {
            $dataPriceImport = $dataPriceImport->where("so.department_id", $department);
        }
        $dataPriceImport = $dataPriceImport
            ->select(
                $tableOrderDetail . '.comment',
                $tableOrderDetail . '.qty_reality as qtyProduct',
                $tableOrderDetail . '.supplier_name',
                $tableOrderDetail . '.supplier_code',
                $tableOrderDetail . '.supplier_id',
                $tableOrderDetail . '.product_unit',
                $tableOrderDetail . '.product_id',
                $tableOrderDetail . '.product_code',
                $tableOrderDetail . '.product_name',
                $tableOrderDetail . '.id as detail_id',
                'cat.sort',
                'so.name as customer_name',
                'so.customer_code',
                'so.customer_id',
                'so.object_id',
                'ipd.price')->get()->toArray();

        return collect($dataPriceImport);
    }

    /**
     * Lấy giá nhập sản phẩm hàng TƯƠI davicook đổ về báo cáo Hàng nhập.
     * @param array $dataSearch
     * @param $select_warehouse
     * @return Collection
     */
    public function getListProductFreshToReportImportPrice(array $dataSearch, $select_warehouse)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $search_supplier = $dataSearch['search_supplier'] ?? '';
        $department = $dataSearch['department'] ?? '';
        $filter_date = ($select_warehouse == 2) ? "sdo.export_date" : "sdo.delivery_date";
        $date = ($select_warehouse == 2) ? "sdo.export_date" : "sdo.delivery_date";

        $objectDavicookOrderDetail = new ShopDavicookOrderDetail();
        $tableDavicookOrderDetail = SC_DB_PREFIX . 'shop_davicook_order_detail';
        $tableDavicookOrder = SC_DB_PREFIX . 'shop_davicook_order';
        $tableImportPriceBoard = SC_DB_PREFIX . 'shop_import_priceboard';
        $tableImportPriceBoardDetail = SC_DB_PREFIX . 'shop_import_priceboard_detail';
        $tableCategory = SC_DB_PREFIX . 'shop_category';

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
        }

        $startToDay = $from_to ? $from_to : (Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString());
        $endToDay = $end_to ? $end_to : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();


        $dataPriceImport = $objectDavicookOrderDetail->leftjoin($tableDavicookOrder . ' as sdo', function ($join) use ($tableDavicookOrderDetail) {
            $join->on($tableDavicookOrderDetail . '.order_id', 'sdo.id');
        })
            ->leftjoin($tableImportPriceBoard . ' as ip', function ($join) use ($tableDavicookOrderDetail) {
                $join->on($tableDavicookOrderDetail . '.supplier_id', 'ip.supplier_id');
                $join->whereBetweenColumns('sdo.bill_date', ["ip.start_date", "ip.end_date"]);
            })
            ->leftjoin($tableImportPriceBoardDetail . ' as ipd', function ($join) use ($tableDavicookOrderDetail) {
                $join->on($tableDavicookOrderDetail . '.product_id', 'ipd.product_id');
                $join->on('ip.id', 'ipd.priceboard_id');
            })
            ->leftjoin($tableCategory . ' as cat', function ($join) use($tableDavicookOrderDetail) {
                $join->on('cat.id', $tableDavicookOrderDetail . '.category_id');
            })
            ->where($filter_date, ">=", $startToDay)
            ->where($filter_date, "<=", $endToDay)
            ->where($tableDavicookOrderDetail.'.total_bom', ">", 0);
//            ->whereBetweenColumns($filter_date, ["ip.start_date", "ip.end_date"]);
        if ($keyword) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($keyword, $tableDavicookOrderDetail) {
                $sql->where('sdo.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('sdo.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDavicookOrderDetail.'.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDavicookOrderDetail.'.product_code', 'like', '%' . $keyword . '%');
            });
        }

        if ($search_supplier) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($search_supplier, $tableDavicookOrderDetail) {
                $sql->where($tableDavicookOrderDetail . '.supplier_name', 'like', '%' . $search_supplier . '%')
                    ->orWhere($tableDavicookOrderDetail . '.supplier_code', 'like', '%' . $search_supplier . '%');
            });
        }

        if ($department) {
            $dataPriceImport = $dataPriceImport->where('sdo.status', 99);
        } else {
            if (!empty($select_warehouse)) {
                if ($select_warehouse == 2) {
                    $dataPriceImport = $dataPriceImport->where($tableDavicookOrderDetail . '.product_type', 0)->where('sdo.status', 2);
                } else {
                    $dataPriceImport = $dataPriceImport->where($tableDavicookOrderDetail . '.product_type', 1)->whereIn('sdo.status', [0, 1, 2]);
                }
            }
        }

        $dataPriceImport = $dataPriceImport
            ->select($tableDavicookOrderDetail . '.id',
                $tableDavicookOrderDetail . '.comment',
                $tableDavicookOrderDetail . '.real_total_bom as qtyProduct',
                $tableDavicookOrderDetail . '.order_id',
                $tableDavicookOrderDetail . '.supplier_name',
                $tableDavicookOrderDetail . '.supplier_code',
                $tableDavicookOrderDetail . '.product_id',
                $tableDavicookOrderDetail . '.product_unit',
                $tableDavicookOrderDetail . '.product_code',
                $tableDavicookOrderDetail . '.product_name',
                $tableDavicookOrderDetail . '.type',
                'cat.sort',
                'sdo.customer_name',
                'sdo.customer_code',
                'sdo.customer_id',
                'sdo.type as type_order',
                'sdo.customer_id',
                'ipd.price')
            ->get();
        $dataMerge = new Collection();
        foreach ($dataPriceImport->groupBy('order_id') as $keyOrder => $itemOrder) {
            foreach ($itemOrder->where('type_order', 0)->where('type', 0)->groupBy('product_id') as $keyProduct => $subItem) {
                $note = '';
                foreach ($subItem as $item){
                    if ($item->comment != '') {
                        $note .= $item->comment . '; ' ?? '';
                    }
                }
                $dataMerge->push(
                    [
                        'comment' => $note,
                        'qtyProduct' => $subItem->sum('qtyProduct'),
                        'supplier_name' => $subItem->first()->supplier_name ,
                        'supplier_code' => $subItem->first()->supplier_code ,
                        'product_unit' => $subItem->first()->product_unit ,
                        'product_id' => $subItem->first()->product_id ,
                        'product_code' => $subItem->first()->product_code ,
                        'product_name' => $subItem->first()->product_name ,
                        'sort' => $subItem->first()->sort,
                        'customer_name' => $subItem->first()->customer_name,
                        'customer_code' => $subItem->first()->customer_code,
                        'customer_id' => $subItem->first()->customer_id,
                        'price' => $subItem->first()->price,
                        'detail_id' => $subItem->first()->id,
                    ]
                );
            }
            foreach ($itemOrder->where('type_order', 0)->where('type', 1)->groupBy('product_id') as $keyProductExtra => $subItemExtra) {
                $noteExtra = '';
                foreach ($subItemExtra as $itemExtra){
                    if ($itemExtra->comment != '') {
                        $noteExtra .= $itemExtra->comment   ?? '';
                    }
                }
                $dataMerge->push(
                    [
                        'comment' => $noteExtra,
                        'qtyProduct' => $subItemExtra->sum('qtyProduct'),
                        'supplier_name' => $subItemExtra->first()->supplier_name ,
                        'supplier_code' => $subItemExtra->first()->supplier_code ,
                        'product_unit' => $subItemExtra->first()->product_unit ,
                        'product_id' => $subItemExtra->first()->product_id ,
                        'product_code' => $subItemExtra->first()->product_code ,
                        'product_name' => $subItemExtra->first()->product_name ,
                        'sort' => $subItemExtra->first()->sort,
                        'customer_name' => $subItemExtra->first()->customer_name,
                        'customer_code' => $subItemExtra->first()->customer_code,
                        'customer_id' => $subItemExtra->first()->customer_id,
                        'price' => $subItemExtra->first()->price,
                        'detail_id' => $subItemExtra->first()->id,
                    ]
                );
            }
            foreach ($itemOrder->where('type_order',1) as $keyNecc => $Necc) {
                $dataMerge->push(
                    [
                        'comment' => $Necc->comment,
                        'qtyProduct' => $Necc->qtyProduct,
                        'supplier_name' => $Necc->supplier_name ,
                        'supplier_code' => $Necc->supplier_code ,
                        'product_unit' => $Necc->product_unit ,
                        'product_id' => $Necc->product_id ,
                        'product_code' => $Necc->product_code ,
                        'product_name' => $Necc->product_name ,
                        'customer_code' => $Necc->customer_code,
                        'customer_id' => $Necc->customer_id,
                        'sort' => $Necc->sort,
                        'customer_name' => $Necc->customer_name,
                        'price' => $Necc->price,
                        'detail_id' => $Necc->id,
                    ]
                );
            }
        }

        return $dataMerge;
    }

    /**
     * Lấy giá nhập sản phẩm hàng KHÔ davicook đổ về báo cáo Hàng nhập.
     * @param array $dataSearch
     * @param $select_warehouse
     * @return Collection
     */
    public function getListProductDryToReportImportPrice(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $search_supplier = $dataSearch['search_supplier'] ?? '';
        $department = $dataSearch['department'] ?? '';
        $objectDavicookOrderDetail = new ShopDavicookOrderDetail();
        $tableDavicookOrderDetail = SC_DB_PREFIX . 'shop_davicook_order_detail';
        $tableDavicookOrder = SC_DB_PREFIX . 'shop_davicook_order';
        $tableImportPriceBoard = SC_DB_PREFIX . 'shop_import_priceboard';
        $tableImportPriceBoardDetail = SC_DB_PREFIX . 'shop_import_priceboard_detail';
        $tableCategory = SC_DB_PREFIX . 'shop_category';

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
        }

        $startToDay = $from_to ? $from_to : (Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString());
        $endToDay = $end_to ? $end_to : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataPriceImport = $objectDavicookOrderDetail->leftjoin($tableDavicookOrder . ' as sdo', function ($join) use ($tableDavicookOrderDetail) {
            $join->on($tableDavicookOrderDetail . '.order_id', 'sdo.id');
        })
            ->leftjoin($tableImportPriceBoard . ' as ip', function ($join) use ($tableDavicookOrderDetail) {
                $join->on($tableDavicookOrderDetail . '.supplier_id', 'ip.supplier_id');
                $join->whereBetweenColumns('sdo.bill_date', ["ip.start_date", "ip.end_date"]);
            })
            ->leftjoin($tableImportPriceBoardDetail . ' as ipd', function ($join) use ($tableDavicookOrderDetail) {
                $join->on($tableDavicookOrderDetail . '.product_id', 'ipd.product_id');
                $join->on('ip.id', 'ipd.priceboard_id');
            })
            ->leftjoin($tableCategory . ' as cat', function ($join) use($tableDavicookOrderDetail) {
                $join->on('cat.id', $tableDavicookOrderDetail . '.category_id');
            })
            ->where('sdo.export_date', ">=", $startToDay)
            ->where('sdo.export_date', "<=", $endToDay)
            ->where($tableDavicookOrderDetail.'.total_bom', ">", 0);
        if ($keyword) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($keyword, $tableDavicookOrderDetail) {
                $sql->where('sdo.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('sdo.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDavicookOrderDetail.'.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere($tableDavicookOrderDetail.'.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if ($search_supplier) {
            $dataPriceImport = $dataPriceImport->where(function ($sql) use ($search_supplier, $tableDavicookOrderDetail) {
                $sql->where($tableDavicookOrderDetail . '.supplier_name', 'like', '%' . $search_supplier . '%')
                    ->orWhere($tableDavicookOrderDetail . '.supplier_code', 'like', '%' . $search_supplier . '%');
            });
        }

        if ($department) {
            $dataPriceImport = $dataPriceImport->where('sdo.status', 99);
        } else {
            $dataPriceImport = $dataPriceImport->where($tableDavicookOrderDetail . '.product_type', 0)->where('sdo.status', 2);
        }

        $dataPriceImport = $dataPriceImport
            ->select($tableDavicookOrderDetail . '.id',
                $tableDavicookOrderDetail . '.comment',
                $tableDavicookOrderDetail . '.real_total_bom as qtyProduct',
                $tableDavicookOrderDetail . '.order_id',
                $tableDavicookOrderDetail . '.supplier_name',
                $tableDavicookOrderDetail . '.supplier_code',
                $tableDavicookOrderDetail . '.product_id',
                $tableDavicookOrderDetail . '.product_unit',
                $tableDavicookOrderDetail . '.product_code',
                $tableDavicookOrderDetail . '.product_name',
                $tableDavicookOrderDetail . '.type',
                'cat.sort',
                'sdo.customer_name',
                'sdo.customer_code',
                'sdo.export_date',
                'sdo.type as type_order',
                'sdo.customer_id',
                'ipd.price')
            ->get();
        $dataPriceImport = $dataPriceImport->groupBy(['customer_code', 'export_date', 'product_id']);
        $dataMerge = new Collection();
        foreach ($dataPriceImport as $customers) {
            foreach ($customers as $exportDates) {
                foreach ($exportDates as $keyProduct => $products) {
                    $note = '';
                    foreach ($products as $item){
                        if ($item->comment != '') {
                            $note .= $item->comment . '; ' ?? '';
                        }
                    }
                    $dataMerge->push(
                        [
                            'comment' => $note,
                            'qtyProduct' => $products->sum('qtyProduct'),
                            'supplier_name' => $products->first()->supplier_name ,
                            'supplier_code' => $products->first()->supplier_code ,
                            'product_unit' => $products->first()->product_unit ,
                            'product_id' => $keyProduct,
                            'product_code' => $products->first()->product_code ,
                            'product_name' => $products->first()->product_name ,
                            'sort' => $products->first()->sort,
                            'customer_name' => $products->first()->customer_name,
                            'customer_code' => $products->first()->customer_code,
                            'customer_id' => $products->first()->customer_id,
                            'price' => $products->first()->price,
                            'detail_id' => $products->first()->id,
                        ]
                    );
                }
            }
        }

        return $dataMerge;
    }

    /**
     * Lấy dữ liệu trả hàng Davicorp.
     * Lấy đơn hàng trả.
     * Lấy đơn hàng davicook diễn giải là hàng đợt 2.
     * @param  array  $dataSearch
     * @param $productId
     * @return mixed
     */
    public function getOrderReturnHistoryDavicorp(array $dataSearch)
    {
        $dataMerge = new Collection();
        $from_to = $dataSearch['from_to'] ? convertVnDateObject($dataSearch['from_to'])->startOfDay()->toDateTimeString()
            : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $end_to = $dataSearch['end_to'] ? convertVnDateObject($dataSearch['end_to'])->endOfDay()->toDateTimeString()
            : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();
        $explain = $dataSearch['explain'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';

        $objOrder = new AdminOrder();
        $nameTable = $objOrder->table;

        # Lấy dơn hàng đợt 2 davicook.
        $dataOrder = $objOrder->join(SC_DB_PREFIX . "shop_order_detail as sod", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sod.order_id");
        });

        if($from_to){
            $dataOrder = $dataOrder->whereDate($nameTable . ".delivery_time", ">=", $from_to);
        }

        if($end_to){
            $dataOrder = $dataOrder->whereDate($nameTable . "..delivery_time", "<=", $end_to);
        }

        if ($explain) {
            $dataOrder = $dataOrder->where($nameTable.".explain", $explain);
        } else {
            $dataOrder = $dataOrder->where($nameTable.".explain", 'Hàng đợt 2');
        }
        $dataOrder = $dataOrder->whereIn($nameTable.".status", [1,2]);
        if ($keyword) {
            $dataOrder = $dataOrder->where(function ($sql) use ($keyword, $nameTable)  {
                $sql->where("sod.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sod.product_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".name", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".id_name", "like", "%" . $keyword . "%")
                ;
            });
        }

        $dataOrder = $dataOrder->select($nameTable . ".name as customer_name",
            $nameTable . '.id_name',
            $nameTable . '.id',
            $nameTable . '.customer_code',
            $nameTable . '.delivery_time as delivery_date',
            $nameTable . '.explain',
            'sod.product_name',
            'sod.product_code',
            'sod.product_unit',
            'sod.qty_reality as qty',
            'sod.price')->orderBy("sod.product_name", 'ASC')->get();

        # Lấy đơn hàng trả davicook.
        $dataReturn = $objOrder->join(SC_DB_PREFIX . "shop_order_return_history as sorh", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sorh.order_id");
        });

        if($from_to){
            $dataReturn = $dataReturn->whereDate($nameTable . ".delivery_time", ">=", $from_to);
        }

        if($end_to){
            $dataReturn = $dataReturn->whereDate($nameTable . "..delivery_time", "<=", $end_to);
        }

        if ($explain) {
            $dataReturn = $dataReturn->where($nameTable.".explain", $explain);
        }
        $dataReturn = $dataReturn->whereIn($nameTable.".status", [1,2]);
        if ($keyword) {
            $dataReturn = $dataReturn->where(function ($sql) use ($keyword, $nameTable) {
                $sql->where("sorh.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sorh.product_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".name", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".id_name", "like", "%" . $keyword . "%")
                ;
            });
        }

        $dataReturn = $dataReturn->select($nameTable . ".name as customer_name",
            $nameTable . '.id_name',
            $nameTable . '.id',
            $nameTable . '.customer_code',
            $nameTable . '.delivery_time as delivery_date',
            $nameTable . '.explain',
            'sorh.product_name',
            'sorh.product_code',
            'sorh.created_at',
            'sorh.product_unit',
            'sorh.return_qty as qty',
            'sorh.price')->orderBy("sorh.created_at", 'desc')->get();

        $dataMerge = $dataReturn->mergeRecursive($dataOrder);

        return $dataMerge;
    }
}

