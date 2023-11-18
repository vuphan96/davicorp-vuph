<?php

namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopOrder;
use App\Front\Models\ShopOrderDetail;
use App\Front\Models\ShopPoint;
use Carbon\Carbon;
use SCart\Core\Front\Models\ShopCustomerAddress;

class AdminCustomer extends ShopCustomer
{
    protected static $getListTitleAdmin = null;
    protected static $getListCustomerGroupByParentAdmin = null;
    private static $getList = null;

    /**
     * Get customer detail in admin
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdmin($id)
    {
        return self::where('id', $id)->first();

    }

    /**
     * Get customer detail in admin json
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getCustomerAdminJson($id)
    {
        return self::getCustomerAdmin($id)
            ->toJson();
    }

    /**
     * Get list customer in admin
     *
     * @param   [array]  $dataSearch  [$dataSearch description]
     *
     * @return  [type]               [return description]
     */
    public static function getCustomerListAdmin(array $dataSearch)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';
        $delivery_date = $dataSearch['delivery_date'] ?? '';

        $customerList = (new ShopCustomer);

        if ($keyword) {
            $customerList = $customerList->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('customer_code', 'like', '%' . $keyword . '%');
        }
        if($delivery_date){
            $delivery_date = convertVnDateObject($delivery_date)->startOfDay()->toDateTimeString();
            $customerIds = ShopOrder::whereDate('delivery_time', $delivery_date)->whereIn('status', [1,2])->get()->pluck('customer_id')->unique();
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
        $customerList = $customerList->paginate(config('pagination.admin.customer'));

        return $customerList;
    }

    /**
     * Find address id
     *
     * @param   [type]  $id  [$id description]
     *
     * @return  [type]       [return description]
     */
    public static function getAddress($id)
    {
        return ShopCustomerAddress::find($id);
    }

    /**
     * Delete address id
     *
     * @return  [type]  [return description]
     */
    public static function deleteAddress($id)
    {
        return ShopCustomerAddress::where('id', $id)->delete();
    }

    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTotalCustomer()
    {
        return self::count();
    }


    /**
     * Get total customer of system
     *
     * @return  [type]  [return description]
     */
    public static function getTopCustomer()
    {
        return self::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }


