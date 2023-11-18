<?php

namespace App\Admin\Models;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopPoint;
use App\Front\Models\ShopRewardTier;
use App\Front\Models\ShopZone;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AdminPoint extends ShopPoint
{
    use HasFactory;

    public static function getRange($start, $end)
    {
        $result = CarbonPeriod::create("$start-1-1", '1 month', "$end-12-31");
        $output = [];
        foreach ($result as $dt) {
            $output[] = $dt->format("m/Y");
        }
        return $output;
    }

    public static function getRewardData(array $dataSearch, $getAll = null)
    {
        $keyword = $dataSearch['keyword'] ?? '';
        $month = $dataSearch['filter_month'] ?? '';
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';

        $tableCustomer = (new ShopCustomer())->getTable();
        $tablePoint = (new ShopPoint)->getTable();
        $tableTier = (new ShopRewardTier)->getTable();
        $tableZone = (new ShopZone())->getTable();

        $pointList =
            DB::table($tableCustomer . ' as customer')
                ->distinct()
                ->leftJoin($tableTier . ' as tier', 'customer.tier_id', 'tier.id')
                ->leftJoin($tableZone . ' as zone', 'customer.zone_id', 'zone.id')
                ->leftJoin($tablePoint . ' as point', function ($join) use ($month) {
                    $join->on('customer.id', '=', 'point.customer_id');
                    $year_month = explode('/', $month);
                    $join->on('point.month', '=', DB::raw(count($year_month) > 1 ? $year_month[0] : Carbon::now()->format('m')));
                    $join->on('point.year', '=', DB::raw(count($year_month) > 1 ? $year_month[1] : Carbon::now()->format('Y')));
                })
                ->select(['customer.id', 'customer.name', 'customer.customer_code', 'customer.address', 'tier.name as tier_name', 'point.point', 'point.created_at', 'tier.rate', 'zone.zone_code']);
        $pointList = $pointList->orderBy("point", "DESC");
        if ($keyword) {
            $pointList = $pointList->where('customer.name', 'like', "%$keyword%");
        }

        if ($from_to) {
            $pointList = $pointList->where('point.point', '>=', $from_to);
        }
        if ($end_to) {
            $pointList = $pointList->where('point.point', '<=', $end_to);
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];

            $pointList = $pointList->orderBy($field, $sort_field);
        } else {
            $pointList = $pointList->orderBy('customer.name', 'ASC');
        }

        if($getAll){
            return $pointList->get();
        }
        return $pointList->paginate(config('pagination.admin.big'));
    }
}
