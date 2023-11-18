<?php

namespace App\Admin\Controllers;

use App\Front\Models\ShopCustomer;
use App\Front\Models\ShopZone;
use App\Http\Requests\Admin\AdminZoneRequest;
use Exception;
use SCart\Core\Admin\Controllers\RootAdminController;

class AdminZoneController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $id = request('id') ?? '';
        $keyword = request('keyword') ?? '';

        $data = [
            'title' => sc_language_render('admin.zone.managment_title'),
            'title_action' => empty($id) ? '<i class="fa fa-plus" aria-hidden="true"></i> ' . sc_language_render('admin.zone.create_title') :
                '<i class="fa fa-pencil-alt" aria-hidden="true"></i> ' . sc_language_render('admin.zone.edit_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_zone.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => empty($id) ? sc_route_admin('admin_zone.process') : sc_route_admin('admin_zone.process', ['id' => $id]),
            'zone' => request('id') ? ShopZone::find($id ?? '') : [],
            'backLink' => route('admin_zone.index')
        ];

        $listTh = [
            'code' => sc_language_render('admin.zone.code'),
            'name' => sc_language_render('admin.zone.name'),
            'action' => sc_language_render('action.title'),
        ];
        $data['topMenuRight'][] = '
                <form action="' . sc_route_admin('admin_zone.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                    <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . 'Tìm theo tên khu vực' . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=menuSearch
        $obj = new ShopZone();
        if ($keyword) {
            $obj = $obj->where('name', 'like', '%' . $keyword . '%');
        }
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(config('pagination.admin.small'));

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'zone_code' => $row['zone_code'],
                'name' => $row['name'],
                'action' => '
                    <a data-perm="zone:edit" href="' . sc_route_admin('admin_zone.index', ['id' => $row['id'] ?? 'not-found-id']) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span data-perm="zone:delete" onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);

        $data['layout'] = empty($id) ? 'index' : 'edit';
        return view($this->templatePathAdmin . 'screen.zone')
            ->with($data);
    }

    public function process(AdminZoneRequest $request)
    {
        $id = request('id');
        $data = $request->validated();
        try {
            if (empty($id)) {
                $zone = (new ShopZone)->fill($data);
                if (!$zone->save()) {
                    throw new Exception('Tạo mới thất bại, vui lòng kiểm tra lại');
                }
            } else {
                $zone = (new ShopZone)->find($id);
                $zone->fill($data);
                if (!$zone->save()) {
                    throw new Exception('Sửa thất bại, vui lòng kiểm tra lại');
                }
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->route('admin_zone.index')->with('success', sc_language_render('action.success'));
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        $arrDontPermission = [];
        foreach ($arrID as $key => $id) {
            if (!$this->checkPermisisonItem($id)) {
                $arrDontPermission[] = $id;
            }
        }
        if (count($arrDontPermission)) {
            return response()->json(['error' => 1, 'msg' => "Khu vực không tồn tại hoặc bị đang được gán cho khách hàng. Chi tiết: " . ': ' . json_encode($arrDontPermission)]);
        }
        ShopZone::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    public function checkPermisisonItem($id)
    {
        $zoneVerify = (new ShopZone)->find($id);
        $countUsed = ShopCustomer::where('zone_id', $id)->count();

        if($zoneVerify && $countUsed < 1){
            return $zoneVerify;
        }
        return null;
    }
}
