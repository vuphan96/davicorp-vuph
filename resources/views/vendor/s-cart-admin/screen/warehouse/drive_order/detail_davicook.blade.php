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
                            <a href="{{ sc_route_admin('driver.list_drive_order_davicook') }}" class="btn btn-flat btn-default"><i class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
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
                                <td class="td-title">Số lượng suất ăn chính:</td>
                                <td>
                                    {!! $order->number_of_servings !!}
                                </td>
                            </tr>

                            <tr>
                                <td class="td-title">Số lượng suất ăn bổ sung:</td>
                                <td>
                                    {!! $order->number_of_extra_servings !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Số lượng suất ăn thực tế:</td>
                                <td>
                                    {!! $order->number_of_reality_servings !!}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.explain') }}</td>
                                <td>
                                    {!! $order->explain !!}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Nhân viên giao hàng:</td>
                                <td>
                                    <a href="#" class="edit-change-drive"
                                       data-name="drive_id" data-type="select"
                                       data-source="{{ json_encode($drivers) }}" data-pk="{{ $order->id }}"
                                       data-ids="{{ $order->id }}"
                                       data-value="{!! $order->drive_id !!}"
                                       data-url="{{ route("driver.change_drive_order_davicook") }}"
                                       data-title="Thay đổi NV giao hàng">{{ $order->drive_name }}</a>
                                </td>

                            </tr>
                            <tr>
                                <td class="td-title">Trạng thái giao hàng:</td>
                                <td>
                                    {{ $delivery_status[$order->delivery_status] }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> {{ sc_language_render('admin.delivery_time') }}</td>
                                <td>{{ date('d/m/Y', strtotime($order->delivery_date ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title"></i>Ngày trên hóa đơn:</td>
                                <td>{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</td>
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
                                                        <td>{{$item->bom}} {{ $item->product_unit ?? ''}}</td>
                                                        <td>{{$item->qty}}</td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}">
                                                            {{ number_format(($item->qty*$item->bom), 2) }}
                                                            {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td>{{ $item->comment }}</td>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            @foreach ($order->details->where('type',0) as $item)
                                                @if ($item->dish_id == $v->dish_id &&  $current_detail_id !== $item->id)
                                                    <tr>
                                                        <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }};
                                                                text-underline-offset: 5px;
                                                                text-decoration: {{ $item->product_type == 0 ? 'underline #cccccc dashed' : 'none' }}">
                                                            {{ $item->product_name ?? 'Nguyên liệu đã bị xóa' }}
                                                        </td>
                                                        <td>{{$item->bom}} {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td>{{$item->qty}}</td>
                                                        <td class="product_total_bom item_id_{{ $item->id }}" >
                                                            {{ number_format($item->qty*$item->bom, 2) }}
                                                            {{ $item->product_unit ?? ''}}
                                                        </td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td>{{ $item->comment }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
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
                                                            {{ roundTotalBom($item->total_bom, $item->product->unit->type ?? 0) }}
                                                        </td>
                                                        <td>{{ $item->product_unit ?? ''}}</td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td>{{ $item->comment }}
                                                        </td>
                                                    @endif
                                                @endforeach
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
                                                                {{ roundTotalBom($item->total_bom, $item->product->unit->type ?? 0) }}
                                                        </td>
                                                        <td>{{ $item->product_unit ?? ''}}</td>
                                                        <td class="import_price">{{ sc_currency_render($item->import_price ?? 0, 'vnd') }}</td>
                                                        <td class="amount_of_product_in_order product_total_cost item_total_cost_id_{{ $item->id }}">{{ sc_currency_render(round($item->amount_of_product_in_order) ?? 0, 'vnd') }}</td>
                                                        <td>{{ $item->comment }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
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
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order->returnHistory as $k => $item)
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
                                        {{ $order->comment }}
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
                                    <td class="text-right td-title data-total-cost">{{ sc_currency_render(round($order->details->sum('amount_of_product_in_order')) ?? 0, 'vnd') }}</td>
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
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
            all_editable();
        });
        function all_editable() {
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };
            $('.edit-change-drive').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }
    </script>
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
