<?php

namespace App\Admin\Models;

use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopEInvoice;
use App\Front\Models\ShopEInvoiceDetail;
use App\Front\Models\ShopUserPriceboard;
use App\Front\Models\ShopProduct;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use SCart\Core\Front\Models\ShopOrder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class AdminEInvoice extends ShopEInvoice
{
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

    /**\
     * Lấy lịch sử và hóa đơn gộp ở E-invoice.
     * @param $id
     * @return Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public static function getEinvoices($id)
    {
        $data = self::with(['histories', 'multipleEinvoices', 'details'])
            ->where('id', $id);
        return $data->first();
    }

    /**
     * Lấy dự liệu chi tiết sản phẩm E-Invoice.
     * @param $id
     */
    public static function getEinvoiceDetails($id)
    {
        return (new ShopEInvoiceDetail())->whereIn('einv_id', $id)->get();
    }

    /**
     * Lấy dữ liệu list E-Invoice.
     * @param array $dataSearch
     * @param null $all
     * @return mixed
     */
    public static function getListAllEInvoiceAdmin(array $dataSearch, $all = null)
    {
        $customer_name = $dataSearch['customer_name'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $invoice_status = $dataSearch['invoice_status'];
        $customer_kind = $dataSearch['customer_kind'];
        $limit = $dataSearch['limit'] ?? '';
        $code = $dataSearch['code'];

        $invoiceList = (new ShopEInvoice);

        if($invoice_status != '')   {
            $invoiceList = $invoiceList->where('process_status', (int) $invoice_status);
        }

        if($customer_kind != '')   {
            $invoiceList = $invoiceList->where('customer_kind', (int) $customer_kind);
        }

        if ($customer_name) {
            $invoiceList = $invoiceList->where('customer_name', 'like', "%$customer_name%");
        }

        if ($code) {
            $invoiceList = $invoiceList->where('order_id', 'like', "%$code%");
        } else {
            $invoiceList = $invoiceList->where('del_flag', 0);
        }

        if ($from_to) {
            $from_to = convertVnDateObject($from_to)->startOfDay()->toDateTimeString();
            $invoiceList = $invoiceList->where(function ($sql) use ($from_to) {
                $sql->where('invoice_date', '>=', $from_to);
            });
        }

        if ($end_to) {
            $end_to = convertVnDateObject($end_to)->endOfDay()->toDateTimeString();
            $invoiceList = $invoiceList->where(function ($sql) use ($end_to) {
                $sql->where('invoice_date', '<=', $end_to);
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $invoiceList = $invoiceList->orderBy($field, $sort_field);
        } else {
            $invoiceList = $invoiceList->orderBy('invoice_date', 'desc');
        }

        if($all){
            return  $invoiceList->get();
        }

        if ($limit) {
            return $invoiceList->paginate($limit);
        }

        return $invoiceList->paginate(config('pagination.admin.order'));
    }

    /**
     * Tính lại tổng tiền hóa đơn
     * @param $id
     */
    public static function updateTotalAmount($id) {
        $einvoice = AdminEInvoice::with('details')->find($id);
        $sum = 0;
        foreach ($einvoice->details as $value) {
            $sum += $value->qty * $value->price;
        }
        $einvoice->total_amount = $sum;
        $einvoice->save();
    }

    /**
     * Lấy dự liệu đơn báo cáo phần ảo E-Invoice.
     */
    public static function getVirtualOrderData($order_id)
    {
        $get_invoice_by_parent_id = ShopEInvoice::where('parent_id',$order_id)->get();
        $get_invoice_by_id = ShopEInvoice::where('id',$order_id)->first();
        if($get_invoice_by_parent_id && count($get_invoice_by_parent_id)>0) {
            foreach($get_invoice_by_parent_id as $invoice) {
                $ids[] = $invoice->id;
            } 
        } else{
            $ids[] = $get_invoice_by_id->id;
        }
        $objEInvoice = new AdminEInvoice();
        $data = $objEInvoice
            ->leftJoin(SC_DB_PREFIX . "shop_einvoice_detail as sed", function($join){
                $join->on(SC_DB_PREFIX . "shop_einvoice.id", "sed.einv_id");
            })
            ->leftJoin(SC_DB_PREFIX . "shop_einvoice_sync_history as sesh", function ($join){
                $join->on(SC_DB_PREFIX . "shop_einvoice.id", "sesh.einv_id");
            });
        $data = $data
            ->whereIn(SC_DB_PREFIX . "shop_einvoice.id", $ids)
            ->select(SC_DB_PREFIX . "shop_einvoice.invoice_date", SC_DB_PREFIX . "shop_einvoice.customer_code", SC_DB_PREFIX . "shop_einvoice.customer_name", 
                     SC_DB_PREFIX . "shop_einvoice.einv_id", SC_DB_PREFIX . "shop_einvoice.total_amount",SC_DB_PREFIX . "shop_einvoice.order_id", SC_DB_PREFIX . "shop_einvoice.id",
                     SC_DB_PREFIX . "shop_einvoice.delivery_date", SC_DB_PREFIX . "shop_einvoice.customer_address", SC_DB_PREFIX . "shop_einvoice.plan_start_date",
                "sed.qty", "sed.unit as unit_name", "sed.product_name", "sed.price", "sed.product_code" )
            ->orderBy(SC_DB_PREFIX . "shop_einvoice.einv_id", 'ASC')->get();
        return $data;
    }

    /**
     * Lấy dự liệu gộp đơn báo cáo phần ảo E-Invoice.
     */
    public static function getVirtualOrderCombineData($id_orders)
    {
        foreach ($id_orders as $id) {
            $ids[] = (int)$id;
        }
        $dataArr = new Collection();
        foreach ($ids as $id) {
            $einvoiceIds = ShopEInvoice::where('id', $id)->get()->pluck('id');
            $parentEinvoice = ShopEInvoice::find($id);
            // $invoice_date = date('d/m/Y', strtotime($parentEinvoice->invoice_date ?? now()));
            $plan_start_date = date('d/m/Y', strtotime($parentEinvoice->plan_start_date ?? now()));
            $details = ShopEInvoiceDetail::select('*', DB::raw('SUM(qty) as sum_qty'))->whereIn('einv_id', $einvoiceIds)->groupBy('product_code', 'price')->get();
            if (count($details) > 0) {
                foreach ($details as $key => $item) {
                    $dataArr->push(
                        [
                            'customer_code' => $parentEinvoice->customer_code ?? '',
                            'customer_name' => $parentEinvoice->customer_name ?? '',
                            'customer_address' => $parentEinvoice->customer_address ?? '',
                            'einvoice_id' => $parentEinvoice->einv_id ?? '......',
                            // 'einvoice_date' => $invoice_date ?? '',
                            'plan_start_date' => $plan_start_date ?? '',
                            'product_code' => $item->product_code ?? '',
                            'product_name' => $item->product_name ?? '',
                            'price' => $item->price ?? 0,
                            'qty' => $item->sum_qty ?? 0,
                            'unit' => $item->unit ?? '',
                        ]
                    );
                }
            } else {
                $dataArr->push(
                    [
                        'customer_code' => $parentEinvoice->customer_code ?? '',
                        'customer_name' => $parentEinvoice->customer_name ?? '',
                        'customer_address' => $parentEinvoice->customer_address ?? '',
                        'einvoice_id' => $parentEinvoice->einv_id ?? '......',
                        // 'einvoice_date' => $invoice_date ?? '',
                        'plan_start_date' => $plan_start_date ?? '',
                        'product_code' => '',
                        'product_name' => '',
                        'price' => 0,
                        'qty' => 0,
                        'unit' => '',
                    ]
                );
            }
        }
        $dataArr = $dataArr->sortBy([
            'plan_start_date', 'asc',
           'einvoice_id', 'asc',
        ]);
        return $dataArr;
    }
}