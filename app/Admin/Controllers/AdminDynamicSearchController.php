<?php

namespace App\Admin\Controllers;

use App\Front\Models\ShopDepartment;
use App\Front\Models\ShopDish;
use App\Front\Models\ShopProduct;
use App\Front\Models\ShopSupplier;
use App\Front\Models\ShopZone;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Admin\Models\AdminDavicookCustomer;
use App\Admin\Models\AdminProductPrice;

class AdminDynamicSearchController extends Controller
{
    public function getDynamicPriceBoard(Request $request)
    {
        $result = AdminProductPrice::where('name', 'like', "%$request->search%")->paginate(config('pagination.search.default'));
        $output = [];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function getDynamicProduct(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $page = $request->page ?? 1;
        // $result = ShopProduct::with('descriptions')->whereHas('descriptions', function ($query) use ($keyword) {
        //     $query
        //         ->where('lang', sc_get_locale())
        //         ->where('name', 'like', "%$keyword%");})
        $result = ShopProduct::where('name', 'like', "%$keyword%")   
            ->paginate(config('pagination.search.default'), ['*'], 'page', $page);

        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->getName(),
                'unit' => $item->unit->name
            ];
        }
        return response()->json($output);
    }

//    get Dynamic product customer
    public function getDynamicProductCustomer(Request $request)
    {

        $keyword = $request->keyword ?? '';
        $customer_id = $request->id ?? '';
        $page = $request->page ?? 1;
        $today = now()->format('Y-m-d');
        
        $result = ShopProduct::with('davicookProductSuppliers')->where('name', 'like', "%$keyword%")

            ->whereHas('davicookProductSuppliers', function ($query) use ($customer_id) {
                $query->where('customer_id', $customer_id);
            })
            ->paginate(config('pagination.search.default'), ['*'], 'page', $page);

        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->getName(),
                'unit' => $item->unit->name,
                'import_price' => (new AdminDavicookCustomer())->getImportPrice($customer_id, $item->id, $today)
            ];
        }
        return response()->json($output);
    }
    // search dish

    public function getDynamicDish(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $page = $request->page ?? 1;
        $result = ShopDish::where('name', 'like', "%$keyword%")->paginate(config('pagination.search.default'), ['*'], 'page', $page);
        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function getDynamicSuppplier(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $page = $request->page ?? 1;
        $result = ShopSupplier::where('name', 'like', "%$keyword%")
            ->paginate(config('pagination.search.default'), ['*'], 'page', $page);

        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function getDynamicZone(Request $request)
    {
        $page = $request->page ?? 1;
        $keyword = $request->keyword ?? '';
        $result = ShopZone::where('name', 'like', "%$keyword%")
            ->paginate(config('pagination.search.default'), ['*'], 'page', $page);

        $output = ['pagination' => ['more' => $result->hasMorePages()], 'count_filtered' => $result->total()];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }

    public function getDynamicDepartment(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $result = ShopDepartment::where('name', 'like', "%$keyword%")->paginate(config('pagination.search.default'));
        $output = [];
        foreach ($result as $item) {
            $output['results'][] = [
                'id' => $item->id,
                'text' => $item->name,
            ];
        }
        return response()->json($output);
    }
}
