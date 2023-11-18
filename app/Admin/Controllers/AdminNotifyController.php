<?php

namespace App\Admin\Controllers;

use App\Exports\AdminExportHistoryNotificationOrder;
use App\Exports\ImportPrice\ExportNotification;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopZone;
use App\Jobs\SendNotify;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminNotification;
use App\Front\Models\ShopCustomer;
use App\Admin\Models\AdminNotificationCustomer;
use App\Admin\Models\AdminNotificationTemplate;
use DB;
use Request;
use App\Http\Requests\Admin\NotifyRequest;
use App\Http\Requests\Admin\AdminNotifyTemplatesRequest;


class AdminNotifyController extends RootAdminController
{
    public $titleFilter;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $data = [
            // ,
            'title' => sc_language_render('admin.notify.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_notify_history.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => ''
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());
        $listTh = [
            'created_at' => sc_language_render('admin.notify.send'),
            'title' => sc_language_render('admin.news.title'),
            'order_id' => 'Mã đơn hàng',
            'customer' => 'Khách hàng',
            'content' => 'Nội dung',
            'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'created_at' => 'text-align:center; width:110px; word-break: normal',
            'title' => 'width:12%;',
            'order_id' => 'width:10% ; text-align:center;',
            'customer' => 'width:15% ; text-align:center;',
            'content' => 'width:45% ; text-align:center; color:',
            'action' => 'text-align:center; min-width:85px',
        ];
        $cssTd = [
            'created_at' => 'text-align:center; word-break: normal',
            'title' => '',
            'action' => 'text-align:center;',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $title = sc_clean(request('title_name') ?? '');
        $sort_order = request('sort_order') ?? 'id_desc';
        $keyword = sc_clean(request('keyword') ?? '');
        $arrSort = [
            SC_DB_PREFIX . 'admin_notification.id__desc' => sc_language_render('filter_sort.id_desc'),
            SC_DB_PREFIX . 'admin_notification.id__asc' => sc_language_render('filter_sort.id_asc'),
            SC_DB_PREFIX . 'shop_customer.name__desc' => sc_language_render('filter_sort.name_desc'),
            SC_DB_PREFIX . 'shop_customer.name__asc' => sc_language_render('filter_sort.name_asc'),
            SC_DB_PREFIX . 'admin_notification.created_at__desc' => sc_language_render('filter_sort.created_desc'),
            SC_DB_PREFIX . 'admin_notification.created_at__asc' => sc_language_render('filter_sort.created_asc'),
        ];
        $order_date_from = sc_clean(request('order_date_from') ?? '');
        $order_date_to = sc_clean(request('order_date_to') ?? '');
        // search
        $obj = new AdminNotification();
        $dataTmp = $obj;
        if ($keyword) {
            $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
                $sql->where(SC_DB_PREFIX . 'admin_notification.title', 'like', '%' . $keyword . '%')
                    ->orwhere(SC_DB_PREFIX . 'admin_notification.order_code', 'like', '%' . $keyword . '%')
                    ->orwhere(SC_DB_PREFIX . 'admin_notification.customer_name', 'like', '%' . $keyword . '%')
                    ->orwhere(SC_DB_PREFIX . 'admin_notification.content', 'like', '%' . $keyword . '%');
            });
        }
        if ((string)$title) {
            $dataTmp = $dataTmp
                ->where('title', (string)$title);
        }
        if ($order_date_from) {
            $order_date_from = convertStandardDate($order_date_from)->toDateTimeString();
            $dataTmp = $dataTmp->where('created_at', '>=', $order_date_from);
        }
        if ($order_date_to) {
            $order_date_to = convertStandardDate($order_date_to)->toDateTimeString();
            $dataTmp = $dataTmp->where('created_at', '<=', $order_date_to);
        }
        $dataTmp = $dataTmp->where('is_admin', 0);
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $dataTmp = $dataTmp->orderBy($field, $sort_field);
        } else {
            $dataTmp = $dataTmp->orderBy('id', 'desc');
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.medium'));

        // ==search
        $dataTr = [];
        $content = '';
        foreach ($dataTmp as $key => $row) {
            if ($row->edit_type == 5) {
                $content = $row->desc;
            } else {
                $content = $row->content;
            }
            $dataTr[$row['id']] = [
                'created_at' => $row['created_at'] ? $row['created_at']->format('d/m/Y - H:i:s') : '',
                'title' => '<a title="Xem chi tiết" href="' . Route('admin_notify_history.detail', ['id' => $row['id']]) . '" >' . $row['title'] . '</a>',
                'order_id' => $row->order_code,
                'customer' => $row->customer_name,
                'content' => '<span style="color: ' . (($row->type_user == 1) ? '' : '#3C8DBC') .'">' . $content . '</span>',
                'action' => '<span onclick="deleteItem(\'' . $row['id'] . '\');"
                    title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>',
            ];
        }
        $objNotifySend = AdminNotification::where('is_admin', 0)->get();
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $dataTmpTitles = $objNotifySend->groupBy('title')->keys();
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');;
        $data['result_items'] = 'Có tất cả <b id="all">' . $dataTmp->total() . '</b> thông báo được gửi đi';
        //menu_right
        $data['menuRight'][] = '
            <button type="button" class="btn  btn-success  btn-flat" title="Xuất excel" id="btn_export">
                <i class="fa fa-file-export" title="' . sc_language_render('category-export') . '"></i> ' . sc_language_render('category-export') .
            '</button>';
        //=menu_right
        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['optionSort'] = $optionSort;
        //=menuSort
        $data['menu_sort'] = '
            <div class="btn-group pull-left">
                <div class="form-group">
                    <select class="form-control" id="order_sort">
                    ' . $optionSort . '
                    </select>
                </div>
            </div>

            <div class="btn-group pull-left">
                <a class="btn btn-flat btn-primary" id="button_sort">
                  <i class="fa fa-sort-amount-asc"></i><span class="hidden-xs"> ' . sc_language_render('admin.sort') . '</span>
                </a>
            </div>';

        $data['script_sort'] = "$('#button_sort').click(function(event) {
                                  var url = '" . route('admin_notify_history.index') . "?sort_order='+$('#order_sort option:selected').val();
                                  $.pjax({url: url, container: '#pjax-container'})
                                });";

        //=menu_sort

        //menu_search
        $optionStatus = '';
        foreach ($dataTmpTitles as $status) {
            $optionStatus .= '<option  ' . (($title == $status) ? "selected" : "") . ' value="' . $status . '">' . $status . '</option>';
        }

        $data['topMenuRight'][] = '
        <form action="' . sc_route_admin('admin_notify_history.index') . '" id="button_search">
            <div class="input-group input-group">
                <div class="input-group float-left" style="margin-left: 50px">
                        <div class="row">
                            <div style="width: 200px; margin: 0px 5px;">
                                <label>Danh mục:</label>
                                <div class="form-group">
                                    <div class="input-group">
                                        <select class="form-control rounded-0 select2" name="title_name" id="title_name">
                                            <option value="">' . sc_language_render('admin.notify.receiver') . '</option>
                                            ' . $optionStatus . '
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 390px; margin: 0px 5px">
                                <div class="row">
                                    <div style="width: 175px; margin: 0px 5px">
                                        <label>Ngày gửi:</label>
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_date_from" id="order_date_from" class="form-control input-sm datepicker rounded-0" style="text-align: center" autocomplete="off" placeholder="" value="' . request('order_date_from') . '" /> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group-addon" style="color: #93A7C1; margin-top: 32px">Đến</div>
                                    <div style="width: 175px; margin: 0px 5px; margin-top: 32px;">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <input type="text" name="order_date_to" id="order_date_to" class="form-control input-sm datepicker rounded-0" style="text-align: center"  placeholder="" autocomplete="off" value="' . request('order_date_to') . '"  /> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div style="width: 340px; margin-top: 32px; padding-right: 42px;">
                                <div class="form-group">
                                    <div class="input-group">
                                    <input type="text" id="keyword" name="keyword" class="form-control rounded-0 float-right" placeholder="' . sc_language_render('admin.notify.search') . '" value="' . $keyword . '">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary  btn-flat btn-search" id="submit_search"><i class="fas fa-search"></i></button>
                                        </div>
                                    </div> 
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.notification.history.index')->with($data);
    }

    /**
     * Xuất file excel thông báo
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportNotification()
    {
        $keyword = sc_clean(request('keyword') ?? '');
        $order_date_from = sc_clean(request('from_to') ?? '');
        $order_date_to = sc_clean(request('end_to') ?? '');
        $title = sc_clean(request('title') ?? '');

        $date = [];
        $ids = explode(',', request('ids'));
        if ($ids) {
            $dataTmp = AdminNotification::findMany($ids);
        } else {
            $dataTmp = new AdminNotification();
            if ($keyword) {
                $dataTmp = $dataTmp->where(function ($sql) use ($keyword) {
                    $sql->where(SC_DB_PREFIX . 'admin_notification.title', 'like', '%' . $keyword . '%')
                        ->orwhere(SC_DB_PREFIX . 'admin_notification.order_code', 'like', '%' . $keyword . '%')
                        ->orwhere(SC_DB_PREFIX . 'admin_notification.customer_name', 'like', '%' . $keyword . '%')
                        ->orwhere(SC_DB_PREFIX . 'admin_notification.content', 'like', '%' . $keyword . '%');
                });
            }
            if ((string)$title) {
                $dataTmp = $dataTmp
                    ->where('title', (string)$title);
            }
            if ($order_date_from) {
                $date['start'] = Carbon::createFromFormat('d/m/Y H:i:s', $order_date_from)->format('d/m/Y');
                $order_date_from = convertStandardDate($order_date_from)->toDateTimeString();
                $dataTmp = $dataTmp->where('created_at', '>=', $order_date_from);
            }
            if ($order_date_to) {
                $date['end'] = Carbon::createFromFormat('d/m/Y H:i:s', $order_date_to)->format('d/m/Y');
                $order_date_to = convertStandardDate($order_date_to)->toDateTimeString();
                $dataTmp = $dataTmp->where('created_at', '<=', $order_date_to);
            }

            $dataTmp = $dataTmp->where('is_admin', 0);
            $dataTmp = $dataTmp->get();
        }


        if (!count($dataTmp) > 0) {
            return redirect()->back()->with('error', 'Không có dữ liệu');
        }

        if (count($dataTmp) > 4000) {
            return redirect()->back()->with('error', 'Dữ liệu quá tải ko thể xuất excel. Vui lòng kiểm tra lại lọc tìm kiếm!');
        }

        $fileName = 'LichSuThongBao-.xlsx';

        return Excel::download(new AdminExportHistoryNotificationOrder($dataTmp, $date), $fileName);
    }

    /**
     * Xuất file excel thông báo
     * @return \Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportChangeImportPrice()
    {
        $id = sc_clean(request('id') ?? '');
        $notify = AdminNotification::find($id);
        if (!$notify) {
            return redirect()->back()->with(['error' => 'Lỗi xuất báo giá nhập']);
        }
        # assoc = true -> covert to array
        $data = json_decode($notify->content_change_import_price, true);
        return Excel::download(new ExportNotification($data), 'ThayDoiBaoGiaNhap.xlsx');
    }

    /**
     * Delete notification.
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrId = explode(',', $ids);
        $delete = AdminNotification::destroy($arrId);
        if ($delete == true) {
            AdminNotificationCustomer::where('notification_id', $arrId)->delete();
        }
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }

    /**
     * Tạo mới thông báo thủ công.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $listTemplates = DB::table(SC_DB_PREFIX . 'admin_notification_template')->get();
        $regions = (new ShopZone())->select(SC_DB_PREFIX . 'shop_zone.*')
            ->join(SC_DB_PREFIX . 'shop_customer', SC_DB_PREFIX . 'shop_customer.zone_id', SC_DB_PREFIX . 'shop_zone.id')
            ->groupBy(SC_DB_PREFIX . 'shop_customer.zone_id')
            ->orderBy(SC_DB_PREFIX . 'shop_zone.name', 'asc')->get();
        foreach ($listTemplates as $key => $template) {
            $templates[$key]['id'] = $template->id;
            $templates[$key]['title'] = $template->title;
            $templates[$key]['content'] = str_replace(array("\n", "\r"), '', $template->content);
        }
        return view($this->templatePathAdmin . 'screen.notification')->with(
            [
                'title' => 'Gửi thông báo thủ công',
                'sub_title' => 'Tạo vào gửi thông báo đến ứng dụng trên điện thoại',
                'icon' => 'fa fa-plus',
                'listTemplates' => $templates ?? [],
                'url_action' => route('admin_notify_manual.create'),
                'regions' => $regions,
            ]
        );
    }

    /**
     * Submit thông báo thủ công.
     * @param NotifyRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function doCreate(NotifyRequest $request)
    {
        $data = $request->validated();
        $region = $data['region'];
        $content = $data['editor'];
        $title = $data['title'];
        $customer = [];
        $link = '';
        if (isset($data['link'])) {
            $link = $data['link'];
        }
        $listCustomer = [];
        if (is_numeric($region)) {
            if (!$region) {
                $listCustomer = ShopCustomer::all();
            } else {
                $listCustomer = ShopCustomer::where('zone_id', $region)->get();
            }
        } else {
            $customer = $data['customer'];
            $listCustomer = [];
            foreach ($customer as $item) {
                $listCustomer[] = ShopCustomer::where('id', $item)->get();
            }
        }
        $icon = url('images/bq_notification.png');
        DB::beginTransaction();
        try {
            $dataInsert = AdminNotification::create([
                'title' => $title,
                'link' => $link,
                'created_at' => now(),
                'content' => $content,
                'icon' => $icon,
            ]);
            $output = [];
            $idCustomer = [];
            if ($customer) {
                foreach ($listCustomer as $key => $item) {
                    $output[] = [
                        'notification_id' => $dataInsert->id ?? '',
                        'customer_id' => $item[0]->id ?? '',
                        'created_at' => now(),
                    ];
                    $idCustomer[] = $item[0]->id ?? '';
                }
            } else {
                foreach ($listCustomer as $key => $item) {
                    $output[] = [
                        'notification_id' => $dataInsert->id ?? '',
                        'customer_id' => $item->id ?? '',
                        'created_at' => now(),
                    ];
                    $idCustomer[] = $item->id ?? '';
                }
            }
            $notify = new SendNotify($idCustomer, $content, $title);
            dispatch($notify);
            AdminNotificationCustomer::insert($output);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin_notify_history.index')->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin_notify_history.index')->with('success', 'Thêm thông báo thành công');
    }

    /**
     * bộ lọc.
     * @return \Illuminate\Http\JsonResponse
     */
    public function search()
    {
        $term = trim(request('q'));
        $tags = ShopCustomer::where('name', 'LIKE', '%' . $term . '%')->limit(10)->get();
        $formatted_tags = array();
        foreach ($tags as $tag) {
            $formatted_tags[] = array(
                'id' => $tag->id,
                'text' => $tag->name
            );
        }
        return response()->json($formatted_tags);
    }

    public function notifyTemplates()
    {
        $data = [
            // ,
            'title' => 'Danh sách mẫu thông báo',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => sc_route_admin('admin_notify_template.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'method' => 'delete',
            'css' => '',
            'js' => ''
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());
        $listTh = [
            'title' => sc_language_render('admin.news.title'),
            'content' => sc_language_render('admin.notification.content'),
//          'created_at' => sc_language_render('admin.notification.create'),
            'action' => sc_language_render('action.title'),
        ];
        $sort_order = request('sort_order') ?? 'id_desc';
        $keyword = sc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => sc_language_render('filter_sort.id_desc'),
            'id__asc' => sc_language_render('filter_sort.id_asc'),
            'title__desc' => sc_language_render('admin.notify.title_desc'),
            'title__asc' => sc_language_render('admin.notify.title_asc'),
            'created_at__desc' => sc_language_render('filter_sort.created_desc'),
            'created_at__asc' => sc_language_render('filter_sort.created_asc'),
        ];
        $cssTh = [
            'title' => "text-align: center; width: 30%",
            'content' => "text-align: center; width:35%",
            'created_at' => "text-align: center; width: 20%",
            'action' => "text-align: center; width: 10%",
        ];
        $cssTd = [
            'title' => "",
            'content' => "",
            'created_at' => "text-align: center",
            'action' => "text-align: center",
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        // search
        $dataTmp = new AdminNotificationTemplate;
        if ($keyword) {
            $dataTmp = $dataTmp
                ->where(SC_DB_PREFIX . 'admin_notification_template.title', 'like', '%' . $keyword . '%')
                ->orwhere(SC_DB_PREFIX . 'admin_notification_template.content', 'like', '%' . $keyword . '%');
        }
        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $dataTmp = $dataTmp->orderBy($field, $sort_field);
        } else {
            $dataTmp = $dataTmp->orderBy('id', 'desc');
        }
        $dataTmp = $dataTmp->paginate(config('pagination.admin.medium'));
        // ==search
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'title' => $row['title'],
                'content' => $row['content'],
                'created_at' => $row['created_at'] ? $row['created_at']->format('d/m/Y - H:i:s') : '',
                'action' => '<a href="' . sc_route_admin('admin_notify_template.edit', ['id' => $row['id']]) . '"><span title="' . sc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['resultItems'] = sc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' => $dataTmp->total()]);
        //menu_right
        $data['menuRight'][] = '<a href="' . sc_route_admin('admin_notify_template.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                                <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
                                </a>';
        //=menu_right
        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $data['urlSort'] = sc_route_admin('admin_notify_template.index', request()->except(['_token', '_pjax', 'sort_order']));
        $data['optionSort'] = $optionSort;
        //=menu_sort

        //menu_search
        $data['topMenuRight'][] = '
        <form action="' . sc_route_admin('admin_notify_template.index') . '" id="button_search">
            <div class="input-group input-group">
                <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Tên tiêu đề" value="' . $keyword . '">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>';
        //=menuSearch
        return view($this->templatePathAdmin . 'screen.list')->with($data);
    }

    public function createTemplates()
    {
        return view($this->templatePathAdmin . 'screen.notification_templates_add')->with([
            'title' => 'Tạo mẫu thông báo',
            'sub_title' => 'Tạo một mẫu thông báo mới',
            'icon' => 'fa fa-plus',
            'url_action' => route('admin_notify_template.create'),
        ]);
    }

    public function postCreateTemplates(AdminNotifyTemplatesRequest $request)
    {
        $data = $request->validated();
        $dataInsert = [
            'title' => $data['title'],
            'content' => $data['editor']
        ];
        try {
            AdminNotificationTemplate::create($dataInsert);
        } catch (Exception $e) {
            return redirect()->route('admin_notify_template.index')->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin_notify_template.index')->with('success', 'Thêm mẫu thông báo thành công');
    }

    public function editTemplates($id)
    {
        $objTemp = AdminNotificationTemplate::findOrFail($id);
        return view($this->templatePathAdmin . 'screen.notification_templates_edit')->with([
            'title' => 'Tạo mẫu thông báo',
            'sub_title' => 'Tạo một mẫu thông báo mới',
            'icon' => 'fa fa-plus',
            'objTemp' => $objTemp,
            'url_action' => route('admin_notify_template.edit', ['id' => $id]),
        ]);
    }

    public function postEditTemplates($id, AdminNotifyTemplatesRequest $request)
    {
        $data = $request->validated();
        $objTemp = AdminNotificationTemplate::findOrFail($id);
        $dataUpdate = [
            'title' => $data['title'],
            'content' => $data['editor']
        ];
        try {
            $tempUpdate = $objTemp->update($dataUpdate);
        } catch (Exception $e) {
            return redirect()->route('admin_notify_template.index')->with('error', sc_language_render('action.failed'));
        }
        return redirect()->route('admin_notify_template.index')->with('success', 'Chỉnh sửa thành công');
    }

    public function deleteListTemplates()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => 'Method not allow!']);
        }
        $ids = request('ids');
        $arrId = explode(',', $ids);
        AdminNotificationTemplate::destroy($arrId);
        return response()->json(['error' => 0, 'msg' => '']);
    }

    public function customerRead($id)
    {
        $data = [
            // ,
            'title' => 'Danh sách đã đọc thông báo',
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'removeList' => '', // 1 - Enable function delete list item
            'buttonSort' => '', // 1 - Enable button sort
            'css' => '',
            'js' => ''
        ];
        $listTh = [
            'customer' => sc_language_render('admin.order.customer_name'),
            'status' => sc_language_render('product.status'),
            'seen_time' => sc_language_render('notification.seen_time'),
        ];
        $cssTh = [
            'customer' => '',
            'status' => 'width:25% ; text-align:center;',
            'seen_time' => ' ',
        ];
        $cssTd = [
            'customer' => '',
            'status' => 'text-align:center;',
            'seen_time' => ' ',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $dataTmp = AdminNotificationCustomer::join(SC_DB_PREFIX . 'shop_customer', SC_DB_PREFIX . 'shop_customer.id', SC_DB_PREFIX . 'admin_notification_customer.customer_id')
            ->where(SC_DB_PREFIX . 'admin_notification_customer.notification_id', $id)
            ->select(SC_DB_PREFIX . 'admin_notification_customer.*', SC_DB_PREFIX . 'shop_customer.name as full_name')->paginate(config('pagination.admin.medium'));
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'customer' => $row['full_name'],
                'status' => $row['seen'] ? '<span class="badge badge-success">Đã xem</span>' : '',
                'updated_at' => $row['updated_at'] ? $row['updated_at']->format('d/m/Y - H:i:s') : '',
            ];
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['result_items'] = 'Có tất cả <b id="all">' . $dataTmp->total() . '</b> thông báo';
        //menu_right
        $data['menu_right'] = '';
        $data['urlSort'] = sc_route_admin('admin_notify_history.index', request()->except(['_token', '_pjax', 'sort_order']));

        return view($this->templatePathAdmin . 'screen.list_notify')->with($data);
    }

    public function detail($id)
    {
        $notify = AdminNotification::findOrFail($id);
        return view($this->templatePathAdmin . 'screen.notification.history.detail')->with(
            [
                'title' => 'Chi tiết thông báo',
                'icon' => 'fa fa-list',
                'notify' => $notify,
            ]
        );
    }

    public function readTick()
    {
        $id = request('id');
        $dataUpdate = [
            'seen' => 1,
        ];

        if ($id) {
            AdminNotification::whereIn('id', is_array($id) ? $id : [$id])->update($dataUpdate);
        } else {
            AdminNotification::query()->update($dataUpdate);
        }

        return response()->json(['data' => $id]);

    }

    public function getListPaging(Request $request)
    {
        $data = [];
        $notifications = AdminNotification::getAdminNotification()
            ->orderBy('created_at', 'DESC')->paginate(config('pagination.notification'));
        $unreadNotifcations = AdminNotification::getAdminNotification()->where(function ($query) {
            $query->whereNull('seen')
                ->orWhere('seen', '=', 0);
        })->get();
        $unread = count($unreadNotifcations);
        foreach ($notifications as $notification) {
            $data[] = [
                'id' => $notification->id,
                'title' => $notification->title ?? "",
                'content' => $notification->content ?? "",
                'link' => $notification->link ?? "",
                'icon' => $notification->icon ?? "",
                'seen' => $notification->seen,
                'created_at' => date_format($notification->created_at, 'd/m/Y H:i:s'),
            ];
        }
        $responseData = [
            'page' => $notifications->currentPage(),
            'more' => $notifications->hasMorePages(),
            'notifications' => $data,
            'unread' => $unread
        ];
        return response()->json($responseData);
    }
}
