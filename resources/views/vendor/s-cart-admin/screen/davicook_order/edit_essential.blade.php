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
                               class="btn btn-flat btn btn-primary" {{ !$editable ? 'order-lock=hide' : "" }}><i
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
                            <a data-perm="davicook_order:update_price" href="#" class="btn btn-flat btn btn-primary" {{ !$editable ? 'order-lock=hide' : "" }}><i
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
                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
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
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-url="{{ route("admin.davicook_order.order_update") }}"
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
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-pk="{{ $order->id }}" data-url="{{ route("admin.davicook_order.order_update") }}"
                                       data-title="Ngày trên hóa đơn:">{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</a>
                                </td>
                                {{-- <td>{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</td> --}}
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày xuất kho hàng khô:</td>
                                <td>{{ !empty($order->export_date) ? date('d/m/Y', strtotime($order->export_date)) : '' }}</td>
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


                <label style="font-size: 20px;color: blue; margin-left: 10px; margin-top: 30px;">
                    NHU YẾU PHẨM
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
                                            <th style="text-align: center; width: auto" class="stt">STT</th>
                                            <th style="text-align: left; width: 140px" class="sku">Mã sản phẩm</th>
                                            <th style="text-align: left; min-width: 220px" class="product_name">Tên sản phẩm</th>
                                            <th style="text-align: left; min-width: 140px" class="total_bom">Số lượng</th>
                                            <th style="text-align: left; min-width: 90px" class="product_unit">Đơn vị</th>
                                            <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                            <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền</th>
                                            <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                            <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($order->details as $item)
                                            <tr>
                                                <td class="check-product-no" style="text-align: center">{{ $i++ }}</td>
                                                <td>{{ $item->product_code ?? '' }}</td>
                                                <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }};
                                                        text-underline-offset: 5px;
                                                        text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                    {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                </td>
                                                <td class="product_qty item_id_{{ $item->id }}">
                                                    <a  data-perm="davicook_order:edit_info" perm-type="disable" href="#" class="edit-item-total-bom"
                                                        data-value="{{ $item->total_bom ?? ''}}"
                                                        data-name="total_bom" data-type="text"
                                                        data-pk="{{ $item->id ?? ''}}"
                                                        data-emptytext="Trống"
                                                        {{ !$editable ? 'order-lock=disable' : "" }}
                                                        data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                        data-title="Số lượng">
                                                        {{ $item->total_bom }}
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
                                                       data-url="{{ route("admin.davicook_order.detail_update") }}"
                                                       data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                </td>
                                                <td style="text-align: center">
                                                    <span data-perm="davicook_order:edit_info" onclick="deleteItem('{{ $item->id ?? '' }}',0);"
                                                          class="btn btn-danger btn-sm btn-flat" data-title="Delete" {{ !$editable ? 'order-lock=hide' : "" }}>
                                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr id="add-item" class="not-print">
                                            <td colspan="10">
                                                <button
                                                        data-perm="davicook_order:edit_info" type="button" class="btn btn-flat btn-success"
                                                        id="add-item-button"
                                                        title="{{sc_language_render('action.add') }}" {{ !$editable ? 'order-lock=hide' : "" }}><i
                                                            class="fa fa-plus"></i> Thêm sản phẩm
                                                </button>
                                                &nbsp;&nbsp;&nbsp;
                                                <button
                                                        style="display: none; margin-right: 50px"
                                                        type="button" class="btn btn-flat btn-warning"
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
                                    <th style="width: 55px">{{ sc_language_render('order.admin.no') }}</th>
                                    <th style="">{{ sc_language_render('order.admin.employe') }}</th>
                                    <th style="">{{ sc_language_render('product.name') }}</th>
                                    <th class="product_return_qty">{{ sc_language_render('admin.order.qty') }}</th>
                                    <th class="product_return_qty">{{ sc_language_render('order.admin.return_no') }}</th>
                                    <th class="product_return_price">{{ sc_language_render('admin.davicook.name_price') ?? "Giá nhập" }}</th>
                                    <th class="product_return_price">{{ sc_language_render('product.total_price') }}</th>
                                    <th class="product_return_comment">{{ sc_language_render('order.admin.return_date') }}</th>
                                    <th class="undo_detail"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($returnOrderHistory as $k => $item)
                                    <tr>
                                        <td class="no_order">{{ $loop->iteration }}</td>
                                        <td style="width: 120px">{{ $item->getEditor() }}</td>
                                        <td>{{ $item->product->name ?? '' }}
                                        <td class="product_return_qty">{{ $item->original_qty ?? 0 }}</td>
                                        <td class="product_return_qty">{{ $item->return_qty ?? 0 }}</td>
                                        <td class="product_return_price">{{ sc_currency_render($item->import_price ?? 0, $order->currency ?? 'VND') }}</td>
                                        <td class="product_return_price">{{ sc_currency_render($item->return_total ?? 0, $order->currency ?? 'VND') }}</td>
                                        <td class="product_return_comment">{{ formatDateVn($item->created_at, true) ?? 'Trống' }}</td>
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
                                            {{ !$editable ? 'order-lock=disable' : "" }}
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
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form onsubmit="return validateSubmitPrint(this);" class="modal-content" id="print-order" method="get" target="_blank"
                  action="{{ sc_route_admin('admin.davicook_order.print_multiple') }}">
                @csrf
                <input type="hidden" name="ids" value="{{ $order->id }}">
                <input type="hidden" name="order_status" value="{{ $order->status }}">
                <input type="hidden" name="customer_ids" id="customer_ids" value="{{ $order->customer_id }}">
                <input type="hidden" name="bill_dates" id="bill_dates" value="{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}">
                <input type="hidden" name="delivery_dates" id="delivery_dates" value="{{ date('d/m/Y', strtotime($order->delivery_date ?? '')) }}">
                <input type="hidden" name="detail" id="detail" value="detail">
                <input type="hidden" name="type_export" id="type_export" value="">
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
                                <input name="type_print_status" id="info_2" type="checkbox" class="custom-control-input radio" checked value="2">
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

        .product_return_qty {
            width: 90px;
            text-align: right;
        }

        .product_return_price {
            width: 110px;
            text-align: right;
        }

        .product_return_comment {
            width: 150px;
            text-align: left;
        }

        .undo_detail {
            text-align: center;
            width: 80px !important;
            max-width: 80px !important;
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

        function update_total(key) {
            var qty = $('.add_product_qty_' + key).val();
            var import_price_str = $('.add_import_price_' + key).eq(0).val();
            var import_price = Number(import_price_str.replace(/[^0-9\.-]+/g,""));

            var total = Math.round(qty * import_price);
            let formated = total.toLocaleString('en-US');
            $('.add_total_' + key).val(formated);
        }

        function formatMoney(number) {
            return number.toLocaleString();
        }

        // Check remove dom
        function checkRemoveDOM() {
            if ($('#add_td').length == 0) {
                $('#add-item-button-save').hide();
            }
        }

        $(function () {
            $(".date_time").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });

        //Add item
        function selectDish(element){
            node = element.closest('tr');
            var id = node.find('option:selected').eq(0).val();
            var cId = $('[name="cId"]').val();
            var oId =  $('[name="id"]').val();
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
                        node.find('.add_dish_code').val(returnedData.dish_code);
                        node.find('#add_td').after(returnedData.products);
                        if (returnedData.error == 1) {
                            alertJs('error', returnedData.msg);
                        }
                        $('#loading').hide();
                    }
                });
            }
        }

        function updateProductNo() {
            $('.check-product-no').each(function (i) {
                $(this).text(i + 1)
            });
        }

        $(document).bind('keypress', function(e) {
            if (e.keyCode == 13) {
                $('#add-item-button').trigger('click');
            }
        });

        $('#add-item-button').click(function () {
            var customer_id = $('[name="customer_id"]').val();
            let product_no = $('.check-product-no').length;
            if (!customer_id) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_create_essential_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        customer_id : customer_id,
                        product_no: product_no
                    },
                    success: function(returnedData){
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#add-item').before(returnedData.product);
                            $('.select2').select2();
                            $('#add-item-button-save').show();
                        }
                    }
                });
            }
        });

        // Send data in đơn hàng davicook.
        function sendDataPrintOrder(typeExport) {
            $('#type_export').val(typeExport)
            $('#print-order').submit();
        }

        $('#add-item-button-save').click(function (event) {
            let customer_id = $('[name="customer_id"]').val();
            $('#add-item-button').prop('disabled', true);
            $('#add-item-button-save').button('loading');
            $('#add-item-button-save').prop('disabled', true);
            $.ajax({
                url: '{{ route("admin.davicook_order.essential_order_detail_add") }}',
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

        function selectProduct(key, e) {
            let node = e.closest('tr');
            var product_id = node.find('option:selected').eq(0).val();
            if (product_id == 0) {
                node.remove();
                return alertJs('error','Sản phẩm hết hàng!');
            }
            var customer_id = $('[name="customer_id"]').val() ;
            var delivery_time = ($('.updateDeliveryTime').data('value')).split("/").reverse().join("-");

            if (!customer_id) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_info_create_extra_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        product_id : product_id,
                        customer_id : customer_id,
                        delivery_time: delivery_time,
                    },
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(returnedData){
                        $('#loading').hide();
                        node.find('.add_product_sku_' + key).val(returnedData.product_sku);
                        node.find('.add_import_price_' + key).val((returnedData.import_price).toLocaleString('en-US')).trigger('keyup');
                        $.each(node.find('.add_import_price_' + key).attr('class').split(' '), function(id, item) {
                            if (item.indexOf('update_change_') == 0) node.find('.add_import_price_' + key).removeClass(item);
                        });
                        node.find('.add_import_price_' + key).addClass('update_change_' + returnedData.product_sku);
                        node.find('.add_product_name_' + key).val(returnedData.product_name);
                        node.find('.add_product_type_' + key).val(returnedData.product_type);
                        node.find('.add_product_unit_' + key).val(returnedData.product_unit);
                    }
                });
            }
        }

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

        {{-- sweetalert2 --}}
        function deleteItem(id) {
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
                            url: '{{ route("admin.davicook_order.essential_order_detail_delete") }}',
                            data: {
                                'detail_order_id': id,
                                'oId': order_id,
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
                            url: '{{ route("admin.davicook_order.undoReturnEssentialOrder") }}',
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
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>

@endpush
