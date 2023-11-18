<?php

namespace App\Admin\Controllers;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopRewardTier;
use App\Http\Requests\Admin\AdminRewardTierRequest;
use Exception;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminRewardTierController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $id = request('id') ?? '';

        $data = [
            'title' => sc_language_render('reward.tier_title'),
            'title_action' => empty($id) ? '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('reward.tier_create_title') :
                '<i class="fa fa-pencil-alt" aria-hidden="true"></i> ' . sc_language_render('reward.tier_edit_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_point_setting.tier.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort,
            'url_action' => empty($id) ? sc_route_admin('admin_point_setting.tier.index') : sc_route_admin('admin_point_setting.tier.index', ['id' => $id]),
            'tier' => request('id') ? ShopRewardTier::find($id ?? '') : [],
            'backLink' => route('admin_point_setting.tier.index')
        ];

        $listTh = [
            'no' => sc_language_render('no'),
            'name' => sc_language_render('reward.tier.name'),
            'rate' => sc_language_render('reward.tier.rate'),
            'action' => sc_language_render('action.title'),
        ];
        $dataTmp = (new ShopRewardTier)->orderBy('id', 'desc')->paginate(config('pagination.admin.zone'));
        $dataTr = [];
        $index = 0;
        foreach ($dataTmp as $row) {
            $index++;
            $dataTr[$row['id']] = [
                'no' => $index,
                'name' => $row['name'],
                'rate' => sc_currency_render($row['rate'], 'VND'),
                'action' => '<a href="' . sc_route_admin('admin_point_setting.tier.index', ['id' => $row['id'] ?? 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp; <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);


        $data['layout'] = empty($id) ? 'index' : 'edit';
        return view($this->templatePathAdmin . 'screen.reward_tier')
            ->with($data);
    }

    public function process(AdminRewardTierRequest $request)
    {
        $id = request('id');
        $data = $request->validated();
        try {
            if (empty($id)) {
                $tier = (new ShopRewardTier())->fill($data);
                if (!$tier->save()) {
                    throw new Exception('Tạo mới thất bại, vui lòng kiểm tra lại');
                }
            } else {
                $tier = (new ShopRewardTier)->find($id);
                $tier->fill($data);
                if (!$tier->save()) {
                    throw new Exception('Sửa thất bại, vui lòng kiểm tra lại');
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin_point_setting.tier.index')->with('success', sc_language_render('action.success'));
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $arrDontPermission = [];
        foreach ($arrID as $id) {
            if (!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
        }
        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.remove_dont_permisison') . ' hoặc hạng đang được sử dụng <br/>Chi tiết: ' . json_encode($arrDontPermission)]);
        }
        ShopRewardTier::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    public function checkPermisisonItem($id)
    {
        $item = (new ShopRewardTier())->find($id);
        if (!ShopCustomer::where('tier_id', $item->id)->count() > 0) {
            return $item;
        }
    }
}
