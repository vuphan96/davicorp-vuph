<?php
namespace App\Admin\Models;

use App\Front\Models\ShopDavicookCustomer;
use App\Front\Models\ShopDavicookOrder;
use App\Front\Models\ShopDavicookProductSupplier;
use App\Front\Models\ShopImportPriceboard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDavicookCustomer extends ShopDavicookCustomer
{
    public static function getListDavicookCustomerAdmin(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $delivery_date = $dataSearch['delivery_date'] ?? '';

        $customerList = (new ShopDavicookCustomer());
        if ($keyword) {
            $customerList = $customerList->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('customer_code', 'like', '%' . $keyword . '%');
        }
        if($delivery_date){
            $delivery_date = convertVnDateObject($delivery_date)->startOfDay()->toDateTimeString();
            $customerIds = ShopDavicookOrder::whereDate('delivery_date', $delivery_date)->whereIn('status', [0,1,2])->get()->pluck('customer_id')->unique();
        }
        if (isset($customerIds)) {
            $customerList = $customerList->whereNotIn('id', $customerIds)->where('status', 1);
//            dd($customerIds, $customerList->get());
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $customerList = $customerList->orderBy($field, $sort_field);
        } else {
            $customerList = $customerList->orderBy('id', 'desc');
        }
        $customerList = $customerList->paginate(config('pagination.admin.medium'));


        return $customerList;
    }
    public static function getCustomerAdmin($id)
    {
        return self::where('id', $id)->first();

    }
    public function getListCustomerExport($keyword = null, $ids = null)
    {
        $customers = (new ShopDavicookCustomer())->with('zone', 'davicookProductSuppliers', 'davicookProductSuppliers.product', 'davicookProductSuppliers.supplier', 'menu', 'menu.details', 'menu.details.product', 'menu.dish');
        if ($keyword) {
            $customers = $customers->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('customer_code', 'like', '%' . $keyword . '%');
        }
        if ($ids) {
            $customers = $customers->whereIn('id', $ids);
        }
        $customers = $customers->get();
        $arrayCustomer = [];
        foreach ($customers as $index => $customer) {
            $arrayCustomer[$index] = [
                'customer_code' => $customer->customer_code,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'tax_code' => $customer->tax_code,
                'serving_price' => $customer->serving_price,
                'order_num' => $customer->order_num,
                'short_name' => $customer->short_name,
                'route' => $customer->route,
                'address' => $customer->address,
                'zone_code' => $customer->zone ? $customer->zone->zone_code : '',
                'zone_name' => $customer->zone ? $customer->zone->name : '',
                'status' => ($customer->status == 1) ? 1 : 0,
                'davicookProductSuppliers' => [],
                'menu' => [],
            ];
            foreach ($customer->davicookProductSuppliers as $keyProduct => $itemProduct) {
                $arrayCustomer[$index]['davicookProductSuppliers'][] = [
                    'product_sku' => $itemProduct->product ? $itemProduct->product->sku : "Mã sản phẩm đã bị xóa",
                    'product_name' => $itemProduct->product ? ($itemProduct->product ? $itemProduct->product->name : "") : "Sản phẩm đã bị xóa",
                    'supplier_code' => $itemProduct->supplier ? $itemProduct->supplier->supplier_code : "",
                    'supplier_name' => $itemProduct->supplier ? $itemProduct->supplier->name : "",
                ];
            }
            foreach ($customer->menu as $keyMenu => $itemMenu) {
                $arrayCustomer[$index]['menu'][] = [
                    'code' => $itemMenu->dish ? $itemMenu->dish->code : "",
                    'name' => $itemMenu->dish ? $itemMenu->dish->name : "",
                    'is_export_menu' => $itemMenu->is_export_menu ?? '',
                    'qty_cooked_dish' => $itemMenu->qty_cooked_dish ?? '',
                    'details' => []
                ];
                foreach ($itemMenu->details as $keyDetail => $detail) {
                    $arrayCustomer[$index]['menu'][$keyMenu]['details'][] = [
                        'sku' => $detail->product ? $detail->product->sku : "Mã nguyên liệu đã bị xóa",
                        'name' => $detail->product ? ($detail->product ? $detail->product->name : "") : "Nguyên liệu đã bị xóa",
                        'qty' => $detail->qty ?? "",
                        'qty_cooked' => $detail->qty_cooked,
                        'is_spice' => $detail->is_spice,
                    ];
                }
            }
        }
        return $arrayCustomer;

    }

    /**
     * Get ingredient import price davicook customer
     * @param $customer_id ,$product_id,$delivery_time
     * @param $product_id
     * @param $delivery_time
     * @return int|mixed $import_price
     */
    public function getImportPrice($customer_id, $product_id, $delivery_time) {
        $import_price = 0;
        $import_price_detail = DB::table(SC_DB_PREFIX . 'shop_davicook_product_supplier as sdps')
            ->join(SC_DB_PREFIX .'shop_import_priceboard as sip', 'sdps.supplier_id', '=', 'sip.supplier_id')
            ->join(SC_DB_PREFIX .'shop_import_priceboard_detail as sipd', function($join) use ($product_id) {
                $join->on('sip.id', '=', 'sipd.priceboard_id')
                    ->where('sipd.product_id', '=', $product_id);
            })
            ->select('sipd.price')
            ->where('sdps.customer_id', '=', $customer_id)
            ->where('sdps.product_id', '=', $product_id)
            ->whereDate('sip.start_date', '<=', $delivery_time)
            ->whereDate('sip.end_date', '>=', $delivery_time)
            ->first();
        if ($import_price_detail) {
            $import_price = $import_price_detail->price;
        }
    
        return $import_price;
    }

    /**
     * Get the input price according to the latest price list
     * @param $customer_id ,$product_id,$delivery_time
     * @param $product_id
     * @param $delivery_time
     * @return int|mixed $import_price
     */
    public function getImportPriceToLatestPriceTable($customer_id, $product_id, $delivery_time) {
        $import_price = 0;
        $import_price_detail = DB::table(SC_DB_PREFIX . 'shop_davicook_product_supplier as sdps')
            ->join(SC_DB_PREFIX .'shop_import_priceboard as sip', 'sdps.supplier_id', '=', 'sip.supplier_id')
            ->join(SC_DB_PREFIX .'shop_import_priceboard_detail as sipd', function($join) use ($product_id) {
                $join->on('sip.id', '=', 'sipd.priceboard_id')
                    ->where('sipd.product_id', '=', $product_id);
            })
            ->select('sipd.price')
            ->where('sdps.customer_id', '=', $customer_id)
            ->where('sdps.product_id', '=', $product_id)
            ->whereDate('sip.start_date', '<=', $delivery_time)
            ->whereDate('sip.end_date', '>=', $delivery_time)
            ->first();
        if ($import_price_detail) {
            $import_price = $import_price_detail->price;
        } else {
            $supplier_id = ShopDavicookProductSupplier::where('customer_id', $customer_id)->where('product_id', $product_id)->first()->supplier_id ?? '';
            $getDataPriceSupplierFirst = ShopImportPriceboard::with('details')->where('supplier_id', $supplier_id)
                ->orderBy('created_at', 'DESC')->first();
            if ($getDataPriceSupplierFirst) {
                $dataDetailPriceSupplier = $getDataPriceSupplierFirst->details ?? [];
                if ($dataDetailPriceSupplier) {
                    $detail = $dataDetailPriceSupplier->where('product_id', $product_id)->first();
                    return $import_price = $detail->price ?? 0;
                }
            }
        }

        return $import_price;
    }
}