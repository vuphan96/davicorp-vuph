@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">{{ sc_language_render('order.order_detail') }}
                        #{{ $orderImport->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ session('nameUrl') ?? sc_route_admin('order_import.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                        @if($orderImport->status != 3)
                            <div class="btn-group float-right" style="margin-right: 10px">
                                <a data-perm="order_import:return"  {{ !$editable ? 'order-lock=hide' : "" }}
                                href="{{ sc_route_admin('order_import.return', ['id' => $orderImport->id]) }}"
                                   {{ !$editable ? 'order-lock=hide' : "" }}
                                   class="btn btn-flat btn btn-primary"><i
                                            class="fa fa-undo"></i>&nbsp;{{ sc_language_render('order.return') }}</a>
                            </div>
                        @endif
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a disabled data-perm="order_import:print" onclick="printPdf('{{ $orderImport->id }}');" href="#" class="btn btn-flat btn btn-primary" data-toggle="modal"
                               data-target="#printDialog" ><i class="fa fa-print"></i>&nbsp;{{ sc_language_render('admin.order.print_invoice') }}
                            </a>
                        </div>
                        <div style="margin-right: 10px" type="button" class="btn-group float-right" data-toggle="modal"
                             data-target="#exampleModal">
                            <a disabled data-perm="order_import:update_price" class="btn btn-flat btn btn-primary" {{ !$editable ? 'order-lock=hide' : "" }}><i
                                        class="fas fa-pen"></i>&nbsp;Cập nhật giá
                            </a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a data-perm="order_import:create" href="{{sc_route_admin('order_import.create')}}" class="btn btn-flat btn-primary" id="button_create_new">
                                <i class="fa fa-plus" title="' . sc_language_render('action.add') . '"></i></a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="#" class="btn btn-flat btn btn-primary" onclick="location.reload()"><i
                                        class="fa fa-sync-alt"></i></a>
                        </div>

                    </div>
                </div>

                <form class="row" id="order_edit_form" method="post"
                      action="{{ sc_route_admin('admin_order.update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $orderImport->id }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <td class="td-title">Tên nhà cung cấp</td>
                                <td {{ !$editable ? 'order-lock=disable' : "" }}>
                                    <select class="form-control customer_id select2" style="width: 100%;" name="customer_id">
                                        @foreach ($suppliers as $key => $supplier)
                                            <option value="{{ $supplier->id }}" {{ $orderImport->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_address') }}:</td>
                                <td>{!! $orderImport->address ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.phone') }}:</td>

                                <td>{!! $orderImport->phone ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.email') }}:</td>
                                <td>{!! $orderImport->email ?? '' !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Trạng thái đơn nhập</td>
                                <td>
                                    <a data-perm="order:edit_info" perm-type="disable" href="#" class="updateStatus"
                                       data-name="status" data-type="select"
                                       data-source="{{ json_encode($status) }}" data-pk="{{ $orderImport->id }}"
                                       data-value="{!! $orderImport->status !!}"
                                       data-url="{{ route("order_import.update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Trạng thái đơn nhập">{{ $status[$orderImport->status] }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày đặt đơn</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($orderImport->created_at ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> Ngày giao hàng</td>
                                 <td>{{ date('d/m/Y', strtotime($orderImport->delivery_date ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> Kho hàng</td>
                                <td>
                                    {{ $orderImport->warehouse_name }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>

                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="import_id" value="{{ $orderImport->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table id="table-product" class="table table-hover box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 45px; padding: 5px; vertical-align: middle; text-align: center">STT</th>
                                            <th style="min-width: 90px; word-break: break-word">Mã sản phẩm </th>
                                            <th style="width: auto; min-width: 270px">Tên sản phẩm</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">Số lượng</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">Số lượng thực tế</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">ĐVT</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_price">Giá </th>
                                            <th class="product_total" style="min-width: 80px;word-break: break-word">Tổng tiền</th>
                                            <th class="product_comment" style="word-break: break-word;min-width: 90px">Ghi chú</th>
                                            <th style="min-width: 65px;word-break: break-word; text-align: center">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                             $countStt = $orderImport->details->count();
                                        @endphp
                                        @foreach ($orderImport->details as $item)
                                            <tr class="ordered-products">
                                                <td style="text-align: center">{{ $i++ }}</td>
                                                <td class="overflow_prevent">{{ $item->product_code ?? $item->product->sku }}</td>
                                                <td>
                                                    @if($orderImport->status != 3)
                                                        <a type="button" onclick="showPopupChooseSupplier($(this), '{{ $item->id }}')">{{  $item->product_name }}</a>

                                                    @else
                                                        {{  $item->product_name }}
                                                    @endif
                                                </td>
                                                <td class="product_qty">
                                                    @if($orderImport->status != 3)
                                                        <a style="font-weight: bold" data-perm="order_import:edit_info"
                                                           perm-type="disable" href="#"
                                                           class="edit-item-detail"
                                                           {{ !$editable ? 'order-lock=disable' : "" }}
                                                           data-value="{{ $item->qty_order }}" data-name="qty_order"
                                                           data-step="any"
                                                           data-type="text" data-min="0"
                                                           data-unit_type="{{ $item->unit_type ?? '' }}"
                                                           data-pk="{{ $item->id }}"
                                                           data-url="{{ route("order_import.update") }}"
                                                           data-title="Số lượng"> {{ $item->qty_order }}</a>
                                                    @else
                                                        {{ $item->qty_order }}
                                                    @endif
                                                </td>
                                                <td class="product_qty">
                                                    @if($orderImport->status != 3)
                                                        <a data-perm="order_import:edit-detail_exportquantity"
                                                           perm-type="disable" href="#"
                                                           class="edit-item-detail"
                                                           {{ !$editable ? 'order-lock=disable' : "" }}
                                                           data-value="{{ $item->qty_reality }}" data-name="qty_reality"
                                                           data-type="text" data-min="0"
                                                           data-pk="{{ $item->id }}"
                                                           data-url="{{ route("order_import.update") }}"
                                                           data-title="Số lượng thực tế"> {{ $item->qty_reality }}</a>
                                                    @else
                                                        {{ $item->qty_reality }}
                                                    @endif
                                                </td>
                                                <td class="product_price">{{ $item->unit_name ?? ''}}
                                                <td class="product_price">
                                                    @if($orderImport->status != 3)
                                                        <a data-perm="order_import:edit_price"
                                                           perm-type="disable" href="#"
                                                           class="edit-item-price"
                                                           {{ !$editable ? 'order-lock=disable' : "" }}
                                                           data-value="{{ $item->product_price }}"
                                                           data-name="product_price" data-type="text"
                                                           data-min="0"
                                                           data-pk="{{ $item->id }}"
                                                           data-url="{{ route("order_import.update") }}"
                                                           data-title="{{ sc_language_render('product.price') }}">{{ sc_currency_render($item->product_price, 'VND') }}</a>
                                                    @else
                                                        {{ $item->product_price }}
                                                    @endif
                                                </td>
                                                <td class="product_total item_id_{{ $item->id }}">{{ sc_currency_render( round($item->amount) ?? 0, 'vnd') }}</td>
                                                <td class="product_comment" style="overflow: hidden">
                                                    <a data-perm="order_import:edit_info" perm-type="disable" href="#"
                                                        class="edit-item-comment"
                                                        {{ !$editable ? 'order-lock=disable' : "" }}
                                                        data-value="{{ $item->comment ?? ''}}"
                                                        data-name="comment" data-type="text"
                                                        data-emptytext="Trống"
                                                        data-pk="{{ $item->id ?? ''}}"
                                                        data-url="{{ route("order_import.update") }}"
                                                        data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                </td>
                                                <td style="text-align: center">
                                                    @if($orderImport->status != 3)
                                                        <span data-perm="order_import:edit_info"
                                                              onclick="deleteItem($(this),'{{ $item->id }}');"
                                                              {{ !$editable ? 'order-lock=hide' : "" }}
                                                              class="btn btn-danger btn-xs" data-title="Delete"><i
                                                                    class="fa fa-trash" aria-hidden="true"></i></span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        @if($orderImport->status != 3)
                                            <tr id="add-item" class="not-print">
                                                <td colspan="10">
                                                    <button data-perm="order_import:edit_info" type="button"
                                                            class="btn btn-flat btn-success"
                                                            id="add-item-button"
                                                            {{ !$editable ? 'order-lock=hide' : "" }}
                                                            title="{{sc_language_render('action.add') }}"><i
                                                                class="fa fa-plus"></i> {{ sc_language_render('action.add') }}
                                                    </button>
                                                    &nbsp;&nbsp;&nbsp;<button style="display: none; margin-right: 50px"
                                                                              type="button" class="btn btn-flat btn-warning"
                                                                              id="add-item-button-save" title="Save"><i
                                                                class="fa fa-save"></i> {{ sc_language_render('action.save') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
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
                                                                 data-value="{{ $orderImport->note ?? ''}}"
                                                                 data-name="comment"
                                                                 {{ !$editable ? 'order-lock=disable' : "" }}
                                                                 data-type="textarea"
                                                                 data-emptytext="Trống"
                                                                 data-pk="{{ $orderImport->id }}"
                                                                 data-url="{{ route("admin_order.update") }}"
                                                                 data-title="{{ sc_language_render('order.admin.comment') }}"> {{ $orderImport->note ?? 'Trống'}}</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End comment --}}
                    {{-- total --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped">
                                <tr>
                                    <td class="td-title">{{ sc_language_render('admin.order_total_price') }}:</td>
                                    <td class="text-right td-title data-total">{{ sc_currency_render( round($orderImport->total ?? 0), 'vnd') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End total --}}
                </div>
                {{--                Show hoàn trả đơn hàng--}}
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="tableFixHead">
                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="9">Lịch sử trả hàng</th>
                                </tr>
                                <tr>
                                    <th style="width: 40px; text-align: center">{{ sc_language_render('order.admin.no') }}</th>
                                    <th style="">{{ sc_language_render('order.admin.employe') }}</th>
                                    <th style="">{{ sc_language_render('product.name') }}</th>
                                    <th class="product_qty" style="word-break: normal">Số lượng ban đầu</th>
                                    <th class="product_qty" style="word-break: normal">{{ sc_language_render('order.admin.return_no') }}</th>
                                    <th class="product_price">{{ sc_language_render('product.price') }}</th>
                                    <th class="product_total">{{ sc_language_render('product.total_price') }}</th>
                                    <th class="product_comment">{{ sc_language_render('order.admin.return_date') }}</th>
                                    <th class="undo_detail">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($orderImport->return as $return)
                                    @php
                                        $price_product = $return->product_price != null ? $return->product_price : ($return->detail->product_price ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="width: 120px">{{ $return->getEditor() }}</td>
                                        <td>{{ $return->product_name }}
                                        <td class="product_qty">{{ ($return->qty_original + $return->qty_return) ?? 0 }}</td>
                                        <td class="product_price">{{ $return->qty_return ?? 0 }}</td>
                                        <td class="product_price">{{ sc_currency_render((float)$price_product ?? 0 , 'VND') }}</td>
                                        <td class="product_price">{{ sc_currency_render(($return->qty_return ?? 0) * ((float)$price_product ?? 0), 'VND') }}</td>
                                        <td class="product_comment">{{ formatDateVn($return->created_at, true) ?? 'Trống' }}</td>
                                        <td class="undo_detail">
                                            @if($orderImport != 3)
                                                <span onclick="undoReturnOrder('{{$return->id}}', '{{$return->import_detail_id}}')" class="btn btn-sm btn-sm btn-warning"><i class="fas fa-undo text-white"></i></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="btn-group float-right" style="margin-left: 10px">
                        <a data-perm="order:print"
                           href="{{ sc_route_admin('admin_order.print_return', ['id' => $orderImport->id]) }}" target="_blank"
                           class="btn btn btn-primary"><i
                                    class="fa fa-print"></i>&nbsp;{{ sc_language_render('admin_order.print_return') }}
                        </a>
                    </div>
                    {{-- //End history --}}

                </div>
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
                                @forelse($orderImport->history ?? [] as $k => $v)
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
                @php
                    $htmlSelectProduct =
                            '<tr class="select-product">
                                <td class="check-num-order" style="text-align:center"></td>
                                <td><input type="text" readonly="readonly" class="add_sku form-control" value=""></td>
                                <td id="add_td">
                                    <select onChange="selectProduct($(this));" name="product_id[]" id="add_id" class="add_id form-control flexselect" tabindex="2">
                                        <option value="" selected></option>';
                                        if(isset($products)) {
                                            foreach ($products as $pId => $product) {
                                                $htmlSelectProduct .='<option value="'.$product->id.'">'.$product->name.' - '.($product->unit->name ?? '').'</option>';
                                            }
                                        }
                    $htmlSelectProduct .='
                                    </select>
                                </td>
                                <td><input type="number" onChange="update_total($(this));"  class="add_qty form-control" name="qty_order[]" data-minimum_qty_norm="0" data-unit_type="" data-check="3" value=""></td>
                                <td><input type="number" readonly class="add_qty_reality form-control" name="add_qty_reality[]" value=""></td>
                                <td><input type="text" readonly class="add_unit form-control"  value=""></td>
                                <td><input type="number" readonly onKeyup="update_total_by_price($(this));" id="add_price" class="add_price form-control" name="product_price[]" value="0"></td>
                                <td><input type="number" readonly class="add_total form-control" value="0"></td>
                                <td><input class="add_comment form-control" name="comment[]" value="" autocomplete="off"></td>
                                <td style="text-align:center"><button onClick="$(this).parent().parent().remove(); checkRemoveDOM(); checkNumberOrder()" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                             </tr>';
                    $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
                @endphp
            </div>
        </div>
    </div>
    <!-- Modal -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="form_update_import_price" action="{{ sc_route_admin('order_import.update_import_price', $orderImport->id) }}" method="post">
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
                        <button type="button" id="btn_submit_update_price" class="btn btn-primary" onclick="updateNewPriceList('{{ $orderImport->id }}')">Đồng
                            ý
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include($templatePathAdmin.'screen.warehouse.import.includes.modal_change_product')
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
    <script type="text/javascript">
        function alertJsCustom(type = 'error', msg = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
            Toast.fire({
                type: type,
                title: msg
            })
        }

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
            document.title = "DonNhapHang-"  + now;
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

        $('#table-product').bind('keydown', function(e) {
            if (e.which === 37 || e.which === 38 || e.which === 9 ||
                e.which === 39 || e.which === 40 || e.which === 13) {
                e.preventDefault();
            }
        });

        $('table.table-product').keydown(function(e){
            var $active = $('input:focus,select:focus',$(this));
            var $next = null;
            var focusableQuery = 'input:visible,select:visible,textarea:visible';
            var position = parseInt( $active.closest('td').index()) + 1;
            var tr_position = parseInt( $active.closest('tr').index());
            var tr_length = 0;
            var od_length = 0;

            $('.select-product').each(function () {
                tr_length++;
            });
            $('.ordered-products').each(function () {
                od_length++;
            });

            switch(e.keyCode) {
                case 37: // Left
                    $next = $active.parent('td').prev().find(focusableQuery);
                    if ($next.hasClass('add_total')) {
                        $next = $active.closest('td').prev().prev().find(focusableQuery);
                    }
                    if ($next.hasClass('add_sku')) {
                        $next = $active.closest('td').find(focusableQuery);
                    }
                    if ($next.hasClass('add_unit')) {
                        $next = $active.closest('td').prev().prev().prev().find(focusableQuery);
                    }
                    break;
                case 38: // Up
                    $next = $active
                        .closest('tr')
                        .prev()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 39: // Right
                    $next = $active.closest('td').next().find(focusableQuery);
                    if ($next.hasClass('add_total')) {
                        $next = $active.closest('td').next().next().find(focusableQuery);
                    }
                    if ($next.hasClass('add_qty_reality')) {
                        $next = $active.closest('td').next().next().next().find(focusableQuery);
                    }
                    break;
                case 40: // Down
                    $next = $active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 13: // Enter
                    $next = $active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    if (tr_position == tr_length+od_length-1) {
                        $('#add-item-button').trigger('click');
                    }
                    break;
                case 9: // Tab
                    $next = $active.closest('td').next().find(focusableQuery);
                    if ($next.hasClass('add_total') || $next.hasClass('btn-danger')) {
                        $next = $active.closest('td').next().next().find(focusableQuery);
                    }
                    if ($next.hasClass('add_qty_reality')) {
                        $next = $active.closest('td').next().next().next().find(focusableQuery);
                    }
                    break;
            }
            if($next && $next.length) {
                $next.focus();
            }
        });
    </script>
    <!-- /Handle navigation with key arrow -->

    <script type="text/javascript">

        function update_total(e) {
            node = e.closest('tr');
            var product_unit_type = node.find('.add_qty').data('unit_type');
            var product_qty_reality = node.find('.add_qty_reality');
            var product_minimum_qty_norm = node.find('.add_qty').data('minimum_qty_norm');
            var qty = node.find('.add_qty').eq(0).val();
            var price = node.find('.add_price').eq(0).val();
            node.find(".add_qty").data("check","");

            if (Number(price) < 0) {
                node.find('.add_price').val(0);
            } else if(Number(qty) == 0) {
                node.find('.add_qty').data('check', 3);
                node.find('.add_qty').css('border', '1px solid red');
            } else if (Number(qty)<Number(product_minimum_qty_norm)) {
                node.find('.add_qty').css('border', '1px solid red');
                node.find('.add_qty').data('check', 2);
                alertJs('error', 'Số lượng nhập vào đang nhỏ hơn định lượng tối thiểu (' + product_minimum_qty_norm + '), vui lòng kiểm tra lại!');
            } else if (product_unit_type==1 && !Number.isInteger(Number(qty))) {
                node.find('.add_qty').css('border', '1px solid red');
                node.find('.add_qty').data('check', 0);
                alertJs('error', 'Vui lòng nhập số lượng sản phẩm là số nguyên!');
            } else {
                node.find('.add_qty').css('border', '');
                node.find('.add_qty').data('check', 1);
            }

            var f_qty = node.find('.add_qty').eq(0).val();
            var f_price = node.find('.add_price').eq(0).val();
            product_qty_reality.val(node.find('.add_qty').val())
            node.find('.add_total').eq(0).val(f_qty * f_price);
        }

        function update_total_by_price(e) {
            node = e.closest('tr');
            var qty = node.find('.add_qty').eq(0).val();
            var price = node.find('.add_price').eq(0).val();

            if(Number(price)<0) {
                node.find('.add_price').val(0);
            }
            node.find('.add_total').eq(0).val(qty * price);
        }

        // Check value có tồn tại trong ds sản phẩm
        function checkExists(inputValue) {
            var x = document.getElementById("product-list");
            var i;
            var flag;
            for (i = 0; i < x.options.length; i++) {
                if(inputValue == x.options[i].value){
                    flag = true;
                }
            }
            return flag;
        }

        function selectProduct(e) {
            let node = e.closest('tr');
            let product_id = e.val();
            let import_id = '{{ $orderImport->id }}';
            node.find('.add_qty').data('check', 3); // Set trạng thái lại trạng thái khi số lượng 0
            node.find('.add_qty').css("border", ""); // Ẩn border red
            // if (checkExists(value) == true) {
            $.ajax({
                url: '{{ sc_route_admin('order_import.get_productInfo_price') }}',
                type: "get",
                dateType: "application/json; charset=utf-8",
                data: {
                    product_id: product_id,
                    import_id: import_id
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (returnedData) {
                    $('#loading').hide();
                    if (returnedData.error == 1) {
                        node.remove();
                        $('#loading').hide();
                        return alertJsCustom('error', returnedData.msg);
                    }
                    node.find('.add_sku').val(returnedData.sku);
                    node.find('.add_qty').val('');
                    node.find('.add_qty_reality').eq(0).val(0);
                    node.find('.add_price').val(returnedData.price);
                    node.find('.add_unit').val(returnedData.unit_name);
                    node.find('.add_qty').data('unit_type', returnedData.unit_type);
                    node.find('.add_comment').val('');
                    node.find('.add_total').eq(0).val(0);
                }
            });
        }

        function checkNumberOrder() {
            var selected = [];
            let count = {{ $countStt }};
            $('.check-num-order').each(function (key) {
                $(this).text(key + 1 + count)
            });
            return selected;
        }

        $('#add-item-button').click(function () {
            var html = '{!! $htmlSelectProduct !!}';
            $('#add-item').before(html);
            $("select.flexselect").flexselect({ hideDropdownOnEmptyInput: true });
            // $("select.flexselect").flexselect();
            $("input:text:enabled:first").focus();
            $('#add-item-button-save').show();
            $('.add_id').focus();
            checkNumberOrder()
        });

        $('#add-item-button-save').click(function (event) {
            let checks = [];
            let products = [];

            $('.add_qty').each(function () {
                checks.push($(this).data('check'));
            });
            $('.add_id').each(function () {
                products.push($(this).val());
            });

            if (products.includes('')) {
                alertJs('error', 'Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!');
            } else if (checks.includes(0)) {
                alertJs('error', 'Vui lòng nhập số lượng sản phẩm là số nguyên!');
            } else if (checks.includes(3)) {
                alertJs('error', 'Số lượng phải lớn hơn 0, vui lòng kiểm tra lại chi tiết hóa đơn!');
            } else {
                $('#add-item-button').prop('disabled', true);
                $.ajax({
                    url: '{{ route("order_import.add_new_item") }}',
                    type: 'post',
                    dataType: 'json',
                    data:
                        $('form#form-add-item').serialize()
                    ,
                    beforeSend: function () {
                        $('#loading').show();
                    }
                    ,success: function (result) {
                        $('#loading').hide();
                        if (parseInt(result.error) == 0) {
                            location.reload();
                        } else {
                            alertJs('error', result.msg);
                            $('#add-item-button').prop('disabled', false);
                        }
                    }
                });
            }
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
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
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
                validate: function (value) {
                    var product_status = $(this).editable().data('status');
                    var product_unit_type = $(this).editable().data('unit_type');
                    var product_minimum_qty_norm = $(this).editable().data('minimum_qty_norm');

                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                    if (Number(value) < 0) {
                        return 'Số lượng sản phẩm không được âm!';
                    }
                    if (product_status == 0) {
                        return 'Sản phẩm tạm hết hàng không thể chỉnh sửa số lượng, vui lòng kiểm tra lại!'
                    }
                    if (product_status == '') {
                        return 'Sản phẩm đã bị xóa không thể chỉnh sửa số lượng, vui lòng kiểm tra lại!'
                    }
                    if (Number(product_unit_type)==1 && !Number.isInteger(Number(value))) {
                        return 'Số lượng sản phẩm phải là số nguyên!';
                    }
                    if (Number(value) < Number(product_minimum_qty_norm)) {
                        return 'Số lượng nhập vào đang nhỏ hơn định lượng tối thiểu (' + product_minimum_qty_norm + ')';
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


        {{-- sweetalert2 --}}
        function deleteItem(element, id) {
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
                            url: '{{ route("order_import.delete_detail") }}',
                            data: {
                                'pId': id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    element.parents().eq(1).remove()
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
                ) { }
            })
        }

        {{--/ sweetalert2 --}}


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        $('#btn_submit_update_price').click(function () {
            $('#loading').show();
            $('#form_update_import_price').submit();
            $('#exampleModal').hide();
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

        function checkRemoveDOM() {
            if ($('#add_td').length == 0) {
                $('#add-item-button-save').hide();
            }
        }

        $('[name="supplier_id"]').change(function () {
            let import_id =  $('[name="import_id"]').val();
            let supplier_id =  $('[name="supplier_id"]').val();
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Bạn có muốn thay đổi nhà cung cấp!',
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
                            url: '{{ route("admin_order.update_customer_info") }}',
                            data: {
                                import_id: import_id,
                                supplier_id: supplier_id,
                                _token: '{{ csrf_token() }}'
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

        });

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
                            url: '{{ route("order_import.undo_import_detail") }}',
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

        const formatter = new Intl.NumberFormat('en-US', {
        });
        
        function showPopupChooseSupplier(e, import_detail_id) {
            if(!import_detail_id) {
                alertMsg('error', 'Chi tiết không tồn tại', '{{ sc_language_render('action.warning') }}');
                return;
            }
            $.ajax({
                url: '{{ sc_route_admin('order_import.get_data_change_product') }}',
                method: "POST",
                data: {
                    detail_id: import_detail_id,
                    _token: "{{csrf_token()}}"
                },
                dataType: "json",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function(){
                    $('#loading').hide();
                },
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', 'Lỗi không tìm thấy các nhà cung cấp sản phẩm này!', '{{ sc_language_render('action.warning') }}');
                        return;
                    } else {
                        if (data) {
                            $('#text_name_product').text(data.product_name)
                            $('#text_price').text(data.product_price)
                            $('#text_name_supplier').html('{!! $orderImport->supplier_name !!}')
                            $('#detail_id').val(data.detail_id);
                            let html = '';
                            for (let item of data.priceForSupplier) {
                                html += '<tr>\n' +
                                        '<td><input style="width: 50px;height: 25px;" type="radio" name="price_detail_id" value="'+item.price_detail_id+'"></td>\n' +
                                        '<td>' + item.supplier_name + '</td>\n' +
                                        '<td>' + formatter.format(item.import_price) + '₫</td>\n' +
                                    '</tr>';
                            }
                            $('#detail_product_supplier').html(html);
                        }
                    }
                    $("#changeProductItemModal").attr("class", "modal fade show");
                }
            });

            $('#close_modal_change_product').on('click', function () {
                $("#changeProductItemModal").removeClass("show");
            });
        }
        function printPdf(ids) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn để xuất PDF', 'Vui lòng chọn it nhất 1 bản ghi trước khi xuất PDF');
                return;
            }
            let href = '{{ sc_route_admin('order_import.print') }}?&ids=' + ids ;
            $("#loading").show();
            printPage(href)
        }
    </script>
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
