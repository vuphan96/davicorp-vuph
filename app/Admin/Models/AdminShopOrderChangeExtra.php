<?php
namespace App\Admin\Models;

use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopProduct;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AdminShopOrderChangeExtra extends Model
{
    public $table = SC_DB_PREFIX . 'shop_order_change_extra';
    protected $guarded = [];

    const TYPE_CONTENT = [
        1 => "Chỉnh sửa định lượng món ăn",
        2 => "Chỉnh sửa số lượng",
        3 => "Chỉnh sửa số lượng thực tế",
        4 => "Chỉnh sửa số lượng sản phẩm",
        5 => "Chỉnh sửa số lượng suất ăn chính",
        6 => "Đơn hàng hủy",
        7 => "Thêm mới món ăn",
        8 => "Thêm mới sản phẩm",
        9 => 'Thêm mới nguyên liệu',
        10 => "Xóa nguyên liệu",
        11 => "Xóa sản phẩm",
    ];

    const STATUS = [
        '1' => 'Chưa xuất kho',
        '2' => 'Đã xuất kho',
        '3' => 'Hủy đơn hàng',
    ];

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = sc_generate_id($type = 'shop_order_change_extra');
            }
        });
    }
    public function order()
    {
        return $this->belongsTo(ShopOrder::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }

    // Lấy dữ liệu đổ về báo cáo in tem bổ sung
    public function getStampList($dataSearch, array $ids = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $key_export = $dataSearch['key_export'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $zone = $dataSearch['zone'] ?? [];
        $department = $dataSearch['department'] ?? [];
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $dataTmp = AdminShopOrderChangeExtra::with("product")->join(SC_DB_PREFIX . 'shop_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_change_extra.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_change_extra.category_id');
            })
            ->leftjoin(SC_DB_PREFIX . 'shop_department as ssd', function ($join) {
                $join->on('ssd.id', 'so.department_id');
            }) ->join(SC_DB_PREFIX . "shop_customer as scc", function ($join) {
                $join->on('so.customer_id', '=', 'scc.id');
            });
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
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

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }
        if ($department) {
            $dataTmp = $dataTmp->whereIn('ssd.id', $department);
        }
        if ($zone) {
            $dataTmp = $dataTmp->whereIn('scc.zone_id', $zone);
        }
        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }
