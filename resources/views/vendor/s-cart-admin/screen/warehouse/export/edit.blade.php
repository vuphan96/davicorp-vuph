@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">Chi tiết đơn hàng
                        #{{ $orderExport->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ sc_route_admin('admin_warehouse_export.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a data-perm="order_export:create" href="{{sc_route_admin('admin_warehouse_export.create')}}" class="btn btn-flat btn-primary" id="button_create_new">
                                <i class="fa fa-plus" title="Thêm"></i></a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="#" class="btn btn-flat btn btn-primary" onclick="location.reload()"><i
                                        class="fa fa-sync-alt"></i></a>
                        </div>

                    </div>
                </div>

                <form class="row" id="order_edit_form" method="post">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $orderExport->id }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <td class="td-title">Xuất kho cho</td>
                                <td {{ !$editable ? 'order-lock=disable' : "" }}>
                                    <select class="form-control customer_id select2" style="width: 100%;" name="customer_id" id="customer_id" onchange="getAddress()" {{ $orderExport->type_order == 2 || $orderExport->status == 2 ? 'disabled' : '' }}>
                                        <option {{$orderExport->customer_name == 'Lý do khác' ? 'selected' : ''}} value="01">Lý do khác</option>
                                        <option {{$orderExport->customer_name == 'Xuất kho từ báo cáo hàng ngày' ? 'selected' : ''}} value="02">Xuất kho từ báo cáo hàng ngày</option>
                                        @foreach ($customers as $key => $customer)
                                            <option value="{{ $customer->id }}" {{ $orderExport->customer_id == $customer->id ? 'selected' : '' }}>{{ $orderExport->customer_id == $customer->id ? $orderExport->customer_name : $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_address') }}:</td>
                                <td id="address_customer">{!! $orderExport->customer_addr ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.phone') }}:</td>

                                <td id="phone_customer">{!! $orderExport->phone ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.email') }}:</td>
                                <td id="email_customer">{!! $orderExport->email ?? '' !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Trạng thái đơn xuất</td>
                                @php
                                $dataStatus = [];
                                foreach ($status as $key => $item) {
                                    $dataStatus[] = [
                                        'value'=>$key,
                                        'text'=>$item
                                ];
                                }
                                @endphp
                                <td>
                                    <a data-perm="order:edit_info" perm-type="disable" href="#" class="updateStatus"
                                       data-name="status" data-type="select"
                                       data-source="{{ json_encode($dataStatus) }}"
                                       data-pk="{{ $orderExport->id }}"
                                       data-value="{{ $orderExport->status }}"
                                       data-url="{{ route("order_export.update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Trạng thái đơn xuất">{{ $status[$orderExport->status] }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày đặt đơn</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($orderExport->created_at ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> Ngày xuất hàng</td>
                                <td>{{ date('d/m/Y', strtotime($orderExport->date_export ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> Kho</td>
                                <td>
                                    {{ $orderExport->warehouse_name }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>

                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="import_id" value="{{ $orderExport->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table id="table-product" class="table table-hover box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 45px; padding: 5px; vertical-align: middle; text-align: center">STT</th>
                                            <th style="min-width: 60px; word-break: break-word">Mã </th>
                                            <th style="width: auto; min-width: 80px">Tên sản phẩm</th>
                                            <th style="width: auto; min-width: 50px">Mã đơn hàng</th>
                                            <th style="width: auto; min-width: 120px">Tên khách hàng</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">Số lượng</th>
                                            <th style="min-width: 150px;word-break: break-word" class="product_qty">Số lượng thực tế</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">ĐVT</th>
                                            <th class="product_comment" style="word-break: break-word;min-width: 90px">Ghi chú</th>
                                            <th style="min-width: 50px;word-break: break-word; text-align: center; max-width: 60px">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                             $countStt = $orderExport->details->count();
                                        @endphp
                                        @foreach($orderExport->details->groupBy('product_id') as $key => $detail)
                                            @php
                                                $qty = $detail->sum('qty');
                                                $qty_reality = $detail->sum('qty_reality');
                                            @endphp
                                            <tr class="ordered-products {{ $orderExport->type_order == 2 ? '' : 'd-none' }}">
                                                <td style="text-align: center">{{ $orderExport->type_order == 2 ? $i++ : '' }}</td>
                                                <td class="overflow_prevent">{{ $detail->first()['product_sku'] ?? '' }}</td>
                                                <td>
                                                    {{ $detail->first()['product_name'] ?? '' }}
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right">
                                                    {{ $qty ?? 0 }}
                                                </td>
                                                <td class="text-right">
                                                    {{ $qty_reality ?? 0 }}
                                                </td>
                                                <td class="product_price">{{ $detail->first()['unit'] ?? '' }}</td>
                                                <td class="product_comment" style="overflow: hidden">
                                                </td>
                                                <td style="text-align: center">
                                                    <span data-perm="order_export:edit_info" onclick="showDetails('{{$key}}')" class="btn btn-outline-warning btn-xs" data-title="Hiển thị chi tiết"><i class="fa fa-eye" aria-hidden="true"></i></span>
                                                </td>
                                            </tr>
                                            @foreach ($detail as $item)
                                                <tr class="detail-{{$key}} {{ $orderExport->type_order == 2 ? 'd-none' : '' }}">
                                                    <td style="text-align: center">{{ $orderExport->type_order == 2 ? '' : $i++ }}</td>
                                                    <td class="overflow_prevent">
                                                        {{ $orderExport->type_order == 2 ? '' : ($item['product_sku'] ?? '') }}
                                                    </td>
                                                    <td>
                                                        {{ $orderExport->type_order == 2 ? '' : ($item['product_name']) }}
                                                    </td>
                                                    <td>{{  $item['order_id_name'] }}</td>
                                                    <td>{{  $item['customer_name'] }}</td>

                                                    <td class="product_qty"><a style="font-weight: bold" data-perm="order_export:edit_info"
                                                                               perm-type="disable" href="#"
                                                                               class="edit-item-detail"
                                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                                               data-value="{{ $item['qty'] }}" data-name="qty"
                                                                               data-step="any"
                                                                               data-type="text" data-min="0"
                                                                               data-unit_type="{{ $item['unit_type'] ?? '' }}"
                                                                               data-pk="{{ $item['id'] }}"
                                                                               data-url="{{ route("order_export.update") }}"
                                                                               data-title="Số lượng"> {{ $item['qty'] }}</a>
                                                    </td>
                                                    <td class="product_qty"><a data-perm="order_export:edit-detail_exportquantity"
                                                                               perm-type="disable" href="#"
                                                                               class="edit-item-detail"
                                                                               {{ !$editable ? 'order-lock=disable' : "" }}
                                                                               data-value="{{ $item['qty_reality'] }}" data-name="qty_reality"
                                                                               data-type="text" data-min="0"
                                                                               data-pk="{{ $item['id'] }}"
                                                                               data-url="{{ route("order_export.update") }}"
                                                                               data-title="Số lượng thực tế"> {{ $item['qty_reality'] }}</a>
                                                    </td>
                                                    <td class="product_price">{{ $item['unit'] ?? ''}}<td class="product_comment" style="overflow: hidden"><a
                                                                data-perm="order_export:edit_info" perm-type="disable" href="#"
                                                                class="edit-item-comment"
                                                                {{ !$editable ? 'order-lock=disable' : "" }}
                                                                data-value="{{ $item['comment'] ?? ''}}"
                                                                data-name="comment" data-type="text"
                                                                data-emptytext="Trống"
                                                                data-pk="{{ $item['id'] ?? ''}}"
                                                                data-url="{{ route("order_export.update") }}"
                                                                data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item['comment'] }}</a>
                                                    </td>
                                                    <td style="text-align: center">
                                                    <span data-perm="order_export:edit_info"
                                                          onclick="deleteItem($(this),'{{ $item['id'] }}');"
                                                          {{ !$editable ? 'order-lock=hide' : "" }}
                                                          class="btn btn-danger btn-xs" data-title="Delete"><i
                                                                class="fa fa-trash" aria-hidden="true"></i></span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach

                                        <tr id="add-item" class="not-print">
                                            <td colspan="10">
                                                <button data-perm="order_export:edit_info" type="button"
                                                        class="btn btn-flat btn-success {{ ($orderExport->type_order == 2 || $orderExport->status == 2) ? 'd-none' : '' }}"
                                                        id="add-item-button"
                                                        {{ !$editable ? 'order-lock=hide' : "" }}
                                                        title="{{sc_language_render('action.add') }}"><i
                                                            class="fa fa-plus"></i> {{ sc_language_render('action.add') }}
                                                </button>
                                                &nbsp;&nbsp;&nbsp;<button style="display: none; margin-right: 50px"
                                                                          type="button" class="btn btn-flat btn-warning"
                                                                          id="btnSave" title="Save"><i
                                                            class="fa fa-save"></i> {{ sc_language_render('action.save') }}
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

                <div class="row">
                    {{-- Comment --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="td-title">{{ sc_language_render('order.order_note') }}:</td>
                                    <td class="order-comment"><a href="#" class="edit-order-comment"
                                                                 data-value="{{ $orderExport->note ?? ''}}"
                                                                 data-name="comment"
                                                                 {{ !$editable ? 'order-lock=disable' : "" }}
                                                                 data-type="textarea"
                                                                 data-emptytext="Trống"
                                                                 data-pk="{{ $orderExport->id }}"
                                                                 data-url="{{ route("admin_order.update") }}"
                                                                 data-title="{{ sc_language_render('order.admin.comment') }}"> {{ $orderExport->note ?? 'Trống'}}</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                    {{-- History --}}
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="4">Lịch sử đơn hàng</th>
                                </tr>
                                <tr>
                                    <td>{{ sc_language_render('admin.order_history.time') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.actor') }}</td>
                                    <td>Thao tác</td>
                                    <td>Nội dung</td>
                                </tr>
                                </thead>
                                <tbody  id="order-history">
                                @forelse($orderExport->history ?? [] as $k => $v)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::make($v->created_at)->format('d/m/Y H:i:s') ?? 'Trống' }}</td>
                                        <td>{{ $v->is_admin == 1 ? '(Ad)' : '' }} {{ $v->user_name != '' ? $v->user_name : $v->getEditor()}}</td>
                                        <td>
                                            {{ $v->title }}
                                        </td>
                                        <td>
                                            <div>
                                                {!! $v->content !!}
                                            </div>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                    {{-- //End history --}}

                </div>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content" method="get" target="_blank" id="printForm"
                  action="{{ sc_route_admin('admin_order.print') }}">
                @csrf
                <input type="hidden" name="ids" value="{{ $orderExport->id }}">
                <input type="hidden" name="option" value="1">
                <input type="hidden" name="type_print" id="type_print" value="">
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
                        <label class="col-4">Thông tin in</label>
                        <div class="col-8">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="type" id="radio_0" type="radio" class="custom-control-input" value="1"
                                       checked>
                                <label for="radio_0" class="custom-control-label">Đơn hàng</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="type" id="radio_1" type="radio" class="custom-control-input" value="2">
                                <label for="radio_1" class="custom-control-label">Ghi chú đơn hàng</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                                class="fa fa-undo"></i> {{sc_language_render('action.discard')}}</button>
                    <button type="button" id="btnConfirmPrint1" onclick="sendDataPrintOrder(1)" class="btn btn-primary"><i
                                class="fa fa-print"></i>In PDF</button>
                    <button type="button" id="btnConfirmPrint2" onclick="sendDataPrintOrder(2)" class="btn btn-success"><i
                                class="fa fa-file-export"></i>Xuất Excel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form_update_import_price" action="{{ sc_route_admin('order_import.update_import_price', $orderExport->id) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="exampleModalLabel">Cập nhật giá</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span>Giá sản phẩm trên hóa đơn sẽ được cập nhật theo bảng giá mới nhất</span> <br> Bạn có đồng ý
                        với điều này ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Không</button>
                        <button type="button" id="btn_submit_update_price" class="btn btn-primary" onclick="updateNewPriceList('{{ $orderExport->id }}')">Đồng
                            ý
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session()->get('product_name'))
        <div class="modal-product" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Xóa sản phẩm</h5>
                        <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll; max-height:200px;">
                        <span>Các sản phẩm sau không tồn tại trong bảng giá mới</span><br>
                        <ul style="color: red">{!! session('product_name') !!}</ul>
                        <span>Bạn có muốn xóa nó</span>
                        <input id="id_product" type="hidden" value="{!!  session('idsProduct') ?? ''  !!}">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-modal" data-dismiss="modal">Không</button>
                        <button type="button" class="btn btn-primary" id="delete_product">Có</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection


@push('styles')
    <style type="text/css">
        .history {
            max-height: 50px;
            max-width: 300px;
            overflow-y: auto;
        }

        .td-title {
            width: 35%;
            font-weight: bold;
        }

        .td-title-normal {
            width: 35%;
        }

        .undo_detail {
            text-align: center;
            max-width: 80px !important;
            word-break: normal !important;

        }

        .product_qty {
            width: 120px;
            text-align: right;
        }

        .product_price, .product_total {
            width: 160px;
            text-align: right;
        }

        .product_comment {
            width: 480px !important;
        }

        table {
            width: 100%;
        }

        table td {
            white-space: nowrap; /** added **/
            word-break: break-word !important;
        }
        .table-product td {
            white-space: normal;
        }
        .table-customer td {
            white-space: normal;
        }
        .custom-control-label {
            font-weight: 400 !important;
        }
        .overflow_prevent {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .modal-product {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: block;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
        }
        .editableform {
            display: table-caption;
        }
        .editable-clear {
            display: none;
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

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
        input { width: 400px; }
        label, input { display: block; }
        label { font-weight: bold; }
        input, .flexselect_dropdown li { font-size: 1rem; }
        small { color: #999; }
        .flexselect_selected small { color: #ddd; }

        .show {
            display: block;
        }

    </style>
    {{-- flexselect --}}
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/flexselect.css') }}" type="text/css" media="screen" />
    {{-- X-Editable --}}
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
@endpush

@push('scripts')
    {{-- flexselect --}}
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>

    {{-- Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>

    <!-- Handle navigation with key arrow -->
{{--    <script type="text/javascript">--}}
{{--        function alertJsCustom(type = 'error', msg = '') {--}}
{{--            const Toast = Swal.mixin({--}}
{{--                toast: true,--}}
{{--                position: 'top-end',--}}
{{--                showConfirmButton: false,--}}
{{--                timer: 5000--}}
{{--            });--}}
{{--            Toast.fire({--}}
{{--                type: type,--}}
{{--                title: msg--}}
{{--            })--}}
{{--        }--}}

{{--        $('#table-product').bind('keydown', function(e) {--}}
{{--            if (e.which === 37 || e.which === 38 || e.which === 9 ||--}}
{{--                e.which === 39 || e.which === 40 || e.which === 13) {--}}
{{--                e.preventDefault();--}}
{{--            }--}}
{{--        });--}}

{{--        $('table.table-product').keydown(function(e){--}}
{{--            var $active = $('input:focus,select:focus',$(this));--}}
{{--            var $next = null;--}}
{{--            var focusableQuery = 'input:visible,select:visible,textarea:visible';--}}
{{--            var position = parseInt( $active.closest('td').index()) + 1;--}}
{{--            var tr_position = parseInt( $active.closest('tr').index());--}}
{{--            var tr_length = 0;--}}
{{--            var od_length = 0;--}}

{{--            $('.select-product').each(function () {--}}
{{--                tr_length++;--}}
{{--            });--}}
{{--            $('.ordered-products').each(function () {--}}
{{--                od_length++;--}}
{{--            });--}}

{{--            switch(e.keyCode) {--}}
{{--                case 37: // Left--}}
{{--                    $next = $active.parent('td').prev().find(focusableQuery);--}}
{{--                    if ($next.hasClass('add_total')) {--}}
{{--                        $next = $active.closest('td').prev().prev().find(focusableQuery);--}}
{{--                    }--}}
{{--                    if ($next.hasClass('add_sku')) {--}}
{{--                        $next = $active.closest('td').find(focusableQuery);--}}
{{--                    }--}}
{{--                    if ($next.hasClass('add_unit')) {--}}
{{--                        $next = $active.closest('td').prev().prev().prev().find(focusableQuery);--}}
{{--                    }--}}
{{--                    break;--}}
{{--                case 38: // Up--}}
{{--                    $next = $active--}}
{{--                        .closest('tr')--}}
{{--                        .prev()--}}
{{--                        .find('td:nth-child(' + position + ')')--}}
{{--                        .find(focusableQuery);--}}
{{--                    break;--}}
{{--                case 39: // Right--}}
{{--                    $next = $active.closest('td').next().find(focusableQuery);--}}
{{--                    if ($next.hasClass('add_total')) {--}}
{{--                        $next = $active.closest('td').next().next().find(focusableQuery);--}}
{{--                    }--}}
{{--                    if ($next.hasClass('add_qty_reality')) {--}}
{{--                        $next = $active.closest('td').next().next().next().find(focusableQuery);--}}
{{--                    }--}}
{{--                    break;--}}
{{--                case 40: // Down--}}
{{--                    $next = $active--}}
{{--                        .closest('tr')--}}
{{--                        .next()--}}
{{--                        .find('td:nth-child(' + position + ')')--}}
{{--                        .find(focusableQuery);--}}
{{--                    break;--}}
{{--                case 13: // Enter--}}
{{--                    $next = $active--}}
{{--                        .closest('tr')--}}
{{--                        .next()--}}
{{--                        .find('td:nth-child(' + position + ')')--}}
{{--                        .find(focusableQuery);--}}
{{--                    if (tr_position == tr_length+od_length-1) {--}}
{{--                        $('#add-item-button').trigger('click');--}}
{{--                    }--}}
{{--                    break;--}}
{{--                case 9: // Tab--}}
{{--                    $next = $active.closest('td').next().find(focusableQuery);--}}
{{--                    if ($next.hasClass('add_total') || $next.hasClass('btn-danger')) {--}}
{{--                        $next = $active.closest('td').next().next().find(focusableQuery);--}}
{{--                    }--}}
{{--                    if ($next.hasClass('add_qty_reality')) {--}}
{{--                        $next = $active.closest('td').next().next().next().find(focusableQuery);--}}
{{--                    }--}}
{{--                    break;--}}
{{--            }--}}
{{--            if($next && $next.length) {--}}
{{--                $next.focus();--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
    <!-- /Handle navigation with key arrow -->

    <script type="text/javascript">

        let originalTitle = document.title;
        function closePrint() {
            document.title = originalTitle;
            document.body.removeChild(this.__container__);
        }

        function setPrint() {
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = closePrint;
            this.contentWindow.onafterprint = closePrint;
            this.contentWindow.focus(); // Required for IE
            $("#loading").hide();
            let now = new Date();
            let startDate = $('#from_to').val();
            let endDate = $('#end_to').val();
            document.title = "DonHangDavicorp-"  + startDate + " - " + endDate;
            this.contentWindow.print();
        }

        function printPage(sURL) {
            const hideFrame = document.createElement("iframe");
            hideFrame.onload = setPrint;
            hideFrame.style.position = "fixed";
            hideFrame.style.right = "0";
            hideFrame.style.bottom = "0";
            hideFrame.style.width = "0";
            hideFrame.style.height = "0";
            hideFrame.style.border = "0";
            hideFrame.src = sURL;
            document.body.appendChild(hideFrame);
        }

        $(document).ready(function () {
            all_editable();
        });


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        function sendDataPrintOrder(type) {
            $('#type_print').val(type);
            let optionType = $('#printForm').find('input[name="type"]:checked').val();
            if (optionType == 1) {
                $('#printForm').attr('action', '{{ route('admin_order.print') }}');
            } else if (optionType == 2) {
                $('#printForm').attr('action', '{{ route('admin_order.print_note') }}');
            }
            $('#printForm').submit();
        }

        function order_print() {
            $('.not-print').hide();
            window.print();
            $('.not-print').show();
        }

        $('#btn_submit_update_price').click(function () {
            $('#loading').show();
            $('#form_update_import_price').submit();
            $('#exampleModal').hide();
        })

        $('#delete_product').on('click', function (e) {
            let id = $('#id_product').val();
            var order_id = $('[name="order_id"]').val();
            window.location.href = '{{ sc_route_admin('admin_order.delete_product_old') }}?id=' + id + '&order_id=' + order_id;
        })
        $('.close-modal').on('click', function (e) {
            $('.modal-product').css("display", "none");
        })

        function convert(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);

            return ([date.getFullYear(), mnth, day].join("-")).split("-").reverse().join("/");
        }

        const formatter = new Intl.NumberFormat('en-US', {
        });


    </script>
    <script type="text/javascript">
        var customerList = <?php echo json_encode($customers ?? '[]', 15, 512) ?>;
        var statusOrder = '{{ $orderExport->status }}'
        function getAddress() {
            var customer_id = $('#customer_id').val();
            var idOrder = '{{ $orderExport->id }}'
            var customer_name = '';
            var customer_code = '';
            var customer_addr = '';
            var customer_email = '';
            var customer_phone = '';
            if(customer_id != '01' && customer_id != '02') {
                customer_addr = customerList.find(item => item.id === customer_id).address;
                customer_email = customerList.find(item => item.id === customer_id).email;
                customer_phone = customerList.find(item => item.id === customer_id).phone;
                customer_name = customerList.find(item => item.id === customer_id).name;
                customer_code = customerList.find(item => item.id === customer_id).customer_code;
                $('#address_customer').text(customer_addr);
                $('#email_customer').text(customer_email);
                $('#phone_customer').text(customer_phone);
            } else {
                $('#address_customer').text(customer_addr);
                $('#email_customer').text(customer_email);
                $('#phone_customer').text(customer_phone);
            }
            let dataCustomer = {
                'customer_id':customer_id,
                'customer_name':customer_name,
                'customer_addr':customer_addr,
                'customer_email':customer_email,
                'customer_phone':customer_phone,
                'customer_code':customer_code,
            }
            $.ajax({
                url: '{{ sc_route_admin('admin_warehouse_export.update_customer') }}',
                type: 'post',
                data: {
                    dataCustomer:dataCustomer,
                    orderId:idOrder,
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.error == 1) {
                        alertJs('error', 'Lỗi lưu dữ liệu: ' + response.message);

                    } else {
                        alertJs('success','Cập nhật thành công');
                        location.reload()
                    }
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }

        {{--/ sweetalert2 --}}
        var count = {{ $countStt }}
        var rowIdxArr = [];
        var currentRowIdx = 0;
        var htmlOption = '';
        var productList = <?php echo json_encode($products ?? '[]', 15, 512) ?>;
        if (Array.isArray(productList)) {
            productList.forEach(function (p) {
                p.unit_name = p.unit?.name ?? ''
                htmlOption += '<option value="' + p.id + '">' + p.name +'</option>'
            });
        } else {
            console.error('productList is not an array:', productList);
        }
        $('#add-item-button').click(function () {
            currentRowIdx++;
            var html = '';
            if(productList){
                html +=  '<tr id="dataRow-' + currentRowIdx + '" class="select-product">';
                html +=  '<td id="productIdx-' + currentRowIdx + '" class="check-num-order" style="text-align:center">' + currentRowIdx + '</td>';
                html +=  '<td><input type="text" id="productSku-' + currentRowIdx + '" readonly class="add_sku form-control" value=""></td>';
                html +=  '<td id="add_td">';
                html +=  '<select onchange="selectProduct(' + currentRowIdx + ')" name="product_id[]" id="productName-' + currentRowIdx + '"  class="add_id form-control flexselect" tabindex="2">';
                html +=  '<option value="" selected></option>';
                html +=   htmlOption;
                html +=   '</select>';
                html +=  '</td>';
                html +=  '<td></td>';
                html +=  '<td></td>';
                html +=  '<td class="pro"><input type="text" onkeyup="checkQtyWarehouse(' + currentRowIdx + ')" id="qty-' + currentRowIdx + '"   class="form-control"  tabindex="-1" autocomplete="off" value=""></td>';
                html +=  '<td class="pro"><input type="text" id="qty-reality-' + currentRowIdx + '"  readonly  class="form-control"  tabindex="-1" value=""></td>';
                html +=  '<td class="pro"><input type="text" id="unit-' + currentRowIdx + '"  readonly class="form-control"  tabindex="-1"  value=""></td>';
                html +=  '<td class="pro"><input type="text" id="note-' + currentRowIdx + '"   class="form-control"  value=""></td>';
                html += '<td style="text-align: center;"><button class="btn btn-danger "  id="actionDelete-' + currentRowIdx + '"  onclick="deleteRow(' + currentRowIdx + ')"  tabindex="-1" ><i class="fa fa-times" aria-hidden="true"></i></button></td>';
                html += '</tr>'
            }

            $("#add-item").before(html);
            rowIdxArr.push(currentRowIdx);
            reIndexRowNumber();
            $("select.flexselect").flexselect({ hideDropdownOnEmptyInput: true });
        });

        function deleteRow(rowIdx) {
            $('#dataRow-' + rowIdx).remove();
            var idx = rowIdxArr.indexOf(rowIdx);
            if (idx !== -1) {
                rowIdxArr.splice(idx, 1);
            }

            reIndexRowNumber();
        }
        function reIndexRowNumber() {
            for (let i = 0; i < (rowIdxArr.length + count); i ++) {
                $('#productIdx-' + rowIdxArr[i]).text(i + 1 + count)
            }
            if(rowIdxArr.length > 0) {
                $('#btnSave').show();
            }else{
                $('#btnSave').hide();
            }

        }
        function selectProduct(rowId) {
            var id = $("#productName-" + rowId).val();
            var productItem = productList.find(item => item.id === id);
            $("#productSku-" + rowId).val(productItem.sku);
            $("#unit-" + rowId).val(productItem.unit_name);
            checkQtyWarehouse(rowId)
        }
        function checkNumberOrder() {
            var selected = [];
            $('.check-num-order').each(function (key) {
                $(this).text(key + 1)

            });

            return selected;
        }

        $('#btnSave').click(function () {
            var dataToSave = [];
            var idOrder = '{{ $orderExport->id }}'
            var customer_id = $("#customer_id").val() ?? '';
            for (var i = 0; i < rowIdxArr.length; i++) {
                var rowIdx = rowIdxArr[i];
                var productSku = $('#productSku-' + rowIdx).val();
                var productId = $('#productName-' + rowIdx).val();
                var productName = productList.find(item => item.id === productId);
                var qty = $('#qty-' + rowIdx).val();
                var unit = $('#unit-' + rowIdx).val();
                var comment = $('#note-' + rowIdx).val();
                if (productName && qty) {
                    var rowData = {
                        'productSku': productSku,
                        'productId': productId,
                        'productName': productName.name,
                        'qty': qty,
                        'unit': unit,
                        'note': comment
                    };
                    dataToSave.push(rowData);
                }
            }
            $.ajax({
                url: '{{ sc_route_admin('admin_warehouse_export.edit') }}',
                type: 'post',
                data: {
                    data_save:dataToSave,
                    customer_id:customer_id,
                    order_id:idOrder,
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.success) {
                        alertJs('success', "Đơn xuất kho đã cập nhật thành công !");
                        {{--window.location.replace('{{ sc_route_admin('admin_warehouse_export.index') }}');--}}
                        location.reload()
                    } else {
                        alertJs('error','Lỗi lưu dữ liệu: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alertJs('error','Lỗi dữ liệu khi nhập vui lòng kiểm tra!');
                    console.error('Status:', status);
                    console.error('Error:', error);
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        })

        {{-- sweetalert2 --}}
        function deleteItem(element, id) {
            if (statusOrder == 2) {
                alertJs('error', 'Đơn đã xuất không thể xóa!');
                return false;
            }
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
                            url: '{{ route("order_export.delete_detail") }}',
                            data: {
                                'pId': id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    element.parents().eq(1).remove()
                                    alertJs('success', response.msg);
                                    location.reload()
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
                ) { }
            })
        }

        {{--/ sweetalert2 --}}

        function all_editable() {
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };

            $('.updateInfo').editable({
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        location.reload()
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
                    $("#loading").show();
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                        $("#loading").hide();
                        location.reload()
                    } else {
                        alertJs('error', response.msg);
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
                        location.reload();
                        $('#order-history').html(response.history);
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function(value) {
                    var format = convert(value);
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
                        location.reload();
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function(value) {
                    var format = convert(value);
                    $(this).text(format);
                }
            });

            $('.edit-item-price').editable({
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
                        return 'Giá sản phẩm không được âm!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        location.reload();
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
            $('.edit-item-detail').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                disabled: (statusOrder==2) ? true : false,
                validate: function (value) {

                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                    if (Number(value) < 0) {
                        return 'Số lượng sản phẩm không được âm!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                        location.reload();
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.edit-item-comment').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                disabled: (statusOrder==2) ? true : false,
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });

            $('.edit-order-comment').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
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
        }
        function checkQtyWarehouse(rowId) {
            var dataProductWarehouses = <?php echo json_encode($dataProductWarehouse ?? '[]', 15, 512) ?>;
            let id_warehouse = {{ $orderExport->warehouse_id ?? ''}};

            if(id_warehouse) {
                dataProductWarehouse = dataProductWarehouses.filter(item => item.warehouse_id == id_warehouse)

            }
            let qtyInput = $('#qty-' + rowId).val();
            let id_product = $("#productName-" + rowId).val();

            if(qtyInput && id_product) {
                let dataWarehouseByProduct = dataProductWarehouse.find(item => item.product_id == id_product);
                if(dataWarehouseByProduct) {
                    let qty = dataWarehouseByProduct.qty;
                    if (qtyInput > qty) {
                        alertJs('warning','Cảnh báo: Số lượng sản phẩm lớn hơn số lượng tồn!');
                        return;
                    }
                }
            }

        }
        function printPdf(id) {
            if (id == "") {
                alertMsg('error', 'Cần chọn để xuất', 'Vui lòng chọn it nhất 1 bản ghi trước khi xoá đối tượng');
                return;
            }
            let href = '{{ sc_route_admin('admin_warehouse_export.print') }}?&ids=' + id ;
            printPage(href)


        }
        function showDetails(id) {
            if ($('.detail-'+id).hasClass('d-none')) {
                $('.detail-'+id).removeClass('d-none');
            } else {
                $('.detail-'+id).addClass('d-none');
            }
        }

    </script>
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
