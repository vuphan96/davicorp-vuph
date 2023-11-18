<?php

namespace App\Admin\Models;

use App\Front\Models\ShopRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class AdminRating extends ShopRating
{
    use HasFactory;

    public static function getRatingData(array $dataSearch, $getAll = null)
    {
        $now = now();
        $keyword = $dataSearch['keyword'] ?? "";
        $inputMonth = empty($dataSearch['filter_month']) ? $now->format("m/Y") : $dataSearch['filter_month'];
        $from_to = $dataSearch['from_to'] ?? '';
        $end_to = $dataSearch['end_to'] ?? '';
        $sort_order = $dataSearch['sort_order'] ?? '';
        $arrSort = $dataSearch['arrSort'] ?? '';

        $inputMonth = explode("/", $inputMonth);
        $month = $inputMonth[0] ?? $now->format("m");
        $year = $inputMonth[1] ?? $now->format("Y");

        $ratingList =
            ShopRating::where("year", $year)->where("month", $month)->with("customer");
        if ($keyword) {
            $ratingList = $ratingList->whereHas('customer', function ($query) use ($keyword) {
                $query->where("name", 'like', "%$keyword%");
            });
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $order = explode('__', $sort_order)[1];

            $ratingList = $ratingList->orderBy($field, $order);
        }
        if ($getAll) {
            return $ratingList->get();
        }

        return $ratingList->paginate(config('pagination.admin.big'));
    }
}
