<?php

namespace App\Admin\Models;

use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopDavicookOrderDetail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use SCart\Core\Front\Models\ShopOrder;
use Illuminate\Database\Eloquent\Builder;

class AdminDavicookOrder extends ShopDavicookOrder
{
    public static $mapStyleStatus = [
        '0' => 'warning', //Đơn nháp
        '1' => 'success', //Đang khả dụng
        '2' => 'info', //Đã xuất khô
        '7' => 'danger', //Đã hủy
    ];
    
    /**
     * Get order detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public static function getOrderDavicookAdmin($id)
    {
        $data = self::with(['details', 'returnHistory'])
            ->where('id', $id);
        return $data->first();
    }

    /**
     * Get list order in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getOrderDavicookListAdmin(array $dataSearch, $all = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $limit = $dataSearch['limit'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $start_day = $dataSearch['start_day'] ?? '';
        $end_day = $dataSearch['end_day'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $order_status = $dataSearch['order_status'];
        $order_purchase_priority_level = $dataSearch['order_purchase_priority_level'];
        $code = $dataSearch['code'];
        $type = $dataSearch['order_type'];
        $explain = $dataSearch['order_explain'] ?? '';
        $option_date = $dataSearch['option_date'] == 2 ? 'export_date' : 'delivery_date';
        $orderList = new ShopDavicookOrder;
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        if($order_status != ''){
            $orderList = $orderList->where('status', (int) $order_status);
        }

        if($order_purchase_priority_level != ''){
            $orderList = $orderList->where('purchase_priority_level', (int) $order_purchase_priority_level);
        }

        if ($keyword) {
            $orderList = $orderList->where(function ($sql) use ($keyword) {
                $sql->where('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_code', 'like', '%'.$keyword.'%');
            });
        }

        if ($explain) {
            $orderList = $orderList->where('explain', $explain);
        }

        if ($code) {
            $orderList = $orderList->where('id_name', 'like', "%$code%");
        }

        if ($type != '') {
            $orderList = $orderList->where('type', $type);
        }

        if ($from_to) {
            $from_to = Carbon::createFromFormat('d/m/Y', $from_to)->startOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($from_to, $option_date) {
                $sql->where($option_date, '>=', $from_to);
            });
        }

        if ($end_to) {
            $end_to = Carbon::createFromFormat('d/m/Y', $end_to)->endOfDay()->toDateTimeString();
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

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $orderList = $orderList->orderBy($field, $sort_field);
        } else {
            $orderList = $orderList->orderBy('id_name', 'desc');
        }

        if ($limit) {
            return $orderList->paginate($limit);
        }

        return $orderList->paginate(config('pagination.admin.order'));
    }

    /**
     * Get list order in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getReturnOrderDavicook(array $dataSearch, $all = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_status = $dataSearch['order_status'];
        $order_purchase_priority_level = $dataSearch['order_purchase_priority_level'];
        $code = $dataSearch['code'];
        $type = $dataSearch['order_type'];
        $orderList = (new ShopDavicookOrder)->with('returnHistory');

        if($order_status != ''){
            $orderList = $orderList->where('status', (int) $order_status);
        }

        if($order_purchase_priority_level != ''){
            $orderList = $orderList->where('purchase_priority_level', (int) $order_purchase_priority_level);
        }

        if ($keyword) {
            $orderList = $orderList->where(function ($sql) use ($keyword) {
                $sql->where('customer_name', 'like', '%'.$keyword.'%')
                    ->orWhere('customer_code', 'like', '%'.$keyword.'%');
            });
        }

        if ($code) {
            $orderList = $orderList->where('id_name', 'like', "%$code%");
        }

        if ($type != '') {
            $orderList = $orderList->where('type', $type);
        }

        if ($from_to) {
            $from_to = Carbon::createFromFormat('d/m/Y', $from_to)->startOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($from_to) {
                $sql->where('delivery_date', '>=', $from_to);
            });
        }

        if ($end_to) {
            $end_to = Carbon::createFromFormat('d/m/Y', $end_to)->endOfDay()->toDateTimeString();
            $orderList = $orderList->where(function ($sql) use ($end_to) {
                $sql->where('delivery_date', '<=', $end_to);
            });
        }

        return $orderList->orderBy('delivery_date', 'asc')->orderBy('customer_name', 'asc')->get();
    }

    /**
     * Update new sub total
     * @param  [int] $orderId [description]
     * @return [type]           [description]
     */
    public static function updateSubTotal($orderId)
    {
        try {
            $order = self::getOrderDavicookAdmin($orderId);
            $details = $order->details;
            $tax = $subTotal = 0;
            if ($details->count()) {
                foreach ($details as $detail) {
                    $tax += $detail->tax;
                    $subTotal += $detail->total_price;
                }
            }
            $order->subtotal = $subTotal;
            $order->tax = $tax;
            $total = $subTotal + $tax + $order->discount + $order->shipping;
            $balance = $total + $order->received;
            $payment_status = 0;
            $order->payment_status = $payment_status;
            $order->total = $total;
            $order->balance = $balance;
            $order->save();
            return 1;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    public function getNewPriceListProduct($id)
    {
        $dataOrder = AdminDavicookOrder::findOrfail($id);
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

     /** Get all davicook order detail.
     * @param  array  $dataSearch
     * @param $productId
     * @return mixed
     */
    public function getDetailOrder(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
        })
            ->leftjoin(SC_DB_PREFIX . "shop_davicook_customer as sc", function($join){
                $join->on("sc.id", SC_DB_PREFIX . "shop_davicook_order.customer_id");
            })
            ->leftjoin(SC_DB_PREFIX . "shop_product as sp", function ($join){
                $join->on("sod.product_id", "sp.id");
            })
            ->leftjoin(SC_DB_PREFIX . "shop_product_description as spd", function($join){
                $join->on("spd.product_id",  "sp.id")->where("spd.lang", sc_get_locale());
            });

        if($from_to){
            $from_to = Carbon::createFromFormat('Y-m-d', $from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", ">=", $from_to);
        }
        if($end_to){
            $end_to = Carbon::createFromFormat('Y-m-d', $end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", "<=", $end_to);
        }
        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order.delivery_date", "<=", $endToDay);
        }
        if ($category) {
            $dataTmp = $dataTmp->where("sp.category_id", $category);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where("spd.name", "like", "%" . $keyword . "%");
        }
        $dataTmp = $dataTmp->where("sp.kind", 1);

        $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . "shop_davicook_order.status" , [0,1,2]);

        $dataTmp = $dataTmp->select(SC_DB_PREFIX . "shop_davicook_order.customer_name", SC_DB_PREFIX . "shop_davicook_order.id as order_id", SC_DB_PREFIX . "shop_davicook_order.customer_id", "spd.name as product_name", "sc.customer_code", "sod.product_id", "sod.comment" , 'sod.total_bom as qty')
            ->orderBy("spd.name", 'ASC');
        return $dataTmp;
    }

    /**
     * Lấy chi tiết là hàng Tươi davicorp đổ về báo cáo bán hàng 2 chỉ tiêu
     * @param  array  $dataSearch
     * @param $productId
     * @return mixed
     */
    public function getFreshProductToReportTwoTarget(array $dataSearch, $typeProduct = null, $status, $date)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $keyZone= $dataSearch['key_zone'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
        })
        ->join(SC_DB_PREFIX . "shop_davicook_customer as sc", function ($join) {
            $join->on(SC_DB_PREFIX . "shop_davicook_order.customer_id", "sc.id");
        });
        if ($keyZone) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyZone) {
                $sql->where('sc.zone_id', $keyZone);
            });
        }

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $endToDay);
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

        $dataTmp = $dataTmp->whereIn("sod.product_type", $typeProduct)
            ->whereIn($nameTable . ".status", $status)
            ->where("sod.total_bom", '>', 0)
            ->select($nameTable . ".customer_name",$nameTable . ".type as type_order", $nameTable . ".id as order_id", "sod.product_name", $nameTable . ".customer_code",
                "sod.product_id", "sod.comment" , 'sod.real_total_bom as qty', 'sod.type', 'sod.id as detail_id', 'sod.product_code', 'sod.product_unit')
            ->orderBy("sod.product_name", 'ASC')->get();
        $dataMerge = new Collection();