    /**
     * [getListAll description]
     * Performance can be affected if the data is too large
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::where('store_id', session('adminStoreId'))
                ->get()->keyBy('id');
        }
        return self::$getList;
    }

    public function getCustomerOrderInToday($dataSearch) {
        $keyword = $dataSearch['keyword'] ?? '';
        $customer_status = $dataSearch['customer_status'] ?? '';
        $delivery_date = $dataSearch['delivery_date'] ?? '';
        $startToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->startOfDay()->toDateTimeString();
        $endToDay = Carbon::createFromFormat('Y-m-d', carbon::today()->toDateString())->endOfDay()->toDateTimeString();
        $customerList = (new ShopCustomer);
        if($delivery_date){
            $delivery_date = convertVnDateObject($delivery_date)->startOfDay()->toDateTimeString();
            $customerIds = ShopOrder::whereDate('delivery_time', $delivery_date)->whereIn('status', [1,2])->get()->pluck('customer_id')->unique();

        } else {
            $customerIds = ShopOrder::whereBetween('delivery_time', [$startToDay, $endToDay])->whereIn('status', [1,2])->get()->pluck('customer_id')->unique();
        }

        if ($customer_status == '' || $customer_status == 1) {
            $customerList = $customerList->whereNotIn('id', $customerIds);
        }

        if ($customer_status == 2) {
            $customerList = $customerList->whereIn('id', $customerIds);
        }

        if ($keyword) {
            $customerList = $customerList->where(function ($sql) use ($keyword) {
                $sql->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('customer_code', 'like', '%' . $keyword . '%');
            });
        }

        $customerList = $customerList->where('status', 1)->orderBy('created_at', 'DESC');
        $customerList = $customerList->paginate(config('pagination.admin.customer') ?? 25);

        return $customerList;
    }

    function getCustomerExportList($filter = null, $ids = null)
    {
        $customers = (new AdminCustomer)
            ->with('zone')
            ->with('department')
            ->with('productSuppliers', 'productSuppliers.product', 'productSuppliers.product', 'productSuppliers.supplier')
            ->with('tier')
            ->orderBy('name');
        if ($filter) {
            $customers = $customers->where('name', 'like', "%$filter%");
        }
        if ($ids) {
            $customers = $customers->whereIn('id', $ids);
        }

        $customers = $customers->get();
        $excelOutput = [];
        $executed = 0;
        foreach ($customers as $key => $customer) {
            $currentProductList = $customer->productSuppliers;
            if (!empty($currentProductList->toArray())) {
                $first = 1;
                foreach ($currentProductList as $pList) {
                    $product = $currentProductList->pop();
                    if ($first) {
                        if($executed >= config('export.limit')) return 0;
                        $temp = [
                            'customer_code' => $customer->customer_code ?? '',
                            'schoolmaster_code' => $customer->schoolmaster_code ?? '',
                            'name' => $customer->name ?? '',
                            'email' => $customer->email ?? '',
                            'phone' => $customer->phone ?? '',
                            'department' => $customer->department->name ?? '',
                            'address' => $customer->address ?? '',
                            'tier' => $customer->tier->name ?? '',
                            'tax_code' => $customer->tax_code ?? '',
                            'short_name' => $customer->short_name ?? '',
                            'route' => $customer->route ?? '',
                            'order_num' => $customer->order_num ?? '',
                            'zone_code' => $customer->zone->zone_code ?? '',
                            'zone_name' => $customer->zone->name ?? '',
                            'password' => '',
                            'kind' => $customer->kind == 1 ? 'CTY HĐ CT' : ( $customer->kind == 2 ? 'TH HĐ CTY' : ( $customer->kind == 0 ? 'HĐ CH' : 0)),
                            'teacher_code' => $customer->teacher_code,
                            'student_code' => $customer->student_code,
                            'status' => $customer->status ?? 0,
                            'sku' => $product->product->sku ?? '',
                            'product_name' => $product->product->name ?? '',
                            'supplier_code' => $product->supplier->supplier_code ?? '',
                            'supplier_name' => $product->supplier->name ?? '',
                        ];
                        $excelOutput[] = $temp;
                        $first = 0;
                        $executed++;
                    } else {
                        if ($executed >= config('export.limit')) return 0;
                        $temp = [
                            'customer_code' => '',
                            'name' => '',
                            'email' => '',
                            'phone' => '',
                            'department' => '',
                            'tier' => '',
                            'tax_code' => '',
                            'address' => '',
                            'zone_code' => '',
                            'zone_name' => '',
                            'password' => '',
                            'kind' => '',
                            'teacher_code' => '',
                            'student_code' => '',
                            'status' => '',
                            'sku' => $product->product->sku ?? '',
                            'product_name' => $product->product->name,
                            'supplier_code' => $product->supplier->supplier_code ?? '',
                            'supplier_name' => $product->supplier->name ?? '',
                        ];
                        $excelOutput[] = $temp;
                        $executed++;
                    }
                }
            } else {
                if ($executed >= config('export.limit')) return 0;
                $excelOutput[] = [
                    'customer_code' => $customer->customer_code ?? '',
                    'schoolmaster_code' => $customer->schoolmaster_code ?? '',
                    'name' => $customer->name ?? '',
                    'email' => $customer->email ?? '',
                    'phone' => $customer->phone ?? '',
                    'department' => $customer->department->name ?? '',
                    'address' => $customer->address ?? '',
                    'tier' => $customer->tier->name ?? '',
                    'tax_code' => $customer->tax_code ?? '',
                    'no' => $customer->no ?? '',
                    'route' => $customer->route ?? '',
                    'zone_code' => $customer->zone->zone_code ?? '',
                    'zone_name' => $customer->zone->name ?? '',
                    'order_num' => $customer->order_num ?? '',
                    'short_name' => $customer->short_name ?? '',
                    'password' => '',
                    'kind' => $customer->kind == 1 ? 'CTY HĐ CT' : ( $customer->kind == 2 ? 'TH HĐ CTY' : ( $customer->kind == 0 ? 'HĐ CH' : 0)),
                    'teacher_code' => $customer->teacher_code,
                    'student_code' => $customer->student_code,
                    'status' => $customer->status ?? 0,
                    'sku' => '',
                    'product_name' => '',
                    'supplier_code' => '',
                    'supplier_name' => '',
                ];
                $executed++;
            }
        }
        return $excelOutput;
    }

    public function changeTier($tier_id)
    {
        $tier = $this->getReward();
        if (empty($tier)) {
            return (new ShopPoint([
                'customer_id' => $this->id,
                'tier_id' => $tier_id,
                'month' => Carbon::now()->format('m'),
                'year' => Carbon::now()->format('Y')
            ]))->save();
        } else {
            return $tier->fill([
                'tier_id' => $tier_id
            ])->save();
        }
    }
}
