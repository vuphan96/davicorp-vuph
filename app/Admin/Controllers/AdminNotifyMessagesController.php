<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use SCart\Core\Admin\Controllers\RootAdminController;
use App\Admin\Models\AdminNotification;
use App\Admin\Models\AdminNotifyMessage;
use DB;
use Request;
use App\Http\Requests\Admin\NotifyRequest;
use App\Http\Requests\Admin\AdminNotifyMessagesRequest;


class AdminNotifyMessagesController extends RootAdminController
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
            'urlDeleteItem' => sc_route_admin('admin_notify_automatic.delete'),
            'removeList' => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'buttonSort' => 1, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'method' => 'delete'
        ];
        //Process add content
        $data['menuRight'] = sc_config_group('menuRight', Request::route()->getName());
        $data['menuLeft'] = sc_config_group('menuLeft', Request::route()->getName());
        $data['topMenuRight'] = sc_config_group('topMenuRight', Request::route()->getName());
        $data['topMenuLeft'] = sc_config_group('topMenuLeft', Request::route()->getName());
        $data['blockBottom'] = sc_config_group('blockBottom', Request::route()->getName());
        $listTh = [
//          'ID' => 'ID',
          'title' => sc_language_render('admin.news.title'),
          'content' => sc_language_render('order.admin.history_content'),
          'timeschedule' => sc_language_render('admin.notify.send'),
          'action' => sc_language_render('action.title'),
        ];
        $cssTh = [
            'title' => 'width:28%',
            'content' => 'width:32% ',
            'timeschedule' => 'width:15% ; text-align:center',
            'action' => 'width:15% ; text-align:center;',
        ];
        $cssTd = [
            'title' => '',
            'content' => '',
            'timeschedule' => 'text-align:center',
            'action' => 'text-align:center',
        ];
        $data['cssTh'] = $cssTh;
        $data['cssTd'] = $cssTd;
        $sort_order = request('sort_order') ?? 'id_desc';
        $keyword = sc_clean(request('keyword') ?? '');
        $arrSort = [
          'id__desc' => sc_language_render('filter_sort.id_desc'),
          'id__asc' => sc_language_render('filter_sort.id_asc'),
          'title_description__desc' => sc_language_render('admin.notify.title_desc'),
          'title_description__asc' => sc_language_render('admin.notify.title_asc'),
        ];
        // search
        $dataTmp = new AdminNotifyMessage();
        if ($keyword) {
            $dataTmp = $dataTmp
              ->where('title', 'like', '%' . $keyword . '%')
              ->orwhere('content', 'like', '%' . $keyword . '%');
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
//                'ID' => $row['id'],
                'title' => $row['title_description'],
                'content'    => $row['content'],
                'timeschedule' => $row['time'],
                'action' =>'<a href="' . route('admin_notify_automatic.detail', ['id' => $row['id']]) . '"><span title="Xem chi tiết" type="button" class="btn btn-flat btn-primary" style="width: 30.25px; height: 31px; padding: 0.2rem 0.75rem;"><i class="fa fa-info" style="margin-bottom: 5px"></i></span></a>&nbsp;
                <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . sc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin . 'component.pagination');
        $data['result_items'] = 'Có tất cả <b id="all">'. $dataTmp->total().'</b> thông báo';
        //menu_right
        $data['menuRight'][] = '<a href="' . sc_route_admin('admin_notify_automatic.create') . '" class="btn  btn-success  btn-flat" title="New" id="button_create_new">
                                <i class="fa fa-plus" title="' . sc_language_render('admin.add_new') . '"></i>
                                </a>';
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
                                  var url = '" . route('admin_notify_automatic.index') . "?sort_order='+$('#order_sort option:selected').val();
                                  $.pjax({url: url, container: '#pjax-container'})
                                });";
        $data['topMenuRight'][] = '
        <form action="' . sc_route_admin('admin_notify_automatic.index') . '" id="button_search">
        <div class="input-group input-group">
            <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="Tiêu đề/ nội dung" value="' . $keyword . '">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
            </div>
        </div>
        </form>';
        // =menuSearch
        return view($this->templatePathAdmin . 'screen.list_notify')->with($data);
    }
    public function detail($id){
        $notifyMessages = AdminNotifyMessage::findOrFail($id);
        return view($this->templatePathAdmin .'screen.notification_messages_detail')->with(
            [
          'title' => sc_language_render('admin.notification.auto.title'),
          'icon' => 'fa fa-list',
          'notifyMessages' => $notifyMessages,
          'url_action' => route('admin_notify_automatic.detail',['id'=>$id]),
        ]
        );
    }
    public function updatedetail($id, AdminNotifyMessagesRequest $request)
    {
      $data = $request->validated();
      $content = AdminNotifyMessage::htmlToPlainText($data['editor']);
      $notifyMessages = AdminNotifyMessage::findOrFail($id);
      try{
        $result = $notifyMessages->update([
            'content' => $content, 
            'title_description' => $data['description'], 
            'time' => $data['schedule']
        ]);
      } catch(Exception $e) {
        return redirect()->route('admin_notify_automatic.index')->with('error', sc_language_render('action.failed'));
      }
      return redirect()->route('admin_notify_automatic.index')->with('success', 'Chỉnh sửa thông báo thành công');
    }
    public function createMessages(){
        return view($this->templatePathAdmin .'screen.notification_messages_create')->with(
            [
          'title' => 'Thêm thông báo tự đông',
          'icon' => 'fa fa-list',
          'url_action' => route('admin_notify_automatic.create'),
        ]
        );
    }
    public function postcreateMessages( AdminNotifyMessagesRequest $request)
    {
      $data = $request->validated();
      $content = AdminNotifyMessage::htmlToPlainText($data['editor']);
      try{
        $result = AdminNotifyMessage::create([
            'title' => $data['code'],
            'content' => $content, 
            'title_description' => $data['description'], 
            'time' => $data['schedule']
        ]);
      } catch(Exception $e) {
        return redirect()->route('admin_notify_automatic.index')->with('error', sc_language_render('action.failed'));
      }
      return redirect()->route('admin_notify_automatic.index')->with('success', 'Thêm thông báo thành công');
    }

    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => sc_language_render('admin.method_not_allow')]);
        }
        $ids = request('ids');
        $arrID = explode(',', $ids);
        AdminNotifyMessage::destroy($arrID);
        return response()->json(['error' => 0, 'msg' => sc_language_render('action.delete_success')]);
    }
}