//        dd($dataTmp->where('type_order',0)->where('type', 0)->toArray());
        foreach ($dataTmp->groupBy('order_id') as $keyTmp => $item) {
            foreach ($item->where('type_order',0)->where('type', 0)->groupBy('product_id') as $keyProduct => $subItem) {
                $commentCombineSub = '';
                $j = 0;
                $commentItem = '';
                $commentCombine = ($subItem->first()->product_name ?? '' ) .' : '. number_format($subItem->sum('qty'), 2);
                foreach ($subItem as $KeyComment => $value) {
                    if ($value->comment != '') {
                        $j++;
                        $commentItem = $value->comment;
                        $commentCombineSub .= number_format($value->qty, 2). ' : ' . $value->comment . ', ';
                    }
                }
                $dataMerge->push(
                    [
                        'product_id' => $keyProduct,
                        'product_code' => $subItem->first()->product_code,
                        'product_unit' => $subItem->first()->product_unit,
                        'product_name' => $subItem->first()->product_name ?? 'Sản phẩm bị xóa',
                        'customer_code' => $subItem->first()->customer_code ?? sc_language_render('customer.delete'),
                        'customer_name' => $subItem->first()->customer_name ,
                        'qty' => $subItem->sum('qty'),
                        'note' => ($j == 1) ? $commentItem : ( ($commentCombineSub != '') ?
                            ( $commentCombine . ' ( ' . rtrim($commentCombineSub, ", ") . ' )' ) : '' ),
                        'detail_id' => $subItem->first()->detail_id ?? '',
                    ]
                );
            }
            foreach ($item->where('type_order',0)->where('type', 1)->groupBy('product_id') as $keyProductExtra => $subItemExtra) {
                $commentCombineSubExtra = '';
                $k = 0;
                $commentItemExtra = '';
                $commentCombineExtra = ($subItemExtra->first()->product_name ?? '' ) .' : '. number_format($subItemExtra->sum('qty'), 2);
                foreach ($subItemExtra as $KeyComment => $valueExtra) {
                    if ($valueExtra->comment != '') {
                        $k++;
                        $commentItemExtra = $valueExtra->comment;
                        $commentCombineSubExtra .= number_format($valueExtra->qty, 2). ' : ' . $valueExtra->comment . ', ';
                    }
                }
                $dataMerge->push(
                    [
                        'product_id' => $keyProductExtra,
                        'product_code' => $subItemExtra->first()->product_code,
                        'product_unit' => $subItemExtra->first()->product_unit,
                        'product_name' => $subItemExtra->first()->product_name ?? 'Sản phẩm bị xóa',
                        'customer_code' => $subItemExtra->first()->customer_code ?? sc_language_render('customer.delete'),
                        'customer_name' => $subItemExtra->first()->customer_name ,
                        'qty' => $subItemExtra->sum('qty'),
                        'note' => ($k == 1) ? $commentItemExtra : ( ($commentCombineSubExtra != '') ?
                            ( $commentCombineExtra . ' ( ' . rtrim($commentCombineSubExtra, ", ") . ' )' ) : '' ),
                        'detail_id' => $subItemExtra->first()->detail_id,
                    ]
                );
            }
            foreach ($item->where('type_order',1) as $keyNecc => $Necc) {
                $dataMerge->push(
                    [
                        'product_id' => $Necc->product_id,
                        'product_code' => $Necc->product_code,
                        'product_unit' => $Necc->product_unit,
                        'product_name' => $Necc->product_name ?? 'Sản phẩm bị xóa',
                        'customer_code' => $Necc->customer_code ?? sc_language_render('customer.delete'),
                        'customer_name' => $Necc->customer_name ,
                        'qty' => $Necc->qty,
                        'note' => $Necc->commnet ?? '',
                        'detail_id' => $Necc->detail_id,
                    ]
                );
            }
        }

        return $dataMerge;
    }

    /**
     * Lấy hàng khô davicook đổ về báo cáo 2 chỉ tiêu bán hàng.
     * @param  array  $dataSearch
     * @param $productId
     * @return mixed
     */
    public function getDryProductToReportTwoTarget(array $dataSearch, $typeProduct = null, $status, $date)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $keyZone= $dataSearch['key_zone'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
        })
        ->leftjoin(SC_DB_PREFIX . "shop_davicook_customer as sc", function ($join) {
            $join->on(SC_DB_PREFIX . "shop_davicook_order.customer_id", "sc.id");
        });
        if ($keyZone) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyZone) {
                $sql->where('sc.zone_id', $keyZone);
            });
        }

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $endToDay);
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

        $dataTmp = $dataTmp->whereIn("sod.product_type", $typeProduct)
            ->whereIn($nameTable . ".status", $status)
            ->where("sod.total_bom", '>', 0)
            ->select($nameTable . ".customer_name",
                $nameTable . ".type as type_order",
                $nameTable . ".id as order_id",
                $nameTable . ".customer_code",
                $nameTable . ".export_date",
                "sod.product_name",
                "sod.product_id",
                "sod.comment" ,
                'sod.real_total_bom as qty',
                'sod.type',
                'sod.product_code',
                'sod.product_unit',
                'sod.id as detail_id')
            ->orderBy("sod.product_name", 'ASC')->get();

        # Đối với các mặt hàng khô davicook -> ko theo từng đơn mà cộng gộp lại theo customer và ngày xuất khô.
        $dataTmp = $dataTmp->groupBy(['customer_code', 'export_date', 'product_id']);
        $dataMerge = new Collection();
        foreach ($dataTmp as $keyCustomer => $customers) {
            foreach ($customers as $keyDate => $exportDates) {
                foreach ($exportDates as $keyProduct => $products) {
                    $commentCombineSubExtra = '';
                    $k = 0;
                    $commentItemExtra = '';
                    $commentCombineExtra = ($products->first()->product_name ?? '' ) .' : '. number_format($products->sum('qty'), 2);
                    foreach ($products as $product) {
                        if ($product->comment != '') {
                            $k++;
                            $commentItemExtra = $product->comment;
                            $commentCombineSubExtra .= number_format($product->qty, 2). ' : ' . $product->comment . ', ';
                        }
                    }
                    $dataMerge->push(
                        [
                            'product_id' => $products->first()->product_id,
                            'product_code' => $products->first()->product_code,
                            'product_unit' => $products->first()->product_unit,
                            'product_name' => $products->first()->product_name ?? 'Sản phẩm bị xóa',
                            'customer_code' => $products->first()->customer_code ?? sc_language_render('customer.delete'),
                            'customer_name' => $products->first()->customer_name ,
                            'qty' => $products->sum('qty'),
                            'note' => ($k == 1) ? $commentItemExtra : ( ($commentCombineSubExtra != '') ?
                                ( $commentCombineExtra . ' ( ' . rtrim($commentCombineSubExtra, ", ") . ' )' ) : '' ),
                            'detail_id' => $products->first()->detail_id,
                        ]
                    );
                }
            }
        }

        return $dataMerge;
    }

    /**
     * Get data report meal difference.
     * @param array $dataSearch
     * @return array
     */
    public function getAllOrderReportMealDifference(array $dataSearch, $product_id = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = empty($product_id) ? ($dataSearch['keyword'] ?? '') : '';
        $arrData = new Collection();
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $tableName = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
        });

        if ($product_id) {
            $dataTmp = $dataTmp->where("sod.product_id", $product_id);
        }

        $dataTmp = $dataTmp->where(function ($sql){
            $sql->where(SC_DB_PREFIX . "shop_davicook_order.status" , 0)
                ->orWhere(SC_DB_PREFIX . "shop_davicook_order.status" , 1)
                ->orWhere(SC_DB_PREFIX . "shop_davicook_order.status" , 2);
        });

        $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order.type", 0);

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($tableName . ".bill_date", ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($tableName . ".bill_date", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where($tableName . ".bill_date", ">=", $startToDay)
                ->where($tableName.".bill_date", "<=", $endToDay);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $tableName) {
                $sql->where("sod.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sod.product_code", "like", "%" . $keyword . "%")
                    ->orWhere($tableName.".customer_code", "like", "%" . $keyword . "%")
                    ->orWhere($tableName.".customer_name", "like", "%" . $keyword . "%");
            });
        }
        $dataTmp = $dataTmp->select($tableName . '.id', $tableName.'.number_of_servings',
            $tableName.'.number_of_reality_servings', 'sod.product_name', 'sod.import_price', 'sod.qty',
            'sod.bom', 'sod.bom_origin', 'sod.total_bom', 'sod.real_total_bom', 'sod.type',
            'sod.product_id', 'sod.product_unit', 'sod.product_code')
            ->orderBy("sod.product_name", 'ASC')
            ->orderBy('sod.qty', 'ASC')->get();
        $number_of_servings = 0;
        $number_of_servings_fact = 0;
        foreach ($dataTmp->groupBy('id') as $key => $item) {
            $number_of_servings += $item->first()->number_of_reality_servings;
            $number_of_servings_fact += $item->first()->number_of_reality_servings;
        }
        foreach ($dataTmp->groupBy('product_id') as $key => $value) {
            $extra_bom = 0;
            $extra_bom_price = 0;
            $qty_total = 0;
            $qty_total_fact = 0;
            $price_menu = 0;
            $price_menu_fact = 0;
            foreach ($value as $keyProduct => $product) {
                if ($product->type == 1) {
                    $extra_bom += round($product->real_total_bom, 2);
                    $extra_bom_price += round($product->real_total_bom * $product->import_price, 2);
                } else {
                    $qty_total += round($product->number_of_reality_servings * $product->bom_origin,2);
                    $qty_total_fact += round($product->real_total_bom,2);
//             CT cũ   ($product->qty * $product->bom)
                    $price_menu += round($product->number_of_reality_servings * $product->bom_origin, 2) * ($product->import_price);
                    $price_menu_fact += round($product->real_total_bom,2) * ($product->import_price);
                }
            }

            if (round($qty_total,2) != round($qty_total_fact + $extra_bom, 2)) {
                $arrData->push([
                    'product_name' => $value->first()->product_name,
                    'product_id' => $key,
                    'product_unit' => $value->first()->product_unit,
                    'product_code' => $value->first()->product_code,
                    'extra_bom' => $extra_bom,
                    'extra_bom_price' => $extra_bom_price,
                    'qty_total' => $qty_total,
                    'qty_total_fact' => $qty_total_fact,
                    'price_menu' =>  $price_menu,
                    'price_menu_fact' => $price_menu_fact,
                ]);
            }
        }
        $data = [
            'number_of_servings' => $number_of_servings,
            'number_of_servings_fact' => $number_of_servings_fact,
            'details' => $arrData,
        ];

        return $data;
    }

    /**
     * @param array $dataSearch
     * @param $product_id
     * @return mixed
     */
    public function getAllOrderReportMealDifferenceDetail(array $dataSearch, $product_id)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->leftjoin(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
            $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
        });

        $dataTmp = $dataTmp->where("sod.product_id", $product_id);

        $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
            $sql->where(SC_DB_PREFIX . "shop_davicook_order.status" , 0)
                ->orWhere(SC_DB_PREFIX . "shop_davicook_order.status" , 1)
                ->orWhere(SC_DB_PREFIX . "shop_davicook_order.status" , 2);
        });

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTable . ".bill_date", ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTable . ".bill_date", "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where($nameTable . ".bill_date", ">=", $startToDay)
                ->where($nameTable . ".bill_date", "<=", $endToDay);
        }

        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTable)  {
                $sql->where($nameTable . ".customer_name", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_code", "like", "%" . $keyword . "%");
            });
        }

        $dataTmp = $dataTmp
            ->selectRaw($nameTable . '.customer_name, '
                .$nameTable.'.bill_date, '
                .$nameTable.'.customer_code, sod.product_name as name, sod.product_id, sod.product_code as sku, sod.import_price, SUM(
                '.$nameTable.'.number_of_reality_servings*sod.bom_origin) as qty_total, SUM(
            sod.real_total_bom) as qty_total_fact, SUM(
            '.$nameTable.'.number_of_reality_servings*sod.bom_origin*sod.import_price) as price_menu, SUM(sod.real_total_bom*sod.import_price) as price_menu_fact, SUM(sod.real_total_bom), sod.type')
            ->groupBy($nameTable . '.bill_date')
            ->groupBy($nameTable . '.customer_code')
            ->orderBy($nameTable . '.bill_date', 'DESC')
            ->orderBy('sod.qty', 'ASC');
        return $dataTmp;
    }

    //Report
    // báo cáo ghi chú order
    public function getNoteReportOrder(array $dataSearch)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $name = $dataSearch['name'] ?? '';

        $dataTmp = AdminDavicookOrder::whereIn('status', [0,1,2]);
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();
        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("delivery_date", ">=", $from_to);
        }
        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where("delivery_date", "<=", $end_to);
        }
        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where("delivery_date", ">=", $startToDay)
                ->where("delivery_date", "<=", $endToDay);
        }
        if ($name) {
            $dataTmp = $dataTmp->where(function ($sql) use ($name) {
                $sql->where('customer_name', 'like', '%' . $name . '%')
                    ->orWhere('customer_code', 'like', '%' . $name . '%')
                    ->orWhere('comment', 'like', '%' . $name . '%');
            });
        }

        $dataTmp = $dataTmp->with('details')->orderBy('customer_name', 'ASC');
        return $dataTmp;
    }

    /**
     * Update purchase priority level order
     * @param $orderId 
     */
    public static function UpdatePurchasePriorityLevel($orderId)
    {
        $item_detail = ShopDavicookOrderDetail::where('order_id', $orderId)->where('product_priority_level', 1)->first();
        $order = AdminDavicookOrder::getOrderDavicookAdmin($orderId);
        if (isset($item_detail)) {
            $order->purchase_priority_level = 1;
        } else {
            $order->purchase_priority_level = 0;
        }
        $order->save();
    }

    /**
     * Update order dacicook status when order has product price = 0
     * @param $orderId 
     */
    public static function UpdateStatusOrder($orderId) {
        $items = ShopDavicookOrderDetail::where('order_id', $orderId)->where('import_price', 0)->get();
        if(count($items)>0) {
            ShopDavicookOrder::findOrFail($orderId)->update(['status' => 0]);
        } else {
            ShopDavicookOrder::findOrFail($orderId)->update(['status' => 1]);
        }
    }

    /**
     * Lấy dữ liệu trả hàng davicook.
     * Lấy đơn hàng trả.
     * Lấy đơn hàng davicook diễn giải là hàng đợt 2.
     * @param  array  $dataSearch
     * @param $productId
     * @return mixed
     */
    public function getOrderReturnHistoryDavicook(array $dataSearch)
    {
        $dataMerge = new Collection();
        $from_to = $dataSearch['from_to'] ? convertVnDateObject($dataSearch['from_to'])->startOfDay()->toDateTimeString()
            : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $end_to = $dataSearch['end_to'] ? convertVnDateObject($dataSearch['end_to'])->endOfDay()->toDateTimeString()
            : Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();
        $explain = $dataSearch['explain'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;

        # Lấy dơn hàng đợt 2 davicook.
        $dataOrder = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sod.order_id");
        });

        if($from_to){
            $dataOrder = $dataOrder->whereDate($nameTable . ".delivery_date", ">=", $from_to);
        }

        if($end_to){
            $dataOrder = $dataOrder->whereDate($nameTable . "..delivery_date", "<=", $end_to);
        }

        if ($explain) {
            $dataOrder = $dataOrder->where($nameTable.".explain", $explain);
        } else {
            $dataOrder = $dataOrder->where($nameTable.".explain", 'Hàng đợt 2');
        }
        $dataOrder = $dataOrder->whereIn($nameTable.".status", [0,1,2]);
        if ($keyword) {
            $dataOrder = $dataOrder->where(function ($sql) use ($keyword, $nameTable) {
                $sql->where("sod.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sod.product_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_name", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".id_name", "like", "%" . $keyword . "%")
                ;
            });
        }

        $dataOrder = $dataOrder->select($nameTable . ".customer_name",
                $nameTable . '.id_name',
                $nameTable . '.id',
                $nameTable . '.customer_code',
                $nameTable . '.delivery_date',
                $nameTable . '.explain',
                'sod.product_name',
                'sod.product_code',
                'sod.product_unit',
                'sod.real_total_bom as qty',
                'sod.import_price as price')->orderBy("sod.product_name", 'ASC')->get();

        # Lấy đơn hàng trả davicook.
        $dataReturn = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_return_history as sdorh", function($join) use ($nameTable){
            $join->on($nameTable . ".id", "sdorh.order_id");
        });

        if($from_to){
            $dataReturn = $dataReturn->whereDate($nameTable . ".delivery_date", ">=", $from_to);
        }

        if($end_to){
            $dataReturn = $dataReturn->whereDate($nameTable . "..delivery_date", "<=", $end_to);
        }

        if ($explain) {
            $dataReturn = $dataReturn->where($nameTable.".explain", $explain);
        }
        $dataReturn = $dataReturn->whereIn($nameTable.".status", [0,1,2]);
        if ($keyword) {
            $dataReturn = $dataReturn->where(function ($sql) use ($keyword, $nameTable) {
                $sql->where("sdorh.product_name", "like", "%" . $keyword . "%")
                    ->orWhere("sdorh.product_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_name", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".customer_code", "like", "%" . $keyword . "%")
                    ->orWhere($nameTable.".id_name", "like", "%" . $keyword . "%")
                ;
            });
        }

        $dataReturn = $dataReturn->select($nameTable . ".customer_name",
            $nameTable . '.id_name',
            $nameTable . '.id',
            $nameTable . '.customer_code',
            $nameTable . '.delivery_date',
            $nameTable . '.explain',
            'sdorh.product_name',
            'sdorh.product_code',
            'sdorh.created_at',
            'sdorh.product_unit',
            'sdorh.return_qty as qty',
            'sdorh.import_price as price')->orderBy("sdorh.created_at", 'desc')->get();

        $dataMerge = $dataReturn->mergeRecursive($dataOrder);

        return $dataMerge;
    }
    public function getDryProductToReportTwoTargetForExport(array $dataSearch, $typeProduct = null, $status, $date)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $keyDepartment = $dataSearch['key_department'] ?? '';
        $keyZone= $dataSearch['key_zone'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
                        $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
                    })
                    ->join(SC_DB_PREFIX . "shop_davicook_customer as sc", function ($join) {
                        $join->on(SC_DB_PREFIX . "shop_davicook_order.customer_id", "sc.id");
                    })
                    ->join(SC_DB_PREFIX . "shop_product as sp", function ($join) {
                        $join->on("sod.product_id", "sp.id");
                    });

        if ($keyZone) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyZone) {
                $sql->where('sc.zone_id', $keyZone);
            });
        }

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $endToDay);
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

        $dataTmp = $dataTmp->whereIn("sod.product_type", $typeProduct)
            ->whereIn($nameTable . ".status", $status)
            ->where("sod.total_bom", '>', 0)
            ->select($nameTable . ".customer_name",
                $nameTable . ".id as order_id",
                $nameTable . ".id_name as order_code",
                $nameTable . ".customer_code",
                $nameTable . ".customer_short_name",
                $nameTable . ".export_date",
                $nameTable . ".explain",
                "sod.product_name",
                "sod.product_id",
                "sod.id_barcode",
                "sod.comment as note" ,
                'sod.real_total_bom as qty',
                'sod.type',
                'sod.product_code',
                'sod.product_unit',
                'sp.category_id',
                'sp.kind',
                'sc.department_id',
                'sc.zone_id',
                'sc.order_num as customer_num',
                'sod.id as detail_id')
            ->orderBy("sod.product_name", 'ASC')->get();
        $dataMerge = new Collection();
        foreach ($dataTmp as $key => $item) {
            $dataMerge->push(
                [
                    'product_id' => $item->product_id,
                    'product_code' => $item->product_code,
                    'product_unit' => $item->product_unit,
                    'product_name' => $item->product_name ?? 'Sản phẩm bị xóa',
                    'customer_code' => $item->customer_code,
                    'customer_name' => $item->customer_name,
                    'customer_short_name' => $item->customer_short_name,
                    'customer_num' => $item->customer_num,
                    'qty' => $item->qty,
                    'note' => $item->note,
                    'detail_id' => $item->detail_id,
                    'order_id' => $item->order_id,
                    'order_code' => $item->order_code,
                    'id_barcode' => $item->id_barcode,
                    'object_id' =>  '',
                    'delivery_date' => $item->export_date,
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

    public function getFreshProductToReportTwoTargetForExport(array $dataSearch, $typeProduct = null, $status, $date)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $keyZone= $dataSearch['key_zone'] ?? '';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $objOrder = new AdminDavicookOrder();
        $nameTable = $objOrder->table;
        $dataTmp = $objOrder->join(SC_DB_PREFIX . "shop_davicook_order_detail as sod", function($join){
                        $join->on(SC_DB_PREFIX . "shop_davicook_order.id", "sod.order_id");
                    })
                    ->join(SC_DB_PREFIX . "shop_davicook_customer as sc", function ($join) {
                        $join->on(SC_DB_PREFIX . "shop_davicook_order.customer_id", "sc.id");
                    })
                    ->join(SC_DB_PREFIX . "shop_product as sp", function ($join) {
                        $join->on("sod.product_id", "sp.id");
                    });

        if ($keyZone) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyZone) {
                $sql->where('sc.zone_id', $keyZone);
            });
        }

        if($from_to){
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $from_to);
        }

        if($end_to){
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $end_to);
        }

        if (empty($from_to) && empty($end_to)) {
            $dataTmp = $dataTmp->where(SC_DB_PREFIX . "shop_davicook_order." . $date, ">=", $startToDay)
                ->where(SC_DB_PREFIX . "shop_davicook_order." . $date, "<=", $endToDay);
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

        $dataTmp = $dataTmp->whereIn("sod.product_type", $typeProduct)
            ->whereIn($nameTable . ".status", $status)
            ->where("sod.total_bom", '>', 0)
            ->select($nameTable . ".customer_name",
                $nameTable . ".type as type_order",
                $nameTable . ".id as order_id",
                $nameTable . ".id_name as order_code",
                $nameTable . ".customer_code",
                $nameTable . ".customer_short_name",
                $nameTable . ".delivery_date",
                $nameTable . ".explain",
                "sod.product_name",
                "sod.product_id",
                "sod.id_barcode",
                "sod.comment as note" ,
                'sod.real_total_bom as qty',
                'sod.type',
                'sod.id as detail_id',
                'sod.product_code',
                'sp.category_id',
                'sp.kind',
                'sc.department_id',
                'sc.zone_id',
                'sc.order_num as customer_num',
                'sod.product_unit')
            ->orderBy("sod.product_name", 'ASC')->get();
        $dataMerge = new Collection();
        foreach ($dataTmp as $key => $item) {
            $dataMerge->push(
                [
                    'product_id' => $item->product_id,
                    'product_code' => $item->product_code,
                    'product_unit' => $item->product_unit,
                    'product_name' => $item->product_name ?? 'Sản phẩm bị xóa',
                    'customer_code' => $item->customer_code,
                    'customer_name' => $item->customer_name,
                    'customer_short_name' => $item->customer_short_name,
                    'customer_num' => $item->customer_num,
                    'qty' => $item->qty,
                    'note' => $item->note,
                    'detail_id' => $item->detail_id,
                    'order_id' => $item->order_id,
                    'order_code' => $item->order_code,
                    'id_barcode' => $item->id_barcode,
                    'object_id' => '',
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
}