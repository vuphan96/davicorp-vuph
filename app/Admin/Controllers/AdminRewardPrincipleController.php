<?php

namespace App\Admin\Controllers;

use App\Admin\Models\AdminRewardPrinciple;
use App\Front\Models\ShopRewardPrinciple;
use App\Http\Requests\Admin\AdminRewardPrincipleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SCart\Core\Admin\Controllers\RootAdminController;
use SCart\Core\Front\Models\ShopLanguage;

class AdminRewardPrincipleController extends RootAdminController
{
    public $languages;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
    }

    public function principle()
    {
        $data = [
            'title' => sc_language_render('reward_principle.create_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('reward_principle.create_title'),
            'icon' => 'fas fa-award',
            'languages' => $this->languages,
            'url_action' => sc_route_admin('admin_point_setting.principle.edit'),
            'data' => ShopRewardPrinciple::where('is_weekend', 0)->get(),
        ];

        return view($this->templatePathAdmin . 'screen.points.point_for_day')
            ->with($data);
    }

    /**
     * Điểm thưởng cho các đơn đặt hàng vào t7 cn.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showPointForWeekend()
    {
        $data = [
            'title' => sc_language_render('reward_principle.create_title'),
            'subTitle' => '',
            'title_description' => sc_language_render('reward_principle.create_title'),
            'icon' => 'fas fa-award',
            'languages' => $this->languages,
            'url_action' => sc_route_admin('admin_point_setting.principle.edit'),
            'data' => ShopRewardPrinciple::where('is_weekend', 1)->get(),
        ];

        return view($this->templatePathAdmin . 'screen.points.point_for_weekend')
            ->with($data);
    }

    /**
     * Handle chính sách điểm thưởng.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postPrinciple()
    {
        $point = \request('point');
        $from = \request('from');
        $to = \request('to');
        $is_admin = \request('is_admin');
        $is_weekend = \request('is_weekend');
        DB::beginTransaction();
        try {
            ShopRewardPrinciple::where('is_admin', $is_admin)->where('is_weekend', $is_weekend)->delete();
            foreach ($from as $key => $value) {
                if ($value == '') {
                    throw new \Exception('Thời gian đặt hàng không dc trống!');
                }
                if ($to[$key] == '') {
                    throw new \Exception('Thời gian đặt hàng không dc trống!');
                }
                $insertData = [
                    'from' => $value,
                    'to' => $to[$key],
                    'point' => $point[$key],
                    'is_admin' => $is_admin,
                    'is_weekend' => $is_weekend,
                ];
                ShopRewardPrinciple::create($insertData);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->with('error', sc_language_render('action.failed'));
        }

        DB::commit();
        return redirect()->back()->with('success', sc_language_render('action.update_success'));
    }

}
