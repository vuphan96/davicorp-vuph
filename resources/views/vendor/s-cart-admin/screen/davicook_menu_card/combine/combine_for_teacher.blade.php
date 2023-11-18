@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        @php
            $now = date('d/m/Y', strtotime(now()));
        @endphp
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">
                        Mã Phiếu #{{ $menuEstCard->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ session('nameUrlDavicookMenuCard') ?? sc_route_admin('admin.davicook_menu_card.index') }}"
                               class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                    </div>
                </div>
                <form class="row" id="order_edit_form form-main" method="post"
                      action="{{ sc_route_admin('admin.davicook_order.order_update') }}">
                    @method('put')
                    @csrf
                    <div class="col-sm-8 mt-3">
                        <table class="table box-body text-wrap table-bordered">
                            <tr>
                                <td class="td-title">Tên Phiếu:</td>
                                <td>
                                    {{ $menuEstCard->card_name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_name') }}:</td>
                                <td>
                                    {{ $menuEstCard->customer_name }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày bắt đầu phiếu:</td>
                                <td>
                                    {{ date('d/m/Y', strtotime($menuEstCard->start_date ?? '')) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày kết thúc phiếu:</td>
                                <td>
                                    {{ date('d/m/Y', strtotime($menuEstCard->end_date ?? '')) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Số tuần trên phiếu:</td>
                                <td>
                                    {{ $menuEstCard->week_no }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Trạng thái phiếu:</td>
                                <td>
                                    {{ $statusSync[$menuEstCard->status_sync] }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Loại phiếu:</td>
                                <td>
                                    {{ $menuEstCard->is_combine == 1 ? 'Phiếu gộp' : 'Phiếu thường' }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Tổng số lượng suất ăn:</td>
                                <td>{{ $menuEstCard->total_number_of_servings }}</td>
                            </tr>
                            <tr>
                                <td class="td-title">Tổng tiền giá vốn trên phiếu:</td>
                                <td>{{ number_format($menuEstCard->total_cost). '₫' }} </td>

                            </tr>
                            <tr>
                                <td class="td-title">Ngày tạo phiếu:</td>
                                <td>{{ date("d/m/Y H:i:s", strtotime($menuEstCard->created_at)) }} </td>
                            </tr>
                        </table>
                    </div>
                </form>
                @foreach($menuEstCard->details->sortBy('date') as $key => $menuCard)
                    <div class="main-order ml-2 mt-5">
                        <div class="title-sub-card d-flex mb-2 mt-2 pr-2">
                            <div class="col-sm-3">
                                <lable class="font-weight-bold">Ngày giao hàng : </lable>
                                {{ date('d/m/Y', strtotime($menuCard->date ?? '')) }}
                            </div>
                            <div class="col-sm-3">
                                <lable class="font-weight-bold">Ngày trên hóa đơn : </lable>
                                {{ date('d/m/Y', strtotime($menuCard->bill_date ?? '')) }}
                            </div>
                            <div class="col-sm-3">
                                <lable class="font-weight-bold">Số lượng suất ăn : </lable>
                                {!! $menuCard->number_of_servings !!}
                            </div>
                            <div class="col-sm-3" style="text-align: right">
                                <input type="button" class="btn btn-info btn-show-detail-dish" onclick="showAndHidenDetail($(this), '{{ $menuCard->id }}')" data-flag="1" value="Hiện nguyên liệu">
                            </div>
                        </div>
                    </div>
                    <form id="form_add_dish_for_menu_card_{{ $menuCard->id }}" action="" method="">
                        @csrf
                        <input type="hidden" name="menu_card_id" value="{{ $menuCard->id }}">
                        <input type="hidden" name="customer_id" value="{{ $menuEstCard->customer_id }}">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table class="table box-body text-wrap table-bordered table-product">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; min-width: 102px" class="dish_code">Ngày</th>
                                                <th style="text-align: center; width: auto" class="dish_code">Mã món ăn</th>
                                                <th style="text-align: center; min-width: 185px;" class="dish_name">Tên món ăn</th>
                                                <th style="text-align: center; min-width: 102px" class="dish_code">Ngày</th>
                                                <th style="text-align: center; min-width: 165px" class="product_name">Tên nguyên liệu</th>
                                                <th style="text-align: left; min-width: 165px" class="bom">Định lượng</th>
                                                <th style="text-align: center; min-width: 140px" class="total_bom">Nguyên liệu suất</th>
                                                <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                                <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền Cost</th>
                                                <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($menuCard->children->sortBy('created_at')->groupBy('dish_id') as $dishId => $dish)
                                                <tr>
                                                    <td>
                                                        {{ date('d/m/Y', strtotime($dish->first()->date_for_dish ?? '')) }}
                                                    </td>
                                                    <td>{{ $dish->first()->dish_code ?? '' }}</td>
                                                    <td>
                                                        {{ $dish->first()->dish_name ?? '' }}
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{ $item->date_for_product }} </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{ date('d/m/Y', strtotime($item->date_for_product != '' ? $item->date_for_product :$item->date_for_dish )) }} </td>                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{$item->bom}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{!! number_format($item->total_bom, 2) !!} {{ $item->product_unit ?? ''}}</td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{ number_format($item->import_price)}} </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{ number_format($item->amount_of_product_in_order) . '₫'}} </td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                        <p class="show-total_{{ $menuCard->id }}">{{ number_format($dish->sum('amount_of_product_in_order')) . '₫'}}</p>
                                                    </td>
                                                    <td class="detail_parent_{{ $menuCard->id }}">
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td>{{ $item->comment ?? '' }}&nbsp</td>
                                                                </tr>
                                                            @endforeach
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="sub-total-cost">
                        <div class="btn-group float-right">
                            <h6 style="font-weight: bold">Tổng tiền giá vốn:</h6>
                            <input type="text" class="sub_total_cost sub_total_cost_key_'+key+'" readonly
                                   value="{{ number_format($menuCard->sub_total_cost). '₫' }}"
                                   style="font-weight: bold; margin: 0px 13px">
                        </div>
                    </div>
                    <br>
                @endforeach
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

        .td-parent {
            border: none !important;
            border-collapse: collapse !important;
            padding: 0 !important;
        }

        .btn-show-detail-dish{
            width: 140px;
        }

        th {
            white-space: nowrap;
        }

        .td-title {
            width: 35%;
            font-weight: bold;
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

        .input-readonly {
            margin-bottom: 0px!important;
            background-color: #e9ecef;
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
            color: #D26C56;
            font-weight: bold;
        }

        .noti-order h5 {
            margin: 7px 0;
        }

        .dish_no {
            display: none;
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
    <script>
        function showAndHidenDetail(element, menuCardId) {
            let flag = element.data('flag');
            if (flag == 1) {
                element.data('flag', 2);
                element.removeClass('btn-info');
                element.addClass('btn-warning');
                element.val('Ẩn nguyên liệu');
                $('.detail_info_'+menuCardId).removeClass('d-none');
                $('.detail_parent_'+menuCardId).addClass('td-parent')
                $('.show-total_'+menuCardId).addClass('d-none')
            } else {
                element.data('flag', 1);
                element.addClass('btn-info');
                element.removeClass('btn-warning');
                element.val('Hiện nguyên liệu');
                $('.detail_info_'+menuCardId).addClass('d-none');
                $('.detail_parent_'+menuCardId).removeClass('td-parent')
                $('.show-total_'+menuCardId).removeClass('d-none')
            }
        }
    </script>
@endpush
