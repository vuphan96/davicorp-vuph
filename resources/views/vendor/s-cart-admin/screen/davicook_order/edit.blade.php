@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">{{ sc_language_render('order.order_detail') }}
                        #{{ $order->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ session('nameUrlOrderDavicook') ?? sc_route_admin('admin.davicook_order.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a data-perm="davicook_order:return" href="{{ sc_route_admin('admin.davicook_order.return', ['id' => $order->id]) }}"
                               class="btn btn-flat btn btn-primary"
                                    {{ !$editable ? 'order-lock=hide' : "" }}><i
                                        class="fa fa-undo"></i>&nbsp;{{ sc_language_render('order.return') }}</a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a data-perm="davicook_order:print" href="#" class="btn btn-flat btn btn-primary" data-toggle="modal"
                               data-target="#printDialog"><i
                                        class="fa fa-print"></i>&nbsp;{{ sc_language_render('admin.order.print_invoice') }}
                            </a>
                        </div>
                        <div style="margin-right: 10px" type="button" class="btn-group float-right" data-toggle="modal"
                             data-target="#exampleModal">
                            <a data-perm="davicook_order:update_price" href="#" {{ !$editable ? 'order-lock=hide' : "" }} class="btn btn-flat btn btn-primary"><i
                                        class="fas fa-pen"></i>&nbsp;Cập nhật giá
                            </a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="#" class="btn btn-flat btn btn-primary" onclick="location.reload()"><i
                                        class="fa fa-sync-alt"></i></a>
                        </div>
                    </div>
                </div>

                <form class="row" id="order_edit_form form-main" method="post"
                      action="{{ sc_route_admin('admin.davicook_order.order_update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table box-body text-wrap table-bordered">
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_name') }}:</td>
                                <td>
                                    {{ $order->customer_name }}
                                    {{--                                    <select class="form-control customer_id select2" style="width: 100%;" name="customer_id">--}}
                                    {{--                                        <option value="{{ $order->customer_id }}">{{ $order->customer_name ?? '' }}</option>--}}
                                    {{--                                        @foreach ($customers as $key => $value)--}}
                                    {{--                                            <option value="{{ $value->id }}">{{ $value->name }}</option>--}}
                                    {{--                                        @endforeach--}}
                                    {{--                                    </select>--}}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_address') }}:</td>
                                <td>
                                    <input id="address" name="address" value="{!! $order->address !!}" class="form-control address" readonly/>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.phone') }}:</td>
                                <td>
                                    <input id="phone" name="phone" value="{!! $order->phone !!}" class="form-control phone" readonly/>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.email') }}:</td>
                                <td>
                                    <input type="email" id="email" name="email" value="{!! $order->email !!}" class="form-control email" readonly/>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Số lượng suất ăn chính:</td>
                                <td>
                                    <a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateNumberOfServings" id="number-of-servings" data-name="number_of_servings" data-type="number" data-min="0"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}" data-emptytext="Trống"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Số lượng suất ăn">{!! $order->number_of_servings !!}</a>
                                </td>
                            </tr>

                            <tr>
                                <td class="td-title">Số lượng suất ăn bổ sung:</td>
                                <td>
                                    <a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateNumberOfExtraServings" id="number-of-extra-servings" data-name="number_of_extra_servings" data-type="number" data-min="0"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}" data-emptytext="Trống"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Số lượng suất ăn">{!! $order->number_of_extra_servings !!}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Số lượng suất ăn thực tế:</td>
                                <td>
                                    <a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateNumberOfServings" id="number-of-servings-reality" data-name="number_of_reality_servings" data-type="number" data-min="0"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}" data-emptytext="Trống"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Số lượng suất ăn bổ sung">{!! $order->number_of_reality_servings !!}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.explain') }}</td>
                                <td>
                                    <a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateInfoRequired" data-name="explain" data-type="select"
                                       data-source="{{ json_encode($orderNote) }}" data-value="{!! $order->explain !!}" data-emptytext="Trống"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="{{ sc_language_render('admin.order.explain') }}">{!! $order->explain !!}</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">{{ sc_language_render('order.order_status') }}:</td>
                                <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateStatus" data-name="status" data-type="select"
                                       data-source="{{ json_encode($statusOrder) }}" data-pk="{{ $order->id }}"
                                       data-value="{!! $order->status !!}"
                                       data-url="{{ route("admin.davicook_order.order_update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="{{ sc_language_render('order.order_status') }}">{{ $statusOrder[$order->status] ?? $order->status }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> {{ sc_language_render('admin.order.created_at') }}</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($order->created_at ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> {{ sc_language_render('admin.delivery_time') }}</td>
                                <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateDeliveryTime" data-name="delivery_date" data-type="date"
                                       data-value="{!! $order->delivery_date !!}"
                                       data-emptytext="Trống"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="{{ sc_language_render('admin.delivery_time') }}">{{ date('d/m/Y', strtotime($order->delivery_date ?? '')) }}</a>
                                </td>
                                {{-- <td>{{ date('d/m/Y', strtotime($order->delivery_date ?? '')) }}</td> --}}
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày trên hóa đơn:</td>
                                <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="updateBillDate" data-name="bill_date" data-type="date"
                                       data-value="{!! $order->bill_date !!}"
                                       data-emptytext="Trống"
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Ngày trên hóa đơn:">{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</a>
                                </td>
                                {{-- <td>{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</td> --}}
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày xuất kho hàng khô:</td>
                                <td>
                                    {{ !empty($order->export_date) ? date('d/m/Y', strtotime($order->export_date)) : '' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày thao tác xuất kho hàng khô:</td>
                                <td>
                                    {{ !empty($order->export_operation_date) ? date('d/m/Y H:i:s', strtotime($order->export_operation_date ?? '')) : '' }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>


                <label style="font-size: 20px; margin-left: 10px; margin-top: 30px; color: blue">
                    ĐƠN CHÍNH
                </label>
                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="cId" value="{{ $order->customer_id }}">
                    <input type="hidden" name="return-order-check" value ="{{ $checkHasOrderReturn }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table class="table box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th style="text-align: center; width: auto" class="dish_code">Mã món ăn</th>
                                            <th style="text-align: center; min-width: 165px" class="dish_name">Tên món ăn</th>
                                            <th style="text-align: center; min-width: 165px" class="product_name">Tên nguyên liệu</th>
                                            <th style="text-align: left; min-width: 165px" class="bom">Định lượng</th>
                                            <th style="text-align: left; min-width: 100px" class="qty">Số lượng</th>
                                            <th style="text-align: center; min-width: 140px" class="total_bom">Nguyên liệu suất</th>
                                            <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                            <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền Cost</th>
                                            <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                            <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dish_order as $k => $v)
                                            @php
                                                $count = 1;
                                                $count_product = 0;
                                            @endphp
                                            @foreach ($order->details->where('type',0) as $value)
                                                @php
                                                    if ($value->dish_id === $v->dish_id) { $count_product++; }
                                                @endphp
                                            @endforeach
                                            <tr>
                                                <td rowspan="{{$count_product}}">{{ $orderDishCode[$v->dish_id] ?? '' }}</td>
                                                <td rowspan="{{$count_product}}">{{ $v->dish_name ?? 'Món ăn đã bị xóa' }}</td>

                                                @foreach ($order->details->where('type',0) as $item)
                                                    @if ($item->dish_id == $v->dish_id && $count > 0)
                                                        @php
                                                            $count = 0;
                                                            $current_detail_id = $item->id;
                                                        @endphp
                                                        <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }};
                                                                text-underline-offset: 5px;
                                                                text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                            {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                        </td>
                                                        <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-bom"
                                                               data-value="{{ $item->bom }}" data-name="bom"
                                                               data-step="any"
                                                               data-type="text" data-min="0"
                                                               data-pk="{{ $item->id }}"
                                                               data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                               data-title="Định lượng">{{$item->bom}}</a> {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td>{{$item->qty}}</td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}">
                                                            {!! (checkRoundedIntTotalBom($item->qty*$item->bom, ($item->product->unit->type ?? 0)))
                                                                ?
                                                                '<b>'.number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', '').'</b>'
                                                                :
                                                                number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', '');
                                                            !!} {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-comment"
                                                               data-value="{{ $item->comment ?? ''}}"
                                                               data-name="comment" data-type="text"
                                                               data-pk="{{ $item->id ?? ''}}"
                                                               data-emptytext="Trống"
                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                               data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                               data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                        </td>
                                                    @endif
                                                @endforeach
                                                <td rowspan="{{$count_product}}" style="text-align: center">
                                                        <span data-perm="davicook_order:edit_info" onclick="deleteItem('{{ $v->dish_id }}',0);"
                                                              class="btn btn-danger btn-sm btn-flat" data-title="Delete"
                                                            {{ !$editable ? 'order-lock=hide' : "" }}>
                                                              <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </span>
                                                </td>
                                            </tr>
                                            @foreach ($order->details->where('type',0) as $item)
                                                @if ($item->dish_id == $v->dish_id &&  $current_detail_id !== $item->id)
                                                    <tr>
                                                        <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }};
                                                                text-underline-offset: 5px;
                                                                text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                            {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                        </td>
                                                        <td><a  data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-bom"
                                                                data-value="{{ $item->bom }}" data-name="bom"
                                                                data-step="any"
                                                                data-type="number" data-min="0"
                                                                data-pk="{{ $item->id }}"
                                                                {{ !$editable ? 'order-lock=disable' : "" }}
                                                                data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                                data-title="Định lượng">{{$item->bom}}</a> {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td>{{$item->qty}}</td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}" {{ !$editable ? 'order-lock=disable' : "" }}>
                                                            {!! (checkRoundedIntTotalBom($item->qty*$item->bom, ($item->product->unit->type ?? 0)))
                                                                ?
                                                                '<b>'.number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', '').'</b>'
                                                                :
                                                                number_format(roundTotalBom($item->qty*$item->bom, $item->product->unit->type ?? 0), 2, '.', '');
                                                            !!} {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-comment"
                                                               data-value="{{ $item->comment ?? ''}}"
                                                               data-name="comment" data-type="text"
                                                               data-emptytext="Trống"
                                                               data-pk="{{ $item->id ?? ''}}"
                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                               data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                               data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <tr id="add-item" class="not-print">
                                            <td colspan="10">
                                                <button
                                                        data-perm="davicook_order:edit_info" type="button" class="btn btn-flat btn-success"
                                                        id="add-item-button"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}
                                                        title="{{sc_language_render('action.add') }}"><i
                                                            class="fa fa-plus"></i> Thêm món
                                                </button>
                                                &nbsp;&nbsp;&nbsp;
                                                <button
                                                        style="display: none; margin-right: 50px"
                                                        type="button" class="btn btn-flat btn-warning"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}
                                                        id="add-item-button-save" title="Save"><i
                                                            class="fa fa-save"></i> Lưu lại
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>


                <label style="font-size: 20px; color: blue; margin-left: 10px; margin-top: 30px;">
                    ĐƠN BỔ SUNG
                </label>
                <form id="form-add-item-extra-order" action="" method="">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                    <input type="hidden" name="return-order-check" value ="{{ $checkHasOrderReturn }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table class="table box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th style="text-align: center; width: auto" class="dish_code">Mã món ăn</th>
                                            <th style="text-align: center; min-width: 165px" class="dish_name">Tên món ăn</th>
                                            <th style="text-align: center; min-width: 230px" class="product_name">Tên nguyên liệu</th>
                                            <th style="text-align: center; min-width: 140px" class="total_bom">Nguyên liệu suất</th>
                                            <th style="text-align: center; min-width: 80px" class="product_unit">Đơn vị</th>
                                            <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                            <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền Cost</th>
                                            <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                            <th style="text-align: center; width: auto" class="comment">Xóa NL</th>
                                            <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($dish_extra_order as $k => $v)
                                            @php
                                                $count = 1;
                                                $count_product = 0;
                                            @endphp
                                            @foreach ($order->details->where('type',1) as $value)
                                                @php
                                                    if ($value->dish_id == $v->dish_id) {
                                                        $count_product++;
                                                    }
                                                @endphp
                                            @endforeach
                                            <tr>
                                                <td rowspan="{{$count_product}}">{{ $orderDishCode[$v->dish_id] ?? '' }}</td>
                                                <td rowspan="{{$count_product}}">{{ $v->dish_name ?? 'Món ăn đã bị xóa' }}</td>

                                                @foreach ($order->details->where('type',1) as $item)
                                                    @if ($item->dish_id == $v->dish_id && $count > 0)
                                                        @php
                                                            $count = 0;
                                                            $current_detail_id = $item->id;
                                                        @endphp
                                                        <td style="color: {{ (($item->product_priority_level ?? 0) == 1) ? 'red' : '' }};
                                                                text-underline-offset: 5px;
                                                                text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                            {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                        </td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}">
                                                            <a  data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-total-bom"
                                                                data-value="{{ $item->total_bom ?? ''}}"
                                                                data-name="total_bom" data-type="text"
                                                                data-pk="{{ $item->id ?? ''}}"
                                                                {{ !$editable ? 'order-lock=disable' : "" }}
                                                                data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                                data-title="Nguyên liệu suất"
                                                            >
                                                                {{ roundTotalBom($item->total_bom, $item->product->unit->type ?? 0) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $item->product_unit ?? ''}}</td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-comment"
                                                               data-value="{{ $item->comment ?? ''}}"
                                                               data-name="comment" data-type="text"
                                                               data-pk="{{ $item->id ?? ''}}"
                                                               data-emptytext="Trống"
                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                               data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                               data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                        </td>
                                                    @endif
                                                @endforeach
                                                <td rowspan="{{$count_product}}"></td>
                                                <td rowspan="{{$count_product}}" style="text-align: center">
                                                        <span  data-perm="davicook_order:edit_info" onclick="deleteItem('{{ $v->dish_id }}',1);"
                                                               class="btn btn-danger btn-sm btn-flat" data-title="Delete"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}><i
                                                                    class="fa fa-trash" aria-hidden="true"></i></span>
                                                </td>
                                            </tr>
                                            @foreach ($order->details->where('type',1) as $item)
                                                @if ($item->dish_id == $v->dish_id &&  $current_detail_id !== $item->id)
                                                    <tr>
                                                        <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }};
                                                                text-underline-offset: 5px;
                                                                text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                            {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                        </td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}">
                                                            <a  data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-total-bom"
                                                                data-value="{{ $item->total_bom ?? ''}}"
                                                                data-name="total_bom" data-type="text"
                                                                data-pk="{{ $item->id ?? ''}}"
                                                                data-emptytext="Trống"
                                                                data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                                data-title="Nguyên liệu suất">
                                                                {{ roundTotalBom($item->total_bom, $item->product->unit->type ?? 0) }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $item->product_unit ?? ''}}</td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td><a data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-comment"
                                                               data-value="{{ $item->comment ?? ''}}"
                                                               data-name="comment" data-type="text"
                                                               data-emptytext="Trống"
                                                               data-pk="{{ $item->id ?? ''}}"
                                                               data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                               data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                        <tr id="add-item-extra-order" class="not-print">
                                            <td colspan="10">
                                                <button
                                                        data-perm="davicook_order:edit_info" type="button" class="btn btn-flat btn-success"
                                                        id="add-item-button-extra-order"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}
                                                        title="{{sc_language_render('action.add') }}">
                                                    <i class="fa fa-plus"></i> Thêm món
                                                </button>
                                                &nbsp;&nbsp;&nbsp;
                                                <button style="display: none; margin-right: 50px"
                                                        type="button" class="btn btn-flat btn-warning"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}
                                                        id="add-item-button-save-extra-order" title="Save">
                                                    <i class="fa fa-save"></i> Lưu lại
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

                {{-- Order return --}}
                <div class="row">
                    <div class="col-sm-12 mt-3">
                        <label style="font-size: 20px; color: blue; margin-left: 10px; margin-top: 25px;">
                            ĐƠN HÀNG TRẢ
                        </label>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="min-width: 55px; max-width: 55px">{{ sc_language_render('order.admin.no') }}</th>
                                    <th style="">{{ sc_language_render('order.admin.employe') }}</th>
                                    <th style="max-width: 120px">Loại</th>
                                    <th style="min-width: 200px">{{ sc_language_render('product.name') }}</th>
                                    <th class="product_qty">{{ sc_language_render('admin.order.qty') }}</th>
                                    <th class="product_qty">{{ sc_language_render('order.admin.return_no') }}</th>
                                    <th class="product_price">{{ sc_language_render('admin.davicook.name_price') ?? "Giá nhập" }}</th>
                                    <th class="product_total">{{ sc_language_render('product.total_price') }}</th>
                                    <th class="product_comment">{{ sc_language_render('order.admin.return_date') }}</th>
                                    <th class="undo_detail"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($returnOrderHistory as $k => $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="width: 120px">{{ $item->getEditor() }}</td>
                                        <td>{{ $item->type == 1 ? 'Đơn Bổ Sung' : 'Đơn chính' }}</td>
                                        <td class="product_name_return">{{ $item->dish_name ?? '' }} - {{ $item->product_name ?? '' }}</td>
                                        <td class="product_qty">{{ $item->original_qty ?? 0 }}</td>
                                        <td class="product_price">{{ $item->return_qty ?? 0 }}</td>
                                        <td class="product_price">{{ sc_currency_render($item->import_price ?? 0, $order->currency ?? 'VND') }}</td>
                                        <td class="product_price">{{ sc_currency_render($item->return_total ?? 0, $order->currency ?? 'VND') }}</td>
                                        <td class="product_comment">{{ formatDateVn($item->created_at, true) ?? 'Trống' }}</td>
                                        <td class="undo_detail"><span onclick="undoReturnOrder('{{$item->id}}', '{{$item->detail_id}}')" class="btn btn-sm btn-sm btn-warning">
                                                <i class="fas fa-undo text-white"></i></span></td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                {{-- /Order return --}}

                <div class="row">
                    {{-- Comment --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="td-title">{{ sc_language_render('order.order_note') }}:</td>
                                    <td class="order-comment">
                                        <a  data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-order-comment"
                                            data-value="{{ $order->comment ?? ''}}"
                                            data-name="comment"
                                            data-type="textarea"
                                            data-emptytext="Trống"
                                            data-pk="{{ $order->id }}"
                                            data-url="{{ route("admin.davicook_order.order_update") }}"
                                            data-title="{{ sc_language_render('order.admin.comment') }}"> {{ $order->comment }}</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End comment --}}
                    {{-- total --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-borderless table-active">
                                <tr>
                                    <td class="td-title">Tổng tiền Cost:</td>
                                    <td class="text-right td-title data-total-cost">{{ sc_currency_render(round($total_cost) ?? 0, 'vnd') }}</td>
                                </tr>
                                <tr>
                                    <td class="td-title">Tổng tiền hàng trả: </td>
                                    <td class="text-right td-title">{{ sc_currency_render(round($total_return_order) ?? 0, 'vnd') }}</td>
                                </tr>
                                <tr>
                                    <td class="td-title">Tổng tiền giá vốn: </td>
                                    <td class="text-right td-title data-total">{{ sc_currency_render(round($total_cost-$total_return_order) ?? 0, 'vnd') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End total --}}
                </div>
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="3">Lịch sử đơn hàng</th>
                                </tr>
                                <tr>
                                    <td>{{ sc_language_render('admin.order_history.time') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.actor') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.action') }}</td>
                                </tr>
                                </thead>
                                <tbody id="order-history">
                                @forelse($order->history ?? [] as $k => $v)
                                    <tr>
                                        <td>{{ $v->add_date }}</td>
                                        <td>{{ $v->getEditor()}}</td>
                                        <td>{!! $v->content !!}</td>
                                    </tr>
                                @empty

                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- //End history --}}
                </div>

                @php
                    $htmlSelectDish = '
                            <tr>
                                <td style="width:80px"><input type="text" disabled class="add_dish_code form-control"></td>
                                <td id="add_td">
                                  <select onChange="selectDish($(this));"  class="add_dish_id form-control select2" name="add_dish_id[]" style="width:100% !important;">';
                                    if (count($dishs)>0) {
                                        $htmlSelectDish .='<option selected disabled hidden value="">Chọn món ăn</option>';
                                        foreach ($dishs as $dish) {
                                            if (isset($orderDishName[$dish->dish_id]) && ($dish->dish->status == 1)) {
                                                $htmlSelectDish .='<option  value="'.$dish->dish_id.'" >'.$orderDishName[$dish->dish_id].'</option>';
                                            }
                                        }
                                    } else {
                                        $htmlSelectDish .='<option selected disabled hidden value="">Chưa có món ăn</option>';
                                    }
                    $htmlSelectDish .='
                                    </select>
                                </td>
                                <td class="add_rowspan" style="text-align:center"><button id="select_dish_button" type="button" onclick="$(this).parent().parent().remove(); checkRemoveDOM()" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                            ';
                    $htmlSelectDish = str_replace("\n", '', $htmlSelectDish);
                    $htmlSelectDish = str_replace("\t", '', $htmlSelectDish);
                    $htmlSelectDish = str_replace("\r", '', $htmlSelectDish);
                    $htmlSelectDish = str_replace("'", '"', $htmlSelectDish);
                @endphp

                @php
                    $htmlSelectDishExtraOrder = '
                            <tr class="select-dish">
                                <td style="width:80px">
                                    <input type="text" disabled id="dish_code" class="add_dish_code_extra_order form-control">
                                    <input type="hidden" name="add_number_product_extra_order[]" class="add_number_product_extra_order" value="0">
                                </td>
                                <td id="add_td_extra_order">
                                    <select onChange="selectDishExtraOrder($(this));" class="add_dish_id_extra_order form-control select2" name="add_dish_id_extra_order[]" style="width:100% !important;">';
                                    if (count($dishs_extra_order)>0) {
                                        $htmlSelectDishExtraOrder .=' <option selected disabled hidden value="">Chọn món ăn</option>';
                                        foreach ($dishs_extra_order as  $dish) {
                                            if (isset($orderDishName[$dish->dish_id]) && ($dishStatus = $dish->dish->status ?? 0) == 1) {
                                                $htmlSelectDishExtraOrder .='<option value="'.$dish->dish_id.'" >'.$orderDishName[$dish->dish_id].'</option>';
                                            }
                                        }
                                    } else {
                                        $htmlSelectDishExtraOrder .='<option selected disabled hidden value="">Chưa có món ăn</option>';
                                    }
                    $htmlSelectDishExtraOrder .='
                                    </select>
                                </td>
                                <td class="add_rowspan" style="text-align:center"><button id="select_dish_button" type="button" onclick="$(this).parent().parent().remove(); checkRemoveDOM();" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                            </tr>
                            ';
                    $htmlSelectDishExtraOrder = str_replace("\n", '', $htmlSelectDishExtraOrder);
                    $htmlSelectDishExtraOrder = str_replace("\t", '', $htmlSelectDishExtraOrder);
                    $htmlSelectDishExtraOrder = str_replace("\r", '', $htmlSelectDishExtraOrder);
                    $htmlSelectDishExtraOrder = str_replace("'", '"', $htmlSelectDishExtraOrder);
                @endphp

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form onsubmit="return validateSubmitPrint(this);" class="modal-content" id="printForm" method="get" target="_blank"
                  action="{{ sc_route_admin('admin.davicook_order.print_multiple') }}">
                @csrf
                <input type="hidden" name="ids" value="{{ $order->id }}">
                <input type="hidden" name="order_status" value="{{ $order->status }}">
                <input type="hidden" name="type_export" id="type_export" value="">
                <input type="hidden" name="detail" id="detail" value="detail">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i
                                class="fas fa-print"></i>&nbsp;{{sc_language_render('order.print.title')}}</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-2">Thông tin in</label>
                        <div class="col-10">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="type_print_status" id="info_1" type="checkbox" class="custom-control-input radio" checked value="1">
                                <label for="info_1" class="custom-control-label">Suất ăn</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="type_print_status" id="info_2" type="checkbox" class="custom-control-input radio" value="2">
                                <label for="info_2" class="custom-control-label">Hàng tươi sống</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="type_print_status" id="info_3" type="checkbox" class="custom-control-input radio" value="3">
                                <label for="info_3" class="custom-control-label">Hàng khô</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                                class="fa fa-undo"></i> {{sc_language_render('action.discard')}}</button>
                    <button type="button" id="btnConfirmPrint" class="btn btn-primary" onclick="sendDataPrintOrder(1)"><i
                                class="fa fa-print"></i> In PDF</button>
                    <button type="button" id="btnConfirmPrintExcel" onclick="sendDataPrintOrder(2)" class="btn btn-success"><i
                                class="fa fa-file-export"></i>Xuất Excel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="exampleModalLabel">Cập nhật giá</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span>Giá nhập sản phẩm trên hóa đơn sẽ được cập nhật theo ngày giao hàng</span>
                    <br> Bạn có đồng ý với điều này ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Không</button>
                    <button type="button" class="btn btn-primary" onclick="updateNewPriceList('{{ $order->id }}')">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style type="text/css">
        @media (min-width: 768px) {
            .box-body td, .box-body th {
                max-width: 888px;
                word-break: break-word;
            }
        }

        @media screen and (max-width: 810px) {
            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
                min-width: 128px;
            }

        }
        th {
            white-space: nowrap;
        }
        .td-title {
            width: 35%;
            font-weight: bold;
        }

        .undo_detail {
            text-align: center;
            max-width: 80px;
            width: 80px !important;
            word-break: normal !important;
        }

        table {
            width: 100%;
        }

        table td {
            white-space: nowrap; /** added **/
        }

        .table-product td {
            white-space: normal;
        }
        .pro_by_dish input {
            margin-bottom: 5px;
        }
        table td:last-child {
            width: auto;
        }
        .custom-control-label {
            font-weight: 400 !important;
        }
        .icon-arrow-right {
            border: solid black !important;
            border-width: 0 3px 3px 0 !important;
            display: inline-block !important;
            padding: 3px !important;
            transform: rotate(-45deg) !important;
            -webkit-transform: rotate(-45deg) !important;
        }
        .icon-arrow-left {
            border: solid black !important;
            border-width: 0 3px 3px 0 !important;
            display: inline-block !important;
            padding: 3px !important;
            transform: rotate(135deg) !important;
            -webkit-transform: rotate(135deg) !important;
        }
        .amount_of_product_in_order, .import_price {
            text-align: right;
        }
        .noti-order {
            text-align: left;
            margin-left: 17px;
        }
        .noti-order ul {
            text-align: left;
            font-weight: normal;
            font-size: 19px;
            margin-top: -7px;
        }
        .noti-order h4 {
            color:#D26C56  ;
            font-weight: bold;
        }
        .noti-order h5 {
            margin: 7px 0;
        }
        .editableform {
            display: table-caption;
        }
        .editable-clear {
            display: none;
        }
    </style>
    <!-- Ediable -->
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>

    <script type="text/javascript">

        function update_total(k, ut) {
            var product_unit_type = ut;
            var k = k;
            var bom = $('.add_bom_' + k).eq(0).val();
            var qty = $('.add_qty_' + k).eq(0).val();
            var import_price_str = $('.add_import_price_' + k).eq(0).val();
            var import_price = Number(import_price_str.replace(/[^0-9\.-]+/g,""));
            $('.add_total_bom_' + k).eq(0).val(roundTotalBom(qty * bom, product_unit_type))

            var total_cost = Math.round(roundTotalBom(qty * bom, product_unit_type) * import_price);
            let formated = total_cost.toLocaleString('en-US');
            $('.add_total_cost_' + k).eq(0).val(formated);
        }

        function formatMoney(number) {
            return number.toLocaleString();
        }

        // Check remove dom
        function checkRemoveDOM() {
            if ($('#add_td').length == 0) {
                $('#add-item-button-save').hide();
            }
            if ($('#add_td_extra_order').length == 0) {
                $('#add-item-button-save-extra-order').hide();
            }
        }

        $(function () {
            $(".date_time").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });

        //Add item
        function selectDish(element){
            let node = element.closest('tr');
            let id = node.find('option:selected').eq(0).val();
            let cId = $('[name="cId"]').val();
            let oId =  $('[name="id"]').val();
            if (!id) {
                node.find('.add_dish_code').html('');
                node.find('.pro_by_dish').remove();
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.dish_info') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        id : id,
                        cId : cId,
                        oId: oId
                    },
                    beforeSend: function(){
                        $('#loading').show();
                        node.find('.pro_by_dish').remove();
                    },
                    success: function(returnedData){
                        $('#loading').hide();

                        node.find('.add_dish_code').val(returnedData.dish_code);
                        node.find('#add_td').after(returnedData.products);
                        if (returnedData.error == 1) {
                            alertJs('error', returnedData.msg);
                        }
                        if (returnedData.out_of_stock == 1) {
                            alertJs('error', returnedData.msg_out_of_stock);
                        }
                    }
                });
            }
        }

        $('#add-item-button').click(function () {
            var html = '{!! $htmlSelectDish !!}';
            $('#add-item').before(html);
            $('.select2').select2();
            $('#add-item-button-save').show();
        });

        $('#add-item-button-save').click(function (event) {
            var customer_id = $('[name="customer_id"]').val();
            $('#add-item-button').prop('disabled', true);
            $('#add-item-button-save').button('loading');
            $.ajax({
                url: '{{ route("admin.davicook_order.detail_add") }}',
                type: 'post',
                dataType: 'json',
                data: $('form#form-add-item').serialize() +"&customer_id="+customer_id,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                        $('#add-item-button').prop('disabled', false);
                    }
                }
            });
        });


        $(document).ready(function () {
            all_editable();
        });

        function all_editable() {
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };

            $('.updateInfo').editable({
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });


            $('.updateStatus').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        location.reload();
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    }
                }
            });

            $('.updateInfoRequired').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.updateDeliveryTime').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function(value) {
                    var format = convertDate(value);
                    $(this).text(format);
                }
            });

            $('.updateBillDate').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function(value) {
                    var format = convertDate(value);
                    $(this).text(format);
                }
            });

            $('.updateNumberOfServings').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        location.reload();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.updateNumberOfExtraServings').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        location.reload();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.edit-item-bom').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                    if (Number(value) < 0) {
                        return 'Định lượng của nguyên liệu không được âm!';
                    }
                    if (!checkLimitDecimal(Number(value))) {
                        return 'Định lượng nhập vào đã vượt mức cho phép hoặc đã vượt quá 7 chử số ở phần thập phân, vui lòng kiểm tra lại!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        $('.data-total-cost').html(response.detail.total_cost);
                        $('.data-total').html(response.detail.total);
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-discount').html(response.detail.discount);
                        $('.item_id_' + response.detail.item_id).html(response.detail.item_total_bom + ' ' + response.detail.item_unit );
                        $('.item_total_cost_id_' + response.detail.item_id).html(response.detail.item_total_cost);
                        var objblance = $('.data-balance').eq(0);
                        objblance.before(response.detail.balance);
                        objblance.remove();
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },

            });

            $('.edit-item-bom').click(function() {
                var return_order_check  = $('[name="return-order-check"]').val();
                if (return_order_check == 1) {
                    alertMsg('warning', '<div class="noti-order"><h4>Cảnh báo:</h4><h5>Đơn hàng đã có lịch sử trả hàng trước đó, thay đổi thông tin đơn có thể dẫn đến sai báo cáo.</h5>'+
                        '<h5>Nếu muốn thay đổi, vui lòng thao tác theo các bước:</h5>'+
                        '<ul><li>B1: Xóa đơn hàng và tạo lại đơn hàng mới</li><li>B2: Chỉnh sửa thông tin trên đơn</li><li>B3: Trả hàng</li></ul></div>');
                    $('.edit-item-bom').editable('hide');
                }
            });

            $('#number-of-servings').click(function() {
                var return_order_check  = $('[name="return-order-check"]').val();
                if (return_order_check == 1) {
                    alertMsg('warning', '<div class="noti-order"><h4>Cảnh báo:</h4><h5>Đơn hàng đã có lịch sử trả hàng trước đó, thay đổi thông tin đơn có thể dẫn đến sai báo cáo.</h5>'+
                        '<h5>Nếu muốn thay đổi, vui lòng thao tác theo các bước:</h5>'+
                        '<ul><li>B1: Xóa đơn hàng và tạo lại đơn hàng mới</li><li>B2: Chỉnh sửa thông tin trên đơn</li><li>B3: Trả hàng</li></ul></div>');
                }
            });

            $('.edit-item-import-price').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        $('.data-total-cost').html(response.detail.total_cost);
                        $('.data-total').html(response.detail.total);
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-discount').html(response.detail.discount);
                        $('.item_total_cost_id_' + response.detail.item_id).html(response.detail.item_total_cost);
                        var objblance = $('.data-balance').eq(0);
                        objblance.before(response.detail.balance);
                        objblance.remove();
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function (value, response) {
                    let a = Number(value);
                    let x = a.toLocaleString('en-US') + '₫';
                    $(this).text(x);
                },
            });

            $('.edit-item-detail').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        $('.data-total-cost').html(response.detail.total_cost);
                        $('.data-total').html(response.detail.total);
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-discount').html(response.detail.discount);
                        $('.item_id_' + response.detail.item_id).html(response.detail.item_total_bom);
                        $('.item_total_cost_id_' + response.detail.item_id).html(response.detail.item_total_cost);
                        var objblance = $('.data-balance').eq(0);
                        objblance.before(response.detail.balance);
                        objblance.remove();
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.edit-item-total-bom').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                    if (Number(value) < 0) {
                        return 'Nguyên liệu suất của nguyên liệu không được âm!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        $('.data-total-cost').html(response.detail.total_cost);
                        $('.data-total').html(response.detail.total);
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-discount').html(response.detail.discount);
                        $('.item_total_cost_id_' + response.detail.item_id).html(response.detail.item_total_cost);
                        var objblance = $('.data-balance').eq(0);
                        objblance.before(response.detail.balance);
                        objblance.remove();
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },

            });

            $('.edit-item-total-bom').click(function() {
                var return_order_check  = $('[name="return-order-check"]').val();
                if (return_order_check == 1) {
                    alertMsg('warning', '<div class="noti-order"><h4>Cảnh báo:</h4><h5>Đơn hàng đã có lịch sử trả hàng trước đó, thay đổi thông tin đơn có thể dẫn đến sai báo cáo.</h5>'+
                        '<h5>Nếu muốn thay đổi, vui lòng thao tác theo các bước:</h5>'+
                        '<ul><li>B1: Xóa đơn hàng và tạo lại đơn hàng mới</li><li>B2: Chỉnh sửa thông tin trên đơn</li><li>B3: Trả hàng</li></ul></div>');
                    $('.edit-item-total-bom').editable('hide');
                }

            });

            $('.edit-item-comment').editable({
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
            $('.edit-order-comment').editable({
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.updatePrice').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                },

                success: function (response, newValue) {
                    if (response.error == 0) {
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-received').html(response.detail.received);
                        $('.data-subtotal').html(response.detail.subtotal);
                        $('.data-tax').html(response.detail.tax);
                        $('.data-total-cost').html(response.detail.total_cost);
                        $('.data-total').html(response.detail.total);
                        $('.data-shipping').html(response.detail.shipping);
                        $('.data-discount').html(response.detail.discount);
                        var objblance = $('.data-balance').eq(0);
                        objblance.before(response.detail.balance);
                        objblance.remove();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }

        function sendDataPrintOrder(typeExport) {
            $('#type_export').val(typeExport)
            $('#printForm').submit();
        }

        {{-- sweetalert2 --}}
        function deleteItem(id, order_detail_type) {
            var order_id =  $('[name="id"]').val();
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ sc_language_render('action.delete_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'POST',
                            url: '{{ route("admin.davicook_order.detail_delete") }}',
                            data: {
                                'dId': id,
                                'oId': order_id,
                                'order_detail_type' : order_detail_type,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    location.reload();
                                    alertJs('success', response.msg);
                                } else {
                                    alertJs('error', response.msg);
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                }
            })
        }

        {{--/ sweetalert2 --}}
        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        function order_print() {
            $('.not-print').hide();
            window.print();
            $('.not-print').show();
        }

        function updateNewPriceList(idOrder) {
            let id = idOrder;
            window.location.href = '{{ sc_route_admin('admin.davicook_order.update_price') }}?id=' + id;
        }

        // Update customer davicook info
        $(document).ready(function () {
            $('.select2').select2()
        });
        $('[name="customer_id"]').change(function () {
            var order_id =  $('[name="id"]').val();
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Thay đổi khách hàng các món ăn trong đơn hàng có thể bị xóa',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Đồng ý',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: 'Trở về',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'POST',
                            url: '{{ route("admin.davicook_order.delete_all_detail") }}',
                            data: {
                                oId: order_id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    getInfo();
                                    updateInfo();
                                } else {
                                    alertJs('error', response.msg);
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                }
            })

        });

        function getInfo() {
            id = $('[name="customer_id"]').val();
            order_id =  $('[name="id"]').val();
            if (id) {
                $.ajax({
                    url: '{{ sc_route_admin('admin.davicook_order.customer_info') }}',
                    type: "get",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id,
                        order_id: order_id,
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (result) {
                        var returnedData = JSON.parse(result);
                        $('[name="email"]').val(returnedData.email);
                        $('[name="address"]').val(returnedData.address);
                        $('[name="phone"]').val(returnedData.phone);
                        $('#loading').hide();
                        location.reload();
                    }
                });
            } else {
                $('#form-main').reset();
            }
        }

        function updateInfo() {
            id = $('[name="customer_id"]').val();
            order_id =  $('[name="id"]').val();
            if (id) {
                $.ajax({
                    url: '{{ sc_route_admin('admin.davicook_order.update_customer_info') }}',
                    type: "get",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id,
                        order_id: order_id,
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (response) {
                        if (response.error == 0) {
                            alertJs('success', response.msg);
                        } else {
                            alertJs('error', response.msg);
                        }
                    }
                });
            } else {
                $('#form-main').reset();
            }
        }

        // Checkbox print order
        $("input:checkbox").on('click', function() {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });

        // Validate submit print order detail
        function validateSubmitPrint(form) {
            var option = $('[name="type_print_status"]:checked').val();
            var order_status = $('[name="order_status"]').val();
            if(option) {
                if (option==="3" && (order_status==="1" || order_status==="0" || order_status==="7")) {
                    Swal.fire(
                        'Lỗi in đơn',
                        'Đơn hàng không thể In hàng khô!',
                        'error'
                    );
                    return false;
                }
            }else {
                Swal.fire(
                    'Lỗi',
                    'Chưa chọn nội dung in!',
                    'error'
                );
                return false;
            }
        }

        /**
         * Hoàn tác trả hàng
         * @param id
         * @param detail_id
         */
        function undoReturnOrder(id, detail_id) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Bạn muốn hoàn tác lại ?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'POST',
                            url: '{{ route("admin.davicook_order.undoReturnOrder") }}',
                            data: {
                                'return_id': id,
                                'detail_id': detail_id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    location.reload();
                                    alertJs('success', response.msg);
                                } else {
                                    alertJs('error', response.msg);
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })
        }

        // Get format currency when user type
        function formatCurrency(input, blur) {
            var input_val = input.val();

            if (input_val === "") { return; }
            var original_len = input_val.length;
            var caret_pos = input.prop("selectionStart");
            if (input_val.indexOf(".") >= 0) {
                var decimal_pos = input_val.indexOf(".");
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);
                left_side = formatNumber(left_side);
                right_side = formatNumber(right_side);
                if (blur === "blur") {
                    right_side += "00";
                }
                right_side = right_side.substring(0, 2);
                input_val = left_side + "." + right_side;
            } else {
                input_val = formatNumber(input_val);
                input_val =  input_val;
                if (blur === "blur") {
                    input_val += ".00";
                }
            }
            input.val(input_val);
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }
        function checkLimitDecimal(value) {
            var rx = /^(\d{0,5}\.\d{0,7}|\d{0,5}|\.\d{0,7})$/;;
            if(rx.test(value)) {
                return true;
            }else{
                return false;
            }
        }

        // Set limit decimal range input
        function limitDecimalPlaces(e, count) {
            if (e.target.value < 0) { e.target.value = Math.abs(e.target.value)}
            if (e.target.value.indexOf('.') == -1) { return; }
            if ((e.target.value.length - e.target.value.indexOf('.')) > count)
            {
                e.target.value = parseFloat(e.target.value).toFixed(count);
            }
        }

        function convertDate(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return ([date.getFullYear(), mnth, day].join("-")).split("-").reverse().join("/");
        }
    </script>

    {{------------------------------------------- Extra order -----------------------------------------------}}
    <script type="text/javascript">

        function selectDishExtraOrder(e) {
            node = e.closest('tr');
            var dish_id = node.find('option:selected').eq(0).val();
            var customer_id = $('[name="customer_id"]').val() ;

            $.ajax({
                url : '{{ sc_route_admin('admin.davicook_order.get_product_dish_create_extra_order') }}',
                type : "get",
                dateType:"application/json; charset=utf-8",
                data : {
                    dish_id : dish_id,
                    customer_id : customer_id
                },
                beforeSend: function(){
                    $('#loading').show();
                    node.find('.td-select-dish').remove();
                },
                success: function(returnedData){
                    $('#loading').hide();
                    node.find('.add_dish_code_extra_order').val(returnedData.dish_code);
                    node.find('#add_td_extra_order').after(returnedData.dish);
                    if (returnedData.error == 1) {
                        alertJs('error', returnedData.msg);
                    }
                    $('.select2').select2();
                }
            });
        }

        $('#add-item-button-extra-order').click(function () {
            var html = '{!! $htmlSelectDishExtraOrder !!}';
            $('#add-item-extra-order').before(html);
            $('#add-item-button-save-extra-order').show();
            $('.select2').select2();
        });

        $('#add-item-button-save-extra-order').click(function (event) {
            var customer_id = $('[name="customer_id"]').val();
            $('#add-item-button-extra-order').prop('disabled', true);
            $('#add-item-button-save-extra-order').prop('disabled', true);

            $.ajax({
                url: '{{ route("admin.davicook_order.detail_add_extra_order") }}',
                type: 'post',
                dataType: 'json',
                data: $('form#form-add-item-extra-order').serialize() +"&customer_id="+customer_id,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                        $('#add-item-button-extra-order').prop('disabled', false);
                        $('#add-item-button-save-extra-order').prop('disabled', false);
                    }
                }
            });
        });

        function addProduct(e) {
            let node = e.closest('tr');
            let customer_id = $('[name="customer_id"]').val();

            $.ajax({
                url : '{{ sc_route_admin('admin.davicook_order.add_product_create_extra_order') }}',
                type : "get",
                dateType:"application/json; charset=utf-8",
                data : {
                    customer_id : customer_id
                },
                success: function(returnedData) {
                    if (returnedData.error == 1) {
                        alertMsg('error', returnedData.msg);
                    } else {
                        node.find('.add-product-name').before(returnedData.product);
                        node.find('.add-product-unit').before(returnedData.product_unit);
                        node.find('.add-product-total_bom').before(returnedData.total_bom);
                        node.find('.add-product-import_price').before(returnedData.import_price);
                        node.find('.add-product-total_cost').before(returnedData.total_cost);
                        node.find('.add-product-comment').before(returnedData.comment);
                        node.find('.add-product-delete').before(returnedData.delete);
                        $('.select2').select2();
                    }
                }
            });
        }

        function deleteProduct(key, e) {
            let node = e.closest('tr');
            if (node.find('.delete-item').length <= 1) {
                node.find('.delete-item').prop("disabled", true);
            } else {
                $('.product_key_' + key).remove();
            }
            node.find('.add_number_product_extra_order').val(node.find('.selected-product').length);
        }

        function selectProductExtraOrder(key, e) {
            let node = e.closest('tr');
            var product_id = node.find('.add_product_id_extra_order_' + key).eq(0).val();
            var customer_id = $('[name="customer_id"]').val() ;
            var delivery_time = ($('.updateDeliveryTime').data('value')).split("/").reverse().join("-");
            $.ajax({
                url : '{{ sc_route_admin('admin.davicook_order.get_product_info_create_extra_order') }}',
                type : "get",
                dateType:"application/json; charset=utf-8",
                data : {
                    product_id : product_id,
                    customer_id : customer_id,
                    delivery_time: delivery_time
                },
                beforeSend: function() {
                    $('#loading').show();
                },
                success: function(returnedData) {
                    $('#loading').hide();
                    if (returnedData.out_of_stock == 1) {
                        node.find('.add_total_bom_extra_order_' + key).val(0);
                        node.find('.add_total_bom_extra_order_' + key).prop('readonly', true);
                        alertJs('error', returnedData.msg_out_of_stock);
                    }
                    node.find('.add_import_price_extra_order_' + key).val((returnedData.import_price).toLocaleString('en-US')).trigger('keyup');
                    node.find('.add_product_name_extra_order_' + key).val(returnedData.product_name);
                    node.find('.add_product_type_extra_order_' + key).val(returnedData.product_type);
                    node.find('.add_product_unit_extra_order_' + key).val(returnedData.product_unit);
                    node.find('.delete-item').addClass('selected-product');
                    node.find('.add_number_product_extra_order').val(node.find('.selected-product').length);
                }
            });
        }

        function update_sum_total_cost() {
        }

        function update_total_extra_order(key) {
            var total_bom_extra_order = $('.add_total_bom_extra_order_' + key).eq(0).val();
            var import_price_extra_order_str = $('.add_import_price_extra_order_' + key).eq(0).val();
            var import_price_extra_order = Number(import_price_extra_order_str.replace(/[^0-9\.-]+/g,""));
            var total_cost_extra_order = Math.round(total_bom_extra_order * import_price_extra_order);
            let formated = total_cost_extra_order.toLocaleString('en-US');
            $('.add_total_cost_extra_order_' + key).eq(0).val(formated);
        }

    </script>
    {{------------------------------------------- End Extra order -----------------------------------------------}}
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
