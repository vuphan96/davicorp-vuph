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
                        <div style="margin-right: 10px" type="button" class="btn-group float-right" data-toggle="modal"
                             data-target="#exampleModal">
                            <a href="{{ route("admin.davicook_menu_card.update_import_price", $menuEstCard->id) }}" class="btn btn-flat btn btn-primary" id="btn_update_price"><i
                                        class="fas fa-pen"></i>&nbsp;Cập nhật giá
                            </a>
                        </div>
                    </div>
                </div>
                <form class="row" id="order_edit_form form-main" method="post"
                      action="{{ sc_route_admin('admin.davicook_order.order_update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $menuEstCard->id }}">
                    <input type="hidden" name="customer_id" value="{{ $menuEstCard->customer_id }}">
                    <input type="hidden" name="type_object" value="{{ $menuEstCard->type_object }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table box-body text-wrap table-bordered">
                            <tr>
                                <td class="td-title">Tên Phiếu:</td>
                                <td>
                                    <a href="#"
                                       class="edit-item-comment"
                                       data-value="{{ $menuEstCard->card_name ?? ''}}"
                                       data-name="card_name" data-type="text"
                                       data-pk="{{ $menuEstCard->id ?? ''}}"
                                       data-emptytext="Trống"
                                       data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                       data-title="Tên phiếu">{{ $menuEstCard->card_name }}</a>
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
                                    <a href="#" class="updateDate start-date" data-name="start_date" data-type="date"
                                       data-value="{!! $menuEstCard->start_date !!}"
                                       data-emptytext="Trống"
                                       data-pk="{{ $menuEstCard->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                       data-title="Sửa ngày bắt đầu phiếu">{{ date('d/m/Y', strtotime($menuEstCard->start_date ?? '')) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày kết thúc phiếu:</td>
                                <td>
                                    <a href="#" class="updateDate end-date" data-name="end_date" data-type="date"
                                       data-value="{!! $menuEstCard->end_date !!}"
                                       data-emptytext="Trống"
                                       data-pk="{{ $menuEstCard->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                       data-title="Sửa ngày kết thúc phiếu">{{ date('d/m/Y', strtotime($menuEstCard->end_date ?? '')) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Số tuần trên phiếu:</td>
                                <td>
                                    <a href="#" class="updateWeekNo" data-name="week_no" data-type="text"
                                       data-value="{!! $menuEstCard->week_no !!}"
                                       data-emptytext="Trống"
                                       data-pk="{{ $menuEstCard->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                       data-title="Số tuần trên phiếu">{{ $menuEstCard->week_no }}</a>
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
                                <lable>Ngày giao hàng</lable>
                                <input type="hidden" class="sub-date" readonly name="date"
                                       value="{{ date('d/m/Y', strtotime($menuCard->date ?? '')) }}"
                                       id="date_sub_card_{{ $menuCard->id }}">
                                <a href="#" class="updateDate" data-name="date" data-type="date"
                                   data-value="{!! $menuCard->date !!}"
                                   data-emptytext="Trống"
                                   data-pk="{{ $menuCard->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                   data-title="{{ sc_language_render('admin.delivery_time') }}">{{ date('d/m/Y', strtotime($menuCard->date ?? '')) }}</a>
                            </div>
                            <div class="col-sm-3">
                                <lable>Ngày trên hóa đơn</lable>
                                <a href="#" class="updateDate" data-name="bill_date" data-type="date"
                                   data-value="{!! $menuCard->bill_date !!}"
                                   data-emptytext="Trống"
                                   data-pk="{{ $menuCard->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                   data-title="Ngày trên hóa đơn">{{ date('d/m/Y', strtotime($menuCard->bill_date ?? '')) }}</a>
                            </div>
                            <div class="col-sm-3">
                                <lable>Số lượng suất ăn</lable>
                                <input type="hidden" id="number_of_servings_sub_card_{{ $menuCard->id }}" value=" {{ $menuCard->number_of_servings }}">
                                <a href="#" class="updateNumberOfServings" id="number_of_servings_{{ $menuCard->id }}"
                                   data-name="number_of_servings" data-type="number" data-step="0.01" data-min="0"
                                   data-pk="{{ $menuCard->id }}"
                                   data-url="{{ route("admin.davicook_menu_card.change_number_of_servings") }}"
                                   data-title="Số lượng suất ăn">{!! $menuCard->number_of_servings !!}</a>
                            </div>
                            <div class="col-sm-2" style="text-align: right">
                              <input type="button" class="btn btn-info btn-show-detail-dish" data-flag="1" value="Hiện nguyên liệu"
                                     onclick="showAndHidenDetail($(this),'{{ $menuCard->id }}');">
                            </div>
                            <div class="col-sm-1" style="text-align: right">
                                <input type="button" class="btn btn-danger btn-remove-item-card" value="Xóa phiếu"
                                       onclick="deleteMenuCardDetail(null,'{{ $menuCard->id }}', '{{ route("admin.davicook_menu_card.detail_card_delete") }}');">
                            </div>
                        </div>
                    </div>
                    <form id="form_add_dish_for_menu_card_{{ $menuCard->id }}" action="" method="">
                        @csrf
                        <input type="hidden" name="menu_card_id" id="menu_card_id_{{ $menuCard->id }}" value="{{ $menuCard->id }}">
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
                                                <th style="text-align: center; min-width: 175px;" class="dish_name">Tên món ăn</th>
                                                <th style="text-align: center; max-width: 120px; min-width: 120px" class="dish_name">Loại</th>
                                                <th style="text-align: center; min-width: 102px; max-width: 102px; width: 102px" class="dish_code">Ngày</th>
                                                <th style="text-align: center; min-width: 175px; max-width: 175px; width: 175px" class="product_name">Tên nguyên liệu</th>
                                                <th style="text-align: left; min-width: 155px; max-width: 155px; width: 155px" class="bom">Định lượng</th>
                                                <th style="text-align: center; width: 140px; max-width: 140px; width: 155140" class="total_bom">Nguyên liệu suất</th>
                                                <th style="text-align: right; max-width: 125px; min-width: 125px; width: 125px" class="import_price">Giá nhập</th>
                                                <th style="text-align: right; min-width: 135px; max-width: 135px; width: 135px" class="amount_of_product_in_order">Tổng tiền Cost</th>
                                                <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                                <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($menuCard->children->sortBy('created_at')->groupBy('dish_id') as $dishId => $dish)
                                                <input type="hidden" class="select_add_dish_id_{{ $menuCard->id }}" value="{{ $dishId }}">
                                                <tr>
                                                    <td><a href="#" class="updateDate" data-name="date_for_dish" data-type="date"
                                                           data-value="{!! $dish->first()->date_for_dish !!}"
                                                           data-emptytext="Trống"
                                                           data-pk="{{ $dish->first()->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                                           data-title="Ngày theo món ăn">{{ date('d/m/Y', strtotime($dish->first()->date_for_dish ?? '')) }}</a>
                                                    </td>
                                                    <td>{{ $dish->first()->dish_code ?? '' }}</td>
                                                    <td>
                                                        <select class="form-control select2" style="width: 95%;" name="dish_id"
                                                                onChange="changDishForMenuCard($(this), '{{$dishId}}', '{{$menuCard->id}}');">
                                                            @foreach ($dishForCustomer as $key => $valueDish)
                                                                <option value="{{ $valueDish->dish_id }}" {{  $valueDish->dish_id == $dishId ? 'selected' : '' }}>{{ $dishName[$valueDish->dish_id] ?? 'Món ăn bị xóa' }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <a href="#" class="select-product-gift"
                                                           data-type="select"
                                                           data-name="product_gift"
                                                           data-pk="{{ $dish->first()->id }}"
                                                           data-value="{{ $dish->first()->product_gift }}"
                                                           data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                                           data-title="Loại">{{ $dish->first()->product_gift == 1 ? 'Quà chiều' : 'Món ăn' }}</a>
                                                    </td>
                                                    <td colspan="7" class="detail_parent_{{ $menuCard->id }}" style="padding: 0px">
                                                        <table class="show-total_{{ $menuCard->id }}">
                                                            <tr>
                                                                <td style="width: 102px; border: none; border-right: 1px solid #dee2e6;"></td>
                                                                <td style="width: 175px; border: none; border-right: 1px solid #dee2e6;"></td>
                                                                <td style="width: 155px; border: none; border-right: 1px solid #dee2e6;"></td>
                                                                <td style="width: 140px; border: none; border-right: 1px solid #dee2e6;"></td>
                                                                <td style="text-align: right; width: 125px; border: none; border-right: 1px solid #dee2e6;"></td>
                                                                <td style="text-align: right; width: 135px; border: none; border-right: 1px solid #dee2e6;">{{ number_format($dish->sum('amount_of_product_in_order')) . '₫'}}</td>
                                                                <td style="border: none"></td>
                                                            </tr>
                                                        </table>
                                                        <table class="d-none detail_info_{{ $menuCard->id }}">
                                                            @foreach($dish as $item)
                                                                <tr>
                                                                    <td style="text-align: center;width: 102px;">
                                                                        <a href="#" class="updateDate" data-name="date_for_product" data-type="date"
                                                                           data-value="{!! ($item->date_for_product == '' ? $item->date_for_dish : $item->date_for_product) !!}"
                                                                           data-emptytext="Trống"
                                                                           data-pk="{{ $item->id }}" data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                                                           data-title="Ngày theo nguyên liệu">{{ date('d/m/Y', strtotime(($item->date_for_product == '' ? $item->date_for_dish : $item->date_for_product))) }}</a>
                                                                    </td>
                                                                    <td style="width: 175px;">{{ $item->product_name }} </td>
                                                                    <td style="width: 155px;">
                                                                        <a href="#"
                                                                           class="edit-item-bom"
                                                                           data-value="{{ $item->bom }}" data-name="bom"
                                                                           data-step="any"
                                                                           data-type="text" data-min="0"
                                                                           data-pk="{{ $item->id }}"

                                                                           data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                                                           data-title="Định lượng">{{$item->bom}}</a> {{ $item->product_unit ?? ''}}
                                                                    </td>
                                                                    <td style="width: 140px;" class="product_total_bom item_id_{{ $item->id }}">
                                                                        {!! number_format($item->total_bom, 2) !!} {{ $item->product_unit ?? ''}}
                                                                    </td>
                                                                    <td style="text-align: right; width: 125px">{{ number_format($item->import_price)}} ₫</td>
                                                                    <td style="text-align: right; width: 135px">{{ number_format($item->amount_of_product_in_order) . '₫'}} </td>
                                                                    <td><a data-perm="davicook_order:edit_info"
                                                                           perm-type="disable" href="#"
                                                                           class="edit-item-comment"
                                                                           data-value="{{ $item->comment ?? ''}}"
                                                                           data-name="comment" data-type="text"
                                                                           data-pk="{{ $item->id ?? ''}}"
                                                                           data-emptytext="Trống"
                                                                           data-url="{{ route("admin.davicook_menu_card.update_item_menu_card_detail") }}"
                                                                           data-title="{{ sc_language_render('order.admin.comment') }}">{{ $item->comment }}</a>
                                                                    </td>
                                                                </tr>
                                                            @endforeach

                                                        </table>
                                                    </td>
                                                    <td rowspan="" style="text-align: center">
                                                        <span data-perm="davicook_order:edit_info"
                                                              onclick="deleteMenuCardDetail('{{ $dishId }}', '{{ $menuCard->id }}', '{{ route("admin.davicook_menu_card.dish_of_menu_card_delete") }}');"
                                                              class="btn btn-danger btn-sm btn-flat"
                                                              data-title="Delete">
                                                              <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            <tr id="btn_add_dish_{{ $menuCard->id }}" class="not-print">
                                                <td colspan="12">
                                                    <button
                                                            type="button"  class="btn btn-flat btn-success"
                                                            id="btn_add_dish_{{ $menuCard->id }}"
                                                            onclick="getSelectDishByCustomer('{{ $menuCard->id }}')"
                                                            title="{{sc_language_render('action.add') }}"><i
                                                                class="fa fa-plus"></i> Thêm món
                                                    </button>
                                                    &nbsp;&nbsp;&nbsp;
                                                    <button
                                                            style="display: none; margin-right: 50px"
                                                            type="button" class="btn btn-flat btn-warning"
                                                            id="btn_save_dish_{{ $menuCard->id }}" onclick="submitFormSaveDishForMenuCardDetail('{{ $menuCard->id }}')" title="Save"><i
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
                    <div class="sub-total-cost">
                        <div class="btn-group float-right">
                            <h6 style="font-weight: bold">Tổng tiền giá vốn:</h6>
                            <input type="text" class="sub_total_cost sub_total_cost_key_'+key+'" readonly
                                   value="{{ number_format($menuCard->sub_total_cost). '₫' }}"
                                   style="font-weight: bold; margin: 0px 13px">
                        </div>
                    </div>
                @endforeach
            </div>
            <form action="" id="form_create_new_sub_menu_card" method="post">
                @csrf
                <div id="menu-card">
                    {{--                        Body --}}
                </div>
                <div class="create-menu-card mb-3">
                    <input type="button" data-perm="davicook_menu_card:create" id="btn_create_new_menu_card" class="btn btn-primary" value="Tạo phiếu mới">
                    <input type="hidden" id="" name="id" value="{{ $menuEstCard->id }}">
                    <input type="hidden" id="" name="customer_id" value="{{ $menuEstCard->customer_id }}">
                    <input type="button" id="btn_save_new_menu_card" class="btn btn-primary" value="Lưu phiếu mới">
                </div>
            </form>
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

        th {
            white-space: nowrap;
        }

        .td-title {
            width: 35%;
            font-weight: bold;
        }

        table {
            width: 100%;
            height: 100%;
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

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>

    <script type="text/javascript">
        $('#btn_save_new_menu_card').hide();
        function formatMoney(number) {
            return number.toLocaleString();
        }

        function CustomShowMsgAlertJs(type = 'error', msg = '') {
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

        // Update customer davicook info
        $(document).ready(function () {
            $('.select2').select2()
        });
        // Check remove dom
        function checkRemoveDOM(menu_card_id) {
            if ($('.dish_no_'+menu_card_id).length == 0) {
                $('#btn_save_dish_'+menu_card_id).hide();
            }
        }

        /**
         *Update giá tiền cost từng phiếu và tổng tiền tất cả
         */
        function updateSubTotalCostAndTotalCost(menu_card_id) {
        }
        /**
         * Check số thứ tự
         * @param key
         */
        function updateDishNo(menu_card_id) {
        }

        $(function () {
            $(".date_time").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });

        /**
         * Xóa món ăn trong từng phiếu con.
         * @param dish_id
         * @param menu_card_id
         * @param url
         */
        function deleteMenuCardDetail(dish_id, menu_card_id, url) {
            let name = dish_id == null ? 'Bạn muốn xóa phiếu con này ?' : 'Bạn muốn xóa món ăn này?';
            let text = dish_id == null ? 'Chú ý: Thao tác không thể hoàn lại!' : '';
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: name,
                text: text,
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'delete',
                            url: url,
                            data: {
                                'dish_id': dish_id,
                                'menu_card_id': menu_card_id,
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


        $('#btn_create_new_menu_card').click(function () {
            createSubMenuCardWithDay('{{ $now }}');
            $(this).hide()
            $('#btn_save_new_menu_card').show();
        })

        /**
         * Tạo các phiếu con từ mảng Date
         * @param arrDate
         */
        function createSubMenuCardWithDay(now) {
            let html = '';
            let number_meal = 1;
            let keyMenu = '{{ $menuEstCard->id }}';
            let key = '{{ rand() }}';
            html += '' +
                '<div class="main-sub-menu-card-by-date mt-4 mb-1" id="sub_card_' + key + '" style="margin-top: 90px;">\n' +
                '<div class="main-order ml-2 mt-5">\n' +
                '<div class="title-sub-card d-flex mb-2 mt-2 pr-2">\n' +
                '<div class="col-sm-4">\n' +
                '<label style="font-size: 20px; margin-left: 10px; margin-top: 30px; color: blue">TẠO PHIẾU MỚI</label>\n' +
                '</div>\n' +
                '</div>\n' +
                '<div class="title-sub-card d-flex mb-2 mt-2 pr-2">\n' +
                '<div class="col-sm-4">\n' +
                '<lable>Ngày</lable>\n' +
                '<input type="text" readonly name="date[' + key + ']" value="" id="date_sub_card_' + key + '" class="date_time" onchange="changeDateNewMenuCard($(this),'+key+')">\n' +
                '</div>\n' +
                '<div class="col-sm-4">\n' +
                '<lable>Số lượng suất ăn</lable>\n' +
                '<input type="number" min="1" class="check-number-of-servings" id="number_of_servings_sub_card_' + key + '" oninput="changeNumberOfServings(' + key + '); updateSubTotalCostAndTotalCost(' + key + ')" name="number_of_servings[' + key + ']" value="' + number_meal + '">\n' +
                '</div>\n' +
                '<div class="col-sm-4 " style="text-align: right">\n' +
                '<input type="button" class="btn-danger btn-remove-item-card" onclick="removeItemSubCard(' + key + ');" value="Xóa phiếu">\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>\n' +

                '<div class="row">\n' +
                '<div class="col-sm-12">\n' +
                '<div class="card collapsed-card">\n' +
                '<div class="table-responsive">\n' +
                '<table class="table box-body text-wrap table-bordered">\n' +
                '<thead>\n' +
                '<tr>\n' +
                '<th style="text-align: center; min-width: 102px" class="dish_code">Ngày</th>\n' +
                '<th style="text-align: center; max-width: 85px!important; min-width: 85px; white-space: normal;" class="dish_code">Mã món ăn</th>\n' +
                '<th style="text-align: center; min-width: 175px" class="dish_name">Tên món ăn</th>\n' +
                '<th style="text-align: center; min-width: 120px" class="product_gift">Loại</th>\n' +
                '<th style="text-align: center; min-width: 102px" class="dish_code">Ngày</th>\n' +
                '<th style="text-align: center; min-width: 175px" class="product_name">Tên nguyên liệu</th>\n' +
                '<th style="text-align: center; min-width: 125px" class="bom">Định lượng</th>\n' +
                '<th style="text-align: center; min-width: 100px" class="total_bom">Nguyên liệu suất</th>\n' +
                '<th style="text-align: center; min-width: 110px" class="import_price">Giá nhập</th>\n' +
                '<th style="text-align: center; min-width: 110px" class="amount_of_product_in_order">Tổng tiền Cost</th>\n' +
                '<th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>\n' +
                ' <th style="text-align: center; width: auto" class="delete">Xóa</th>\n' +
                '</tr>\n' +
                '</thead>\n' +
                '<tbody>\n' +
                '<tr id="btn_add_dish_' + key + '" class="not-print">\n' +
                '<td colspan="12">\n' +
                '<button type="button" class="btn btn-flat btn-success" onclick="getSelectDishByCustomer(' + key + ');" id="btn_add_menu_key_' + key + '"><i class="fa fa-plus"></i>Thêm món</button>\n' +
                '</td>\n' +
                '</tr>\n' +
                '</tbody>\n' +
                '</table>\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>\n' +
                '<div class="sub-total-cost d-none">\n' +
                '<div class="btn-group float-right">\n' +
                '<h6 style="font-weight: bold">Tổng tiền giá vốn:</h6>\n' +
                '<input type="hidden" class="" name="sub_total_cost[' + key + ']"  id="number_sub_total_cost_' + key + '" >\n' +
                '<input type="text" class="sub_total_cost sub_total_cost_key_' + key + '" readonly id="sub_total_cost_' + key + '" style="font-weight: bold; margin: 0px 13px">\n' +
                '</div>\n' +
                '</div>\n' +
                '</div>'
            ;

            $('#menu-card').append(html)
            $(".date_time").datepicker({
                dateFormat: "dd/mm/yy"
            });
        }

        /**
         * Xóa phiếu con khi thêm mới ở edit.
         * @param key
         */
        function removeItemSubCard(key) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Xác nhận xóa phiếu này!',
                text: "Lưu ý : Xóa sẽ không thể hoàn tác lại!",
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,
                preConfirm: function () {
                    $('#sub_card_' + key).remove();
                    $('#btn_create_new_menu_card').show();
                    $('#btn_save_new_menu_card').hide();
                }
            })
        }
        /**
         * Check ngày trên phiếu mới có hợp lệ.
         **/
        function changeDateNewMenuCard(e,key) {
            let arrDate = [];
            let value = e.val();
            let start_date = new Date($('.start-date').data('value'));
            let end_date = new Date($('.end-date').data('value'));
            let newDate = new Date(value.split("/").reverse().join("-"));
            $('.sub-date').each(function () {
                arrDate.push($(this).val())
            })

            if (arrDate.includes(value)) {
                e.val('');
                return alertMsg('error', 'Lỗi', 'Ngày được chọn đã tồn tại!');
            }

            if (newDate < start_date || newDate > end_date) {
                e.val('');
                return alertMsg('error', 'Lỗi', 'Ngày được chọn không nằm trong khoản ngày đã cho!');
            }
        }

        /**
         * Function xử lý cập nhập lại :
         * Nguyên liệu suất
         * Giá tiền cost
         */
        function updateTotalAmountInline(key_card, key_index, unit_type) {
            var product_unit_type = unit_type;
            var bom = $('.add_bom_' + key_card + '_' + key_index).eq(0).val();
            var qty = $('#number_of_servings_sub_card_' + key_card).val();
            var import_price = $('.add_import_price_' + key_card + '_' + key_index).eq(0).val();
            // var import_price = Number(import_price_str.replace(/[^0-9\.-]+/g, ""));

            $('.add_total_bom_' + key_card + '_' + key_index).eq(0).val(roundTotalBom(qty * bom, product_unit_type));

            var total_cost = Math.round(roundTotalBom(qty * bom, product_unit_type) * import_price);
            let formated = total_cost.toLocaleString('en-US');
            $('.number_amount_of_product_in_order_' + key_card + '_' + key_index).eq(0).val(total_cost);
            $('.amount_of_product_in_order_' + key_card + '_' + key_index).eq(0).html(formated);
            // updateSubTotalCostAndTotalCost(key_card, key_index);
        }

        /**
         * Function xử lý khi thay đổi số lượng suất ăn
         */
        function changeNumberOfServings(key) {
            let sum = 0;
            var num = $('#number_of_servings_sub_card_' + key).val();
            if ($('.pro_by_dish_' + key).length > 0) {
                $('.change_num_of_ser_' + key).val(num).trigger('change');
            }
            $('.check-number-of-servings').each(function () {
                sum = sum + parseFloat($(this).val());
            });

            $('#total_number_of_servings').val(sum);
        }

        /**
         * Function xử lý khi thay đổi món ăn
         */
        function changDishForMenuCard(element, dish_id, menu_card_id) {
            $(this).button('loading');
            let customer_id = $('[name="customer_id"]').val();
            let new_dish_id = element.val();
            $.ajax({
                url: '{{ route("admin.davicook_menu_card.change_dish_for_menu_card") }}',
                type: 'post',
                dataType: 'json',
                data: {
                    new_dish_id: new_dish_id,
                    old_dish_id: dish_id,
                    customer_id: customer_id,
                    menu_card_id: menu_card_id,
                    _token : "{{ csrf_token() }}",
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        alertJs('success', result.msg);
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                    }
                }
            });
        }

        /**
         * Submit form tạo mới các phiếu con ở màng chi tiết.
         **/
        $('#btn_save_new_menu_card').click(function () {
            $(this).button('loading');

            let flag_not_product = false;

            $('.main-sub-menu-card-by-date').each(function (i) {
                if (!$('div').hasClass('check-null-product')) {
                    return flag_not_product = true;
                }
            })

            if (flag_not_product) {
                return alertMsg('error','Chưa tạo sản phẩm cho phiếu con!');
            }
            $.ajax({
                url: '{{ route("admin.davicook_menu_card.store_new_menu_card_for_display_edit") }}',
                type: 'post',
                dataType: 'json',
                data: $('form#form_create_new_sub_menu_card').serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        alertJs('success', result.msg);
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                        $('#add-item-button').prop('disabled', false);
                    }
                }
            });
        })

        /**
         * Select món ăn theo khách hàng
         * @param menu_card_id
         */
        function getSelectDishByCustomer(menu_card_id) {
            let customer_id = $('[name="customer_id"]').val();
            let type_object = $('[name="type_object"]').val();
            let number_servings = $('#number_of_servings_sub_card_'+menu_card_id).val();
            let date_create = $('#date_sub_card_'+menu_card_id).val();
            if (number_servings === null) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Số lượng xuất ăn trống!');
            }
            if (number_servings <= 0) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Số lượng xuất ăn nhỏ hơn 0!');
            }
            if (!Number.isInteger(parseFloat(number_servings))) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Số lượng xuất ăn phải là số nguyên!');
            }
            if (date_create == '') {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Ngày trên phiếu đang trống!');
            }
            if (customer_id === null) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa có thông tin khách hàng!');
            } else {
                $.ajax({
                    url: '{{ sc_route_admin('admin.davicook_menu_card.get_dish_by_customer') }}',
                    type: "get",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        customer_id: customer_id,
                        type_object: type_object,
                        key: menu_card_id,
                        number_of_servings: number_servings,
                        date: date_create,
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (returnedData) {
                        $('#loading').hide();
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#btn_add_dish_'+menu_card_id).before(returnedData.dish);
                            // updateDishNo(key);
                            // let elm = $('.select2');
                            // elm.select2();
                            $('.select2').each(function () {
                                $(this).select2();
                            });
                        }
                    }
                });
            }
        }

        /**
         * Request lấy nguyên liệu theo món ăn đã chọn
         * @param e
         * @param menu_card_id
         */
        function getProductBySelectDish(e, menu_card_id) {
            $('.select2').select2();
            let arrDish = [];
            let customer_id = $('[name="customer_id"]').val();
            let node = e.closest('tr');
            let add_delivery_time_for_dish = node.find('.add_date_for_dish_'+menu_card_id);
            $('.select_add_dish_id_' + menu_card_id).each(function () {
                if ($(this).val() != null && $(this).val() !== '') {
                    arrDish.push($(this).val());
                }
            })
            let uniqueArrDish = unique(arrDish);
            if (arrDish.length !== uniqueArrDish.length) {
                node.find('.pro_by_dish_' + menu_card_id).remove();
                return alertMsg('error', 'Lỗi tạo phiếu', 'Món ăn đã tồn tại. Vui lòng chọn món khác!');
            }
            let dish_id = node.find('.select_add_dish_id_' + menu_card_id).val();
            let is_spice = node.find('.select_add_is_spice_' + menu_card_id).val();

            let number_of_servings = $('#number_of_servings_sub_card_'+menu_card_id).val();
            let date = $('#date_sub_card_' + menu_card_id).val();
            let type_object = $('[name="type_object"]').val();
            if (customer_id == null) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa có thông tin khách hàng!');
            }
            if (number_of_servings <= 0) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Vui lòng nhập số lượng suất ăn!');
            }
            $.ajax({
                url: '{{ sc_route_admin('admin.davicook_menu_card.get_product_by_dish') }}',
                type: "get",
                dateType: "application/json; charset=utf-8",
                data: {
                    dish_id: dish_id,
                    is_spice: is_spice,
                    key: menu_card_id,
                    customer_id: customer_id,
                    number_of_servings: number_of_servings,
                    date: date,
                    type_object : type_object,
                },
                beforeSend: function () {
                    $('#loading').show();
                    node.find('.pro_by_dish_' + menu_card_id).remove();
                },
                success: function (returnedData) {
                    $('#loading').hide();
                    if (returnedData.error == 1) {
                        alertJs('error', returnedData.msg);
                    }
                    add_delivery_time_for_dish.append(
                        '<input type="text" name="delivery_time['+menu_card_id+']['+returnedData.dish_id+']" value="'+ date +'" id="delivery_time_'+menu_card_id+'_'+returnedData.dish_id+'" class="form-control date_time">'
                    )
                    node.find('.add_dish_code_' + menu_card_id).val(returnedData.dish_code);
                    node.find('#add_td_product_' + menu_card_id).after(returnedData.products);
                    $('#key_date_for_dish').val(menu_card_id);
                    $('#btn_save_dish_'+menu_card_id).show();
                    if (returnedData.msgProductOff) {
                        CustomShowMsgAlertJs('error', returnedData.msgProductOff);
                    }

                    $( ".date_time" ).datepicker({
                        dateFormat: "dd/mm/yy"
                    });
                }
            });
        }

        /**
         * Check mảng trùng nhau
         * @param arr
         * @returns {[]}
         */
        function unique(arr) {
            var newArr = []
            for (var i = 0; i < arr.length; i++) {
                if (!newArr.includes(arr[i])) {
                    newArr.push(arr[i])
                }
            }
            return newArr
        }

        /**
         * Submit khi thêm món ăn mới cho từng phiếu.
         * @param menu_card_id
         */
        function submitFormSaveDishForMenuCardDetail(menu_card_id)
        {
            $('#btn_add_dish_'+menu_card_id).prop('disabled', true);
            $('#btn_save_dish_'+menu_card_id).button('loading');
            let number_of_servings = $('#number_of_servings_sub_card_'+menu_card_id).val();
            $.ajax({
                url: '{{ route("admin.davicook_menu_card.store_dish_for_menu_card") }}',
                type: 'post',
                dataType: 'json',
                data: $('form#form_add_dish_for_menu_card_'+menu_card_id).serialize()
                            +"&number_of_servings=" + number_of_servings,
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
        }

        $('#btn_update_price').click(function () {
            $('#loading').show();
        })

        $(document).ready(function () {
            all_editable();
        });

        function all_editable() {
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };
            /**
             * Thay đổi số lượng xuất ăn từng phiếu con.
             */
            $('.updateNumberOfServings').editable({
                validate: function (value) {
                    if (value == '' || parseInt(value) <= 0) {
                        return 'Không được rỗng và nhỏ hơn 0';
                    }
                    if (!Number.isInteger(parseFloat(value))) {
                        return 'Số lượng suất ăn phải là số nguyên!';
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

            $('.updateDate').editable({
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
                        setTimeout(function () {
                            location.reload();
                        }, 1500);
                    }
                },
                display: function (value) {
                    var format = convertDate(value);
                    $(this).text(format);
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
                    if (Number(value) <= 0) {
                        return 'Định lượng của nguyên liệu không nhỏ hơn 0!';
                    }
                    if (!checkLimitDecimal(Number(value))) {
                        return 'Định lượng nhập vào đã vượt mức cho phép hoặc đã vượt quá 7 chử số ở phần thập phân, vui lòng kiểm tra lại!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        location.reload();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                },

            });

            $('.updateWeekNo').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                },
                display: function (value) {
                    $(this).text(value);
                }
            });

            $('.select-product-gift').editable({
                source: [
                    {value: 0, text: 'Món ăn'},
                    {value: 1, text: 'Quà chiều'},
                ],
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

            $('.edit-item-comment').editable({
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }

        // Checkbox print order
        $("input:checkbox").on('click', function () {
            var $box = $(this);
            if ($box.is(":checked")) {
                var group = "input:checkbox[name='" + $box.attr("name") + "']";
                $(group).prop("checked", false);
                $box.prop("checked", true);
            } else {
                $box.prop("checked", false);
            }
        });

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function checkLimitDecimal(value) {
            var rx = /^(\d{0,5}\.\d{0,7}|\d{0,5}|\.\d{0,7})$/;
            ;
            if (rx.test(value)) {
                return true;
            } else {
                return false;
            }
        }

        // Set limit decimal range input
        function limitDecimalPlaces(e, count) {
            if (e.target.value < 0) {
                e.target.value = Math.abs(e.target.value)
            }
            if (e.target.value.indexOf('.') == -1) {
                return;
            }
            if ((e.target.value.length - e.target.value.indexOf('.')) > count) {
                e.target.value = parseFloat(e.target.value).toFixed(count);
            }
        }

        function convertDate(str) {
            var date = new Date(str),
                mnth = ("0" + (date.getMonth() + 1)).slice(-2),
                day = ("0" + date.getDate()).slice(-2);
            return ([date.getFullYear(), mnth, day].join("-")).split("-").reverse().join("/");
        }

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
