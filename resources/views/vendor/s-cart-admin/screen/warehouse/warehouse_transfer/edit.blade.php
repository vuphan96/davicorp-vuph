@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">Chi tiết phiếu điều chuyển
                        #{{ $warehouseTransfer->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ session('nameUrl') ?? sc_route_admin('warehouse_transfer.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a disabled data-perm="warehouse_transfer:print" href="#" class="btn btn-flat btn btn-primary" data-toggle="modal"
                               data-target="#printDialog" ><i
                                        class="fa fa-print"></i>&nbsp;In đơn
                            </a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a data-perm="warehouse_transfer:create" href="{{sc_route_admin('warehouse_transfer.create')}}" class="btn btn-flat btn-primary" id="button_create_new">
                                <i class="fa fa-plus" title="' . sc_language_render('action.add') . '"></i></a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="#" class="btn btn-flat btn btn-primary" onclick="location.reload()"><i
                                        class="fa fa-sync-alt"></i></a>
                        </div>

                    </div>
                </div>

                <form class="row" id="order_edit_form" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $warehouseTransfer->id }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <td class="td-title">Tên đơn điều chuyển</td>
                                <td>
                                    <a style="font-weight: bold" data-perm="warehouse_transfer:edit_info"
                                       perm-type="disable" href="#"
                                       class="updateInfoRequired"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-value="{{ $warehouseTransfer->title }}" data-name="title"
                                       data-step="any"
                                       data-type="text" data-min="0"
                                       data-pk="{{ $warehouseTransfer->id }}"
                                       data-url="{{ route("warehouse_transfer.update") }}"
                                       data-title="Tên đơn"> {{ $warehouseTransfer->title }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Tên kho chuyển:</td>
                                <td>{!! $warehouseTransfer->warehouse_name_to ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">Tên kho nhận:</td>

                                <td>{!! $warehouseTransfer->warehouse_name_from ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">Lý do chuyển hàng:</td>
                                <td>
                                    <a style="font-weight: bold" data-perm="warehouse_transfer:edit_info"
                                       perm-type="disable" href="#"
                                       class="updateInfoRequired"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-value="{{ $warehouseTransfer->reason }}" data-name="reason"
                                       data-step="any"
                                       data-type="text" data-min="0"
                                       data-pk="{{ $warehouseTransfer->id }}"
                                       data-url="{{ route("warehouse_transfer.update") }}"
                                       data-title="Lý do chuyể hàng"> {{ $warehouseTransfer->reason }}</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Trạng thái đơn chuyển hàng</td>
                                <td>
                                    <a data-perm="warehouse_transfer:edit_info" perm-type="disable" href="#" class="updateStatus"
                                       data-name="status" data-type="select"
                                       data-source="{{ json_encode($status) }}" data-pk="{{ $warehouseTransfer->id }}"
                                       data-value="{!! $warehouseTransfer->status !!}"
                                       data-url="{{ route("warehouse_transfer.update") }}"
                                       {{ !$editable ? 'order-lock=disable' : "" }}
                                       data-title="Trạng thái đơn nhập">{{ $status[$warehouseTransfer->status] }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày đặt đơn</td>
                                <td>{{ date('d/m/Y H:i:s', strtotime($warehouseTransfer->created_at ?? '')) }}</td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày chuyển hàng</td>
                                <td>{{ $warehouseTransfer->date_export != '' ? date('d/m/Y', strtotime($warehouseTransfer->date_export)) : '' }}</td>
                            </tr>
                            <tr>
                                <td class="td-title">Mã phiếu nhập hàng</td>
                                <td style="display: flex; flex-wrap: wrap;">
                                    @foreach($warehouseTransfer->imports as $import)
                                        <a data-perm="import_order:detail" target="_blank" href="{{ sc_route_admin('order_import.edit', ['id' => $import->import_id ? $import->import_id : 'not-found-id']) }}">{{ $import->import_id_name }}</a>;&nbsp;
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>

                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="warehouse_transfer_id" value="{{ $warehouseTransfer->id }}">
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
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">ĐVT</th>
                                            <th class="product_comment" style="word-break: break-word;min-width: 90px">Ghi chú</th>
                                            <th style="min-width: 65px;word-break: break-word; text-align: center">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                             $countStt = $warehouseTransfer->details->count();
                                        @endphp
                                        @foreach ($warehouseTransfer->details as $key => $item)
                                            <tr class="ordered-products">
                                                <td style="text-align: center">{{ $key + 1 }}</td>
                                                <td class="overflow_prevent">{{ $item->product_code }}</td>
                                                <td>{{  $item->product_name }}</td>
                                                <td class="product_qty"><a style="font-weight: bold" data-perm="warehouse_transfer:edit_info"
                                                                           perm-type="disable" href="#"
                                                                           class="edit-item-detail"
                                                                           {{ !$editable ? 'order-lock=disable' : "" }}
                                                                           data-value="{{ $item->qty }}" data-name="qty"
                                                                           data-step="any"
                                                                           data-type="text" data-min="0"
                                                                           data-pk="{{ $item->id }}"
                                                                           data-url="{{ route("warehouse_transfer.update") }}"
                                                                           data-title="Số lượng"> {{ $item->qty }}</a>
                                                </td>

                                                <td class="product_price">{{ $item->unit_name ?? ''}}
                                                <td class="product_comment" style="overflow: hidden"><a
                                                            data-perm="warehouse_transfer:edit_info" perm-type="disable" href="#"
                                                            class="edit-item-comment"
                                                            {{ !$editable ? 'order-lock=disable' : "" }}
                                                            data-value="{{ $item->comment ?? ''}}"
                                                            data-name="comment" data-type="text"
                                                            data-emptytext="Trống"
                                                            data-pk="{{ $item->id ?? ''}}"
                                                            data-url="{{ route("warehouse_transfer.update") }}"
                                                            data-title="Comment">{{ $item->comment }}</a>
                                                </td>
                                                <td style="text-align: center">
                                                    <span data-perm="warehouse_transfer:edit_info"
                                                          onclick="deleteItem($(this),'{{ $item->id }}');"
                                                          {{ !$editable ? 'order-lock=hide' : "" }}
                                                          class="btn btn-danger btn-xs" data-title="Delete"><i
                                                                class="fa fa-trash" aria-hidden="true"></i></span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr id="add-item" class="not-print">
                                            <td colspan="10">
                                                <button data-perm="warehouse_transfer:edit_info" type="button"
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="row">
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="td-title">Ghi chú đơn chuyển:</td>
                                    <td class="order-comment"><a href="#" class="edit-order-comment"
                                                                 data-value="{{ $warehouseTransfer->note ?? ''}}"
                                                                 data-name="note"
                                                                 {{ !$editable ? 'order-lock=disable' : "" }}
                                                                 data-type="textarea"
                                                                 data-emptytext="Trống"
                                                                 data-pk="{{ $warehouseTransfer->id }}"
                                                                 data-url="{{ route("admin_order.update") }}"
                                                                 data-title="Sửa ghi chú"> {{ $warehouseTransfer->note ?? 'Trống'}}</a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6 mt-3">
                    </div>
                </div>
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="4">Lịch sử đơn chuyển hàng</th>
                                </tr>
                                <tr>
                                    <td>{{ sc_language_render('admin.order_history.time') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.actor') }}</td>
                                    <td>Thao tác</td>
                                    <td>Nội dung</td>
                                </tr>
                                </thead>
                                <tbody  id="order-history">
                                @forelse($warehouseTransfer->history ?? [] as $k => $v)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::make($v->created_at)->format('d/m/Y H:i:s') ?? 'Trống' }}</td>
                                        <td>(Ad){{ $v->user_name }}</td>
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
                <input type="hidden" name="ids" value="{{ $warehouseTransfer->id }}">
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
                    <td><input type="number" class="add_qty form-control" name="qty[]" data-minimum_qty_norm="0" data-unit_type="" data-check="3" value=""></td>
                    <td><input type="text" readonly class="add_unit form-control"  value=""></td>
                    <td><input class="add_comment form-control" name="comment[]" value="" autocomplete="off"></td>
                    <td style="text-align:center"><button onClick="$(this).parent().parent().remove(); checkRemoveDOM(); checkNumberOrder()" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                 </tr>';
        $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
    @endphp
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
        let arrUnit = <?php echo json_encode($unit ?? []); ?>;
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
            let product_id = e.val();
            let unit = arrUnit.find(p => (p.product_id == product_id));
            let node = e.closest('tr');
            node.find('.add_qty').css("border", ""); // Ẩn border red
            node.find('.add_sku').val(unit.sku);
            node.find('.add_qty').data('unit_type', unit.type_unit);
            node.find('.add_qty').val('');
            node.find('.add_unit').val(unit.unit_name);
            node.find('.add_comment').val('');
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
                if ($(this).val() < 0) {
                    return alertJs('error', 'Số lượng nhỏ hơn 0');
                }
                checks.push($(this).val());
            });

            $('.add_id').each(function () {
                products.push($(this).val());
            });

            if (products.includes('')) {
                alertJs('error', 'Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!');
            } else if (checks.includes('')) {
                alertJs('error', 'Số lượng sản phẩm không được để trống');
            } else {
                $('#add-item-button').prop('disabled', true);
                $.ajax({
                    url: '{{ route("warehouse_transfer.add_item_detail") }}',
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


            $('.edit-item-detail').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    var product_status = $(this).editable().data('status');
                    var product_unit_type = $(this).editable().data('unit_type');

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
            console.log(id);
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
                            url: '{{ route("warehouse_transfer.delete_detail") }}',
                            data: {
                                'detail_id': id,
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

        function checkRemoveDOM() {
            if ($('#add_td').length == 0) {
                $('#add-item-button-save').hide();
            }
        }

        const formatter = new Intl.NumberFormat('en-US', {
        });

    </script>
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
