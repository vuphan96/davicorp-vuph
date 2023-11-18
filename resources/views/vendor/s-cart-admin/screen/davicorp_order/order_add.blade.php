@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- card-header -->
                <div class="card-header with-border">
                    <div class="card-tools">
                        <div class="btn-group float-right" style="margin-right: 5px">
                            <a href="{{ sc_route_admin('admin_order.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /card-header -->

                <!-- card-body -->
                <form accept-charset="UTF-8" class="form-horizontal" id="form-main">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('customer_id') ? ' text-red' : '' }} mt-1 ml-1">
                            <label for="customer_id" class="col-2 asterisk col-form-label">Chọn khách hàng</label>
                            <div class="col-sm-8">
                                <select class="form-control customer_id select2 " style="width: 100%;"
                                        name="customer_id" id="customer_id">
                                    <option value="">{{ sc_language_render('order.admin.select_customer') }}</option>
                                    @foreach ($customers as $k => $v)
                                        <option value="{{ $v->id }}">{{ $v->name.'<'.$v->customer_code.'>' }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('customer_id'))
                                    <span class="text-sm">
                                        {{ $errors->first('customer_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="input-group-append">
                                <a href="{{ sc_route_admin('admin_customer.index') }}">
                                    <button type="button" id="button-filter" class="btn btn-success  btn-flat"><i
                                                class="fa fa-plus" title="Add new"></i></button>
                                </a>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('address') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="address" class="col-2 col-form-label">Địa chỉ</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input readonly id="address" name="address" value="{{ old('address') }}"
                                           class="form-control address" placeholder=""/>
                                </div>
                                @if ($errors->has('address'))
                                    <span class="text-sm">
                                        {{ $errors->first('address') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row  {{ $errors->has('object_id') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="object_id" class="col-2 col-form-label asterisk">Đối tượng</label>
                            <div class="col-sm-8">
                                <select class="form-control object_id" style="width: 100%;" name="object_id"
                                        id="object_id">
                                    <option value="2" {{ (old('object_id') == 2) ? 'selected':'' }}>Học sinh</option>
                                    <option value="1" {{ (old('object_id') == 1) ? 'selected':'' }}>Giáo viên</option>
                                </select>
                                @if ($errors->has('object_id'))
                                    <span class="text-sm">
                                        {{ $errors->first('object_id') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row  {{ $errors->has('explain') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="explain" class="col-2 col-form-label asterisk">Diễn giải</label>
                            <div class="col-sm-8">
                                <select class="form-control explain" style="width: 100%;" name="explain" id="explain">
                                    @foreach ($orderExplains as $v)
                                        <option value="{{ $v }}" {{ (old('explain') ==$k) ? 'selected':'' }}>{{ $v}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('explain'))
                                    <span class="text-sm">
                                        {{ $errors->first('explain') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('delivery_time') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="delivery_time" class="col-2 col-form-label asterisk">Ngày giao hàng</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="delivery_time"
                                           autocomplete="off"
                                           name="delivery_time"
                                           value="{{ $delivery_time }}"
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm delivery_time date_time"
                                           data-date-format="dd/mm/yyyy"
                                           id="delivery_time"
                                           placeholder="Chọn ngày"/>
                                </div>
                                @if ($errors->has('delivery_time'))
                                    <span class="text-sm">
                                        {{ $errors->first('delivery_time') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('bill_date') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="bill_date" class="col-2 asterisk col-form-label">Ngày trên hóa đơn</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input onchange="handleChangeBillDate()" type="text" style="width: 100px;"
                                           id="bill_date"
                                           autocomplete="off"
                                           name="bill_date"
                                           id="bill_date"
                                           value="{{ $bill_date }}"
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm bill_date date_time"
                                           data-date-format="dd/mm/yyyy"
                                           placeholder="Chọn ngày"/>
                                </div>
                                @if ($errors->has('bill_date'))
                                    <span class="text-sm">
                                        {{ $errors->first('bill_date') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('comment') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="address" class="col-2 col-form-label">Ghi chú</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="comment" value="{{ old('comment') }}" name="comment"
                                           class="form-control comment" placeholder=""/>
                                </div>
                                @if ($errors->has('comment'))
                                    <span class="text-sm">
                                        {{ $errors->first('comment') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </form>
                <form id="form-add-item" action="" method="">
                    @csrf
                    <div class="row">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table id="table-product"
                                               class="table box-body text-wrap table-bordered table-product">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: 52px; word-break: break-word" class="sku">STT</th>
                                                <th style="text-align: center; max-width: 90px; word-break: break-word" class="sku">Mã sản phẩm</th>
                                                <th style="text-align: center; min-width: 230px" class="product_name">Tên sản phẩm</th>
                                                <th style="text-align: center; width: auto" class="product_qty">Số lượng</th>
                                                <th style="text-align: center; width: auto" class="product_qty">SL thực tế</th>
                                                <th style="text-align: center; width: auto" class="product_unit">Đơn vị tính</th>
                                                <th style="text-align: center; width: auto" class="product_price">Giá</th>
                                                <th style="text-align: center; width: auto" class="product_total">Tổng tiền</th>
                                                <th style="text-align: center; width: auto" class="product_comment">Ghi chú</th>
                                                <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr></tr>
                                            <tr id="add-item" class="not-print">
                                                <td colspan="11">
                                                    <button type="button" class="btn btn-flat btn-success"
                                                            id="add-item-button"
                                                            title="{{sc_language_render('action.add') }}"><i
                                                                class="fa fa-plus"></i> {{ sc_language_render('action.add') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <!-- /card-body -->

                <!-- .card-footer -->
                <div class="">
                    <div class="col-md-12">
                        <div class="btn-group float-right">
                            <h6 style="font-weight: bold">Tổng tiền:</h6>
                            <h6 class="add-sum-total"
                                style="font-weight: bold; margin: 0px 13px">{{ sc_currency_render($sum_total ?? 0, 'vnd') }}</h6>
                        </div>
                    </div>
                </div>
                <div class="card-footer row">
                    <div class="col-md-12">
                        <div class="btn-group float-right">
                            <button type="submit" class="btn btn-flat btn btn-primary submit-create-order">Đặt hàng
                            </button>
                        </div>
                    </div>
                </div>
                <!-- /.card-footer -->

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style type="text/css">
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .color-red {
            color: red !important;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        input {
            width: 400px;
        }

        label, input {
            display: block;
        }

        label {
            font-weight: bold;
        }

        input, .flexselect_dropdown li {
            font-size: 1rem;
        }

        small {
            color: #999;
        }

        .flexselect_selected small {
            color: #ddd;
        }
    </style>
    {{-- flexselect --}}
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/flexselect.css') }}" type="text/css" media="screen"/>
@endpush

@push('scripts')
    {{-- flexselect --}}
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>

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

        var productList = [];
        var currentRowIdx = 0;
        var rowIdxArr = [];
        var productOptions = '';

        $('#table-product').bind('keydown', function (e) {
            if (e.which === 37 || e.which === 38 || e.which === 9 ||
                e.which === 39 || e.which === 40 || e.which === 13) {
                e.preventDefault();
            }
        });

        $('table.table-product').keydown(function (e) {
            let $active = $('input:focus,select:focus', $(this));
            let $next = null;
            let focusableQuery = 'input:visible,select:visible,textarea:visible';
            let position = parseInt($active.closest('td').index()) + 1;
            let tr_position = parseInt($active.closest('tr').index());

            switch (e.keyCode) {
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
                    if (tr_position == rowIdxArr.length) {
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
            if ($next && $next.length) {
                $next.focus();
            }
        });

        $('#customer_id').change(() => {
            resetState();
            handleGetListProduct();
        })

        $('#object_id').change(() => {
            resetState();
            handleGetListProduct();
        })

        function resetState() {
            $('.select-product').remove();
            rowIdxArr = [];
            currentRowIdx = 0;
        }

        function handleGetListProduct() {
            let bill_date = ($("#bill_date").val()).split("/").reverse().join("-");
            let customer_id = $("#customer_id").val();
            let object_id = $("#object_id").val();

            $.ajax({
                url: '{{ sc_route_admin('admin_order.product_create_order') }}',
                type: "get",
                dateType: "application/json; charset=utf-8",
                data: {
                    bill_date: bill_date,
                    customer_id: customer_id
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (returnedData) {
                    if (returnedData.error == 1) {
                        node.remove();
                        $('#loading').hide();
                        return alertJsCustom('error', returnedData.msg);
                    }
                    if (object_id == 1) {
                        productList = returnedData.price_teacher_array.filter(p => (p.product_price != null && p.product_price != undefined));
                    } else {
                        productList = returnedData.price_student_array.filter(p => (p.product_price != null && p.product_price != undefined));
                    }

                    productOptions = '<option value="" selected></option>';
                    if (productList) {
                        console.log(productList);
                        productList.forEach(p => {
                            if (p.status == 0) {
                                productOptions += '<option value="' + p.product_id + '">' + p.product_name + ' (' + p.product_code + ') - ' + p.unit + ' - Hết hàng!</option>'
                            } else {
                                productOptions += '<option value="' + p.product_id + '">' + p.product_name + ' (' + p.product_code + ') - ' + p.unit + '</option>'
                            }
                        })
                    }

                    $('#loading').hide();
                }
            });
        }

        $('#add-item-button').click(function () {
            let bill_date = ($("#bill_date").val()).split("/").reverse().join("-");
            if (!bill_date) {
                alertJs('error', 'Chưa có thông tin ngày giao hàng!');
                return;
            }

            let customer_id = $("#customer_id").val();
            if (!customer_id) {
                alertJs('error', 'Chưa có thông tin khách hàng!');
                return;
            }


            let object_id = $("#object_id").val();
            if (!object_id) {
                alertJs('error', 'Chưa có thông tin đối tượng!');
                return;
            }

            let addButton = $(this);
            addButton.prop('disabled', true);

            let rowIdx = ++currentRowIdx;
            rowIdxArr.push(rowIdx);

            let rowHtml = '<tr class="select-product" id="row_' + rowIdx + '">'
            rowHtml += '<td class="pro check-num-order" style="text-align: center">' + (rowIdxArr.length) + '</td>';
            rowHtml += '<td class="pro"><input  id="add_sku_' + rowIdx + '" type="text" readonly class="add_sku form-control"  value=""></td>';
            rowHtml += '<td><select id="add_id_' + rowIdx + '" onChange="handleSelectProduct(' + rowIdx + ');" name="add_id[]"  class="add_id form-control flexselect" tabindex="2">' + productOptions + '</select></td>';
            rowHtml += '<td><input id="add_qty_' + rowIdx + '" type="number" onChange="handleInputQty(' + rowIdx + ');"  class="add_qty form-control" name="add_qty[]" data-minimum_qty_norm="0" data-unit_type="" data-check="3" value=""></td>';
            rowHtml += '<td><input id="add_qty_reality_' + rowIdx + '" type="number" readonly class="add_qty_reality form-control" name="add_qty_reality[]" value=""></td>';
            rowHtml += '<td><input id="add_unit_' + rowIdx + '" type="text" readonly class="add_unit form-control"  value=""></td>';
            rowHtml += '<td><input id="add_price_' + rowIdx + '" type="number" readonly class="add_price form-control" name="add_price[]" value="0"></td>';
            rowHtml += '<td><input id="add_total_' + rowIdx + '" type="number" readonly class="add_total form-control" value="0"></td>';
            rowHtml += '<td><input id="add_comment_' + rowIdx + '" class="add_comment form-control" name="add_comment[]" value="" autocomplete="off"></td>';
            rowHtml += '<td style="text-align:center"><button onClick="handleRemoveRow(' + rowIdx + ')" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>';
            rowHtml += '</tr>';

            $('#add-item').before(rowHtml);
            $("#add_id_" + rowIdx).flexselect({hideDropdownOnEmptyInput: true});
            $("#add_id_" + rowIdx + "_flexselect").focus();
            addButton.prop('disabled', false);

        })

        function handleSelectProduct(rowIdx) {
            $('#loading').show();
            let productSelect = $('#add_id_' + rowIdx).val();
            $('#add_qty_' + rowIdx).css("border", ""); // Ẩn border red

            let product = productList.find(p => p.product_id == productSelect);

            $('#add_sku_' + rowIdx).val(product.product_code);
            $('#add_qty_' + rowIdx).val('');
            $('#add_qty_' + rowIdx).data('unit_type', product.unit_type);
            $('#add_qty_' + rowIdx).data('minimum_qty_norm', product.minimum_qty);
            $('#add_qty_reality_' + rowIdx).val('');
            $('#add_price_' + rowIdx).val(product.product_price);
            $('#add_unit_' + rowIdx).val(product.unit);
            $('#add_comment_' + rowIdx).val('');
            $('#add_total_' + rowIdx).val(0);
            $('#loading').hide();

            update_sum_total();
            reIndexRowNum();
        }

        function update_sum_total() {
            let sum = 0;
            $('.add_total').each(function () {
                sum += Number(this.value.replace(/[^0-9\.-]+/g, ""));
            });
            let formated = sum.toLocaleString('en-US') + '₫';
            $('.add-sum-total').html(formated);
        }

        function reIndexRowNum() {
            $('.check-num-order').each(function (key) {
                $(this).text(key + 1)
            });
        }

        function handleInputQty(rowIdx) {
            let unit_type = $('#add_qty_' + rowIdx).data('unit_type');
            let minimum_qty_norm = $('#add_qty_' + rowIdx).data('minimum_qty_norm');
            let qty = $('#add_qty_' + rowIdx).val();
            let price = $('#add_price_' + rowIdx).val();

            if (Number(price) < 0) {
                $('#add_price_' + rowIdx).val(0);
                price = 0;
            }

            if (Number(qty) == 0) {
                $('#add_qty_' + rowIdx).css('border', '1px solid red');
                return;
            }

            if (Number(qty) < Number(minimum_qty_norm)) {
                $('#add_qty_' + rowIdx).css('border', '1px solid red');
                alertJs('error', 'Số lượng nhập vào đang nhỏ hơn định lượng tối thiểu (' + minimum_qty_norm + '), vui lòng kiểm tra lại!');
                return;
            }

            if (unit_type == 1 && !Number.isInteger(Number(qty))) {
                $('#add_qty_' + rowIdx).css('border', '1px solid red');
                alertJs('error', 'Vui lòng nhập số lượng sản phẩm là số nguyên!');
                return;
            }

            $('#add_qty_' + rowIdx).css('border', '');

            $('#add_total_' + rowIdx).val(qty * price);
            $('#add_qty_reality_' + rowIdx).val(qty);
            update_sum_total();
        }

        function handleRemoveRow(rowIdx) {
            $('#row_' + rowIdx).remove();

            const index = rowIdxArr.indexOf(rowIdx);
            if (index > -1) {
                rowIdxArr.splice(index, 1);
            }

            reIndexRowNum();
            update_sum_total();
        }

        function handleChangeBillDate() {
            let productSelectList = [];

            rowIdxArr.forEach(rowIdx => {
                productSelectList.push({rowIdx, product_id: $('#add_id_' + rowIdx).val()})
            })

            let bill_date = ($("#bill_date").val()).split("/").reverse().join("-");
            let customer_id = $("#customer_id").val();
            let object_id = $("#object_id").val();

            $.ajax({
                url: '{{ sc_route_admin('admin_order.product_create_order') }}',
                type: "get",
                dateType: "application/json; charset=utf-8",
                data: {
                    bill_date: bill_date,
                    customer_id: customer_id
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (returnedData) {
                    if (returnedData.error == 1) {
                        node.remove();
                        $('#loading').hide();
                        return alertJsCustom('error', returnedData.msg);
                    }
                    if (object_id == 1) {
                        productList = returnedData.price_teacher_array.filter(p => (p.product_price != null && p.product_price != undefined));
                    } else {
                        productList = returnedData.price_student_array.filter(p => (p.product_price != null && p.product_price != undefined));
                    }

                    productOptions = '<option value="" selected></option>';
                    if (productList) {
                        productList.forEach(p => {
                            if (p.status == 0) {
                                productOptions += '<option value="' + p.product_id + '">' + p.product_name + ' (' + p.product_code + ') - ' + p.unit + '- Hết hàng!</option>'
                            } else {
                                productOptions += '<option value="' + p.product_id + '">' + p.product_name + ' (' + p.product_code + ')  - ' + p.unit + '</option>'
                            }
                        })
                    }

                    let productNamesRemove = '';
                    productSelectList.forEach(p => {
                        let productExist = productList.find(p2 => p2.product_id == p.product_id);
                        let rowIdx = p.rowIdx;
                        if (!productExist) {

                            // Xoa san pham
                            let productCode = $('#add_sku_' + rowIdx).val()
                            if (productCode) {
                                productNamesRemove += "“" + productCode + "”, ";
                            }


                            $('#row_' + rowIdx).remove();
                            const index = rowIdxArr.indexOf(rowIdx);
                            if (index > -1) {
                                rowIdxArr.splice(index, 1);
                            }
                        } else {
                            // cap nhat gia
                            console.log(productExist);
                            $('#add_price_' + rowIdx).val(productExist.product_price ? productExist.product_price : 0);
                            let qty = $('#add_qty_' + rowIdx).val();
                            let price = productExist.product_price;
                            $('#add_total_' + rowIdx).val(qty * price);
                        }
                    })

                    if (productNamesRemove && productNamesRemove != '') {
                        alertJs('warning', 'Sản phẩm: <div style="color:red">&nbsp;' + productNamesRemove + '&nbsp;</div>  được xóa khỏi giỏ hàng vì không tồn tại trong báo giá mới, vui lòng chọn sản phẩm thay thế!');
                    }

                    update_sum_total();
                    reIndexRowNum();

                    $('#loading').hide();
                }
            });

        }

        $('.submit-create-order').click(function () {
            let customer_id = $('#customer_id').val();
            if (!customer_id) {
                alertJs('error', 'Chưa có thông tin khách hàng!');
                return;
            }

            let object_id = $('#object_id').val();
            let delivery_time = ($('#delivery_time').val()).split("/").reverse().join("-"); // dd/mm/yyyy -> yyyy-mm-dd
            let bill_date = ($('#bill_date').val()).split("/").reverse().join("-");
            let comment = $('#comment').val();
            let explain = $('#explain').val();

            if (rowIdxArr.length == 0) {
                alertJs('error', 'Chưa có thông tin sản phẩm!');
                return;
            }

            for (let i = 0; i < rowIdxArr.length; i++) {
                let rowIdx = rowIdxArr[i];
                let productId = $('#add_id_' + rowIdx).val();
                if (!productId) {
                    alertJs('error', 'Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!');
                    return;
                }

                let qty = $('#add_qty_' + rowIdx).val();
                let unit_type = $('#add_qty_' + rowIdx).data('unit_type');
                let minimum_qty_norm = $('#add_qty_' + rowIdx).data('minimum_qty_norm');

                if (!qty || Number(qty) == 0) {
                    alertJs('error', 'Số lượng phải lớn hơn 0, vui lòng kiểm tra lại chi tiết hóa đơn!');
                    return;
                }

                if (Number(qty) < Number(minimum_qty_norm)) {
                    alertJs('error', 'Vui lòng nhập số lượng sản phẩm lớn hơn hoặc bằng với định lượng tối thiểu của sản phẩm!');
                    return;
                }

                if (unit_type == 1 && !Number.isInteger(Number(qty))) {
                    alertJs('error', 'Vui lòng nhập số lượng sản phẩm là số nguyên!');
                    return;
                }
            }

            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Xác nhận tạo đơn hàng',
                text: "",
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            url: '{{ sc_route_admin('admin_order.order_create') }}',
                            type: "post",
                            dateType: "application/json; charset=utf-8",
                            data:
                                $('form#form-add-item').serialize()
                                + "&customer_id=" + customer_id
                                + "&delivery_time=" + delivery_time
                                + "&bill_date=" + bill_date
                                + "&comment=" + comment
                                + "&object_id=" + object_id
                                + "&explain=" + explain,
                            beforeSend: function () {
                                $('#loading').show();
                            },
                            success: function (result) {
                                $('#loading').hide();
                                if (parseInt(result.error) == 0) {
                                    window.location.href = "{{ route('admin_order.show_detail') }}/" + result.order_id;
                                    alertJs('success', result.msg);
                                } else {
                                    alertJs('error', result.msg);
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
        })
    </script>
@endpush