//        if ($key_export) {
//            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".kind", $key_export);
//        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrderChangeExtra) {
                $sql->where($nameTableOrderChangeExtra. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrderChangeExtra. '.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_change_extra.id', $ids);
        }
        $dataTmp = $dataTmp->with("product")->whereIn('so.status', [1, 2, 7])->with('order.customer.department')

        ->select($nameTableOrderChangeExtra . '.id as id_change', $nameTableOrderChangeExtra . '.delivery_date_origin', $nameTableOrderChangeExtra . '.order_id', $nameTableOrderChangeExtra . '.id_barcode', $nameTableOrderChangeExtra . '.order_name', $nameTableOrderChangeExtra . '.product_id',
                'so.delivery_time', $nameTableOrderChangeExtra . '.product_short_name  as product_name', $nameTableOrderChangeExtra . '.product_num', $nameTableOrderChangeExtra . '.product_unit',
                $nameTableOrderChangeExtra . '.qty', $nameTableOrderChangeExtra . '.qty_change',
                $nameTableOrderChangeExtra. '.content', 'so.customer_code', 'so.customer_short_name as customer_name', 'so.customer_num',
                'ssd.short_name', 'so.object_id', $nameTableOrderChangeExtra . '.created_at',  $nameTableOrderChangeExtra . '.category_id',  $nameTableOrderChangeExtra . '.content', $nameTableOrderChangeExtra . '.product_code as sku', 'so.name','ssd.id as department_id','scc.zone_id as zone_id')->get();
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
                'id' => $item->id_change,
                'order_id' => $item->order_id,
                'id_barcode' => $item->id_barcode,
                'order_name' => $item->order_name,
                'product_id' => $item->product_id,
                'delivery_time' => $item->delivery_date_origin,
                'product_name' => $item->product_name,
                'order_num' => $item->product_num,
                'name_unit' => $item->product_unit,
                'qty' => $item->qty,
                'qty_change' => $item->qty_change,
                'content' => $item->content,
                'customer_code' => $item->customer_code,
                'customer_name' => $item->customer_name,
                'customer_num' => $item->customer_num,
                'short_name' => $item->short_name,
                'object_id' => $item->object_id,
                'send_time' => $item->created_at,
                'qr_code' => $item->product ? ($item->product->qr_code ?? "") : "",
                'category_id' => $item->category_id ?? "",
                'product_sku' => $item->sku ?? "",
                'customer_fullname' => $item->name ?? ""
            ];

        }
        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }
    public function getProductFreshDavicookToReportStamp($dataSearch, array $ids = null, $select_warehouse = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = AdminShopOrderChangeExtra::with("product")->join(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_change_extra.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_change_extra.category_id');
            });
        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
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

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }
        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrderChangeExtra) {
                $sql->where($nameTableOrderChangeExtra. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrderChangeExtra. '.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_change_extra.id', $ids);
        }
        if (!empty($select_warehouse)) {
            if ($select_warehouse == 2) {
                $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . '.kind', 0)->whereIn('so.status', [2, 7]);
            } else {
                $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . '.kind', 1)->whereIn('so.status', [0, 1, 2, 7]);
            }
        }
        $dataTmp = $dataTmp->with("product")->with('order.customer.department')

            ->select($nameTableOrderChangeExtra . '.id as id_change', $nameTableOrderChangeExtra . '.delivery_date_origin', $nameTableOrderChangeExtra . '.order_id', $nameTableOrderChangeExtra . '.id_barcode', $nameTableOrderChangeExtra . '.order_name', $nameTableOrderChangeExtra . '.product_id',
                'so.delivery_date', $nameTableOrderChangeExtra . '.product_short_name  as product_name', $nameTableOrderChangeExtra . '.product_num', $nameTableOrderChangeExtra . '.product_unit',
                $nameTableOrderChangeExtra . '.qty', $nameTableOrderChangeExtra . '.qty_change',
                $nameTableOrderChangeExtra. '.content', 'so.customer_code', 'so.customer_short_name', 'so.customer_num',
                $nameTableOrderChangeExtra . '.created_at',  $nameTableOrderChangeExtra . '.category_id',  $nameTableOrderChangeExtra . '.content', $nameTableOrderChangeExtra . '.product_code as sku', 'so.customer_name' )->get();
        $arrListStampDetails = [];
        $qrList = new \Illuminate\Support\Collection([]);
        $department_name = ShopDepartment::find(1)->short_name;
        foreach ($dataTmp as $item) {
            $url = $item->product ? ($item->product->qr_code ?? "") : "";
            if($url){
                $qrSearch = $qrList->where("url", $url)->first();
                if(!$qrSearch){
                    $qrList->push(ShopProduct::generateQr($url));
                }
            };
            $arrListStampDetails[] = [
                'id' => $item->id_change,
                'order_id' => $item->order_id,
                'id_barcode' => $item->id_barcode,
                'order_name' => $item->order_name,
                'product_id' => $item->product_id,
                'delivery_time' => $item->delivery_date_origin,
                'product_name' => $item->product_name,
                'order_num' => $item->product_num,
                'name_unit' => $item->product_unit,
                'qty' => $item->qty,
                'qty_change' => $item->qty_change,
                'content' => $item->content,
                'customer_code' => $item->customer_code,
                'customer_name' => $item->customer_short_name,
                'customer_num' => $item->customer_num,
                'short_name' => $department_name,
                'object_id' => '',
                'send_time' => $item->created_at,
                'qr_code' => $item->product ? ($item->product->qr_code ?? "") : "",
                'category_id' => $item->category_id ?? "",
                'product_sku' => $item->sku ?? "",
                'customer_fullname' => $item->customer_name ?? ""
            ];

        }
        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }


    public function getProductDryDavicookToReportStamp($dataSearch, array $ids = null)
    {
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $order_send_time_from = $dataSearch['order_send_time_from'] ?? '';
        $order_send_time_to = $dataSearch['order_send_time_to'] ?? '';
        $order_date_from = $dataSearch['order_date_from'] ?? '';
        $order_date_to = $dataSearch['order_date_to'] ?? '';
        $keyword = $dataSearch['keyword'] ?? '';
        $category = $dataSearch['category'] ?? '';
        $content = $dataSearch['content'] ?? '';
        $nameTableOrderChangeExtra = SC_DB_PREFIX . 'shop_order_change_extra';

        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();

        $dataTmp = AdminShopOrderChangeExtra::with("product")->join(SC_DB_PREFIX . 'shop_davicook_order as so', function ($join) {
            $join->on(SC_DB_PREFIX . 'shop_order_change_extra.order_id', 'so.id');
        })
            ->leftjoin(SC_DB_PREFIX . 'shop_category as sc', function ($join) {
                $join->on('sc.id',SC_DB_PREFIX . 'shop_order_change_extra.category_id');
            });

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", ">=", $from_to);
        }
        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra.".delivery_date_origin", "<=", $end_to);
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

        // Order send time
        if ($order_send_time_from) {
            $order_send_time_from = convertStandardDate($order_send_time_from)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", ">=", $order_send_time_from);
        }
        if ($order_send_time_to) {
            $order_send_time_to = convertStandardDate($order_send_time_to)->toDateTimeString();
            $dataTmp = $dataTmp->where("$nameTableOrderChangeExtra.created_at", "<=", $order_send_time_to);
        }

        if ($category) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".category_id", $category);
        }
        if ($content) {
            $dataTmp = $dataTmp->where($nameTableOrderChangeExtra . ".content", $content);
        }
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword, $nameTableOrderChangeExtra) {
                $sql->where($nameTableOrderChangeExtra. '.product_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.id_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_name', 'like', '%' . $keyword . '%')
                    ->orWhere('so.customer_code', 'like', '%' . $keyword . '%')
                    ->orWhere($nameTableOrderChangeExtra. '.product_code', 'like', '%' . $keyword . '%');
            });
        }
        if (!empty($ids)) {
            $dataTmp = $dataTmp->whereIn(SC_DB_PREFIX . 'shop_order_change_extra.id', $ids);
        }
        //->where('so.status', '!=', 7)
        $dataTmp = $dataTmp->with("product")->where($nameTableOrderChangeExtra . '.kind', 0)->whereIn('so.status', [2, 7])->with('order.customer.department')

            ->select($nameTableOrderChangeExtra . '.id as id_change', $nameTableOrderChangeExtra . '.delivery_date_origin', $nameTableOrderChangeExtra . '.order_id',$nameTableOrderChangeExtra . '.id_barcode', $nameTableOrderChangeExtra . '.order_name', $nameTableOrderChangeExtra . '.product_id',
                'so.delivery_date', $nameTableOrderChangeExtra . '.product_short_name  as product_name', $nameTableOrderChangeExtra . '.product_num', $nameTableOrderChangeExtra . '.product_unit',
                $nameTableOrderChangeExtra . '.qty', $nameTableOrderChangeExtra . '.qty_change',
                $nameTableOrderChangeExtra. '.content', 'so.customer_code', 'so.customer_short_name', 'so.customer_num',
                $nameTableOrderChangeExtra . '.created_at',  $nameTableOrderChangeExtra . '.category_id',  $nameTableOrderChangeExtra . '.content', $nameTableOrderChangeExtra . '.product_code as sku', 'so.customer_name' )->get();
        $arrListStampDetails = [];
        $qrList = new \Illuminate\Support\Collection([]);
        $department_name = ShopDepartment::find(1)->short_name;
        foreach ($dataTmp as $item) {
            $url = $item->product ? ($item->product->qr_code ?? "") : "";
            if($url){
                $qrSearch = $qrList->where("url", $url)->first();
                if(!$qrSearch){
                    $qrList->push(ShopProduct::generateQr($url));
                }
            };
            $arrListStampDetails[] = [
                'id' => $item->id_change,
                'order_id' => $item->order_id,
                'id_barcode' => $item->id_barcode,
                'order_name' => $item->order_name,
                'product_id' => $item->product_id,
                'delivery_time' => $item->delivery_date_origin,
                'product_name' => $item->product_name,
                'order_num' => $item->product_num,
                'name_unit' => $item->product_unit,
                'qty' => $item->qty,
                'qty_change' => $item->qty_change,
                'content' => $item->content,
                'customer_code' => $item->customer_code,
                'customer_name' => $item->customer_short_name,
                'customer_num' => $item->customer_num,
                'short_name' => $department_name,
                'object_id' => '',
                'send_time' => $item->created_at,
                'qr_code' => $item->product ? ($item->product->qr_code ?? "") : "",
                'category_id' => $item->category_id ?? "",
                'product_sku' => $item->sku ?? "",
                'customer_fullname' => $item->customer_name ?? ""
            ];

        }
        return [
            "item" => $arrListStampDetails,
            "qr" => $qrList
        ];
    }
}
















