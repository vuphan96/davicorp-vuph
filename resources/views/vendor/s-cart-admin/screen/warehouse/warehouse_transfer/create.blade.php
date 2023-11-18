@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <!-- .card-header -->
                <div class="card-header with-border">
                    <div class="card-tools">
                        <div class="btn-group float-right" style="margin-right: 5px">
                            <a href="{{ sc_route_admin('warehouse_transfer.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->

                <!-- card-body -->
                <form action="{{ sc_route_admin('warehouse_transfer.store') }}" method="post" accept-charset="UTF-8"
                      class="form-horizontal" id="form-main">

                    <div class="card-body">
                        <div class="form-group row mt-4 ml-1">
                            <label for="address" class="col-2 col-form-label asterisk">Tên đơn chuyển hàng</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input  id="title" name="title" value="{{ old('title') }}" class="form-control title" placeholder=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-1 ml-1">
                            <label for="warehouse_id_to" class="col-2 asterisk col-form-label">Chọn kho hàng chuyển</label>
                            <div class="col-sm-8">
                                <select class="form-control customer_id select2 " style="width: 100%;" name="warehouse_id_to">
                                    @foreach ($wareHouse as $k => $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mt-1 ml-1">
                            <label for="warehouse_id_to" class="col-2 asterisk col-form-label">Chọn kho hàng nhận</label>
                            <div class="col-sm-8">
                                <select class="form-control customer_id select2 " style="width: 100%;" name="warehouse_id_from">
                                    @foreach ($wareHouse as $k => $v)
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row mt-4 ml-1">
                            <label for="reason" class="col-2 col-form-label asterisk">Lý do chuyển hàng</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="reason" value="{{ old('reason') }}" name="reason" class="form-control reason" placeholder=""/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-4 ml-1">
                            <label for="reason" class="col-2 col-form-label">Tìm đơn nhập hàng:</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="keyword" class="form-control " placeholder="Nhập tên NCC, Mã đơn hàng nhập"/>
                                    <div class="input-group-append">
                                        <button type="button" id="search_import_order" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-4 ml-1 d-none" id="div_show_error" style="margin-bottom: 0px">
                            <label for="reason" class="col-2 col-form-label"></label>
                            <div class="col-sm-8" >
                                <p class="text-red">Không tìm thấy các đơn hàng nhập phù hợp!</p>
                            </div>
                        </div>
                        <div class="form-group row mt-4 ml-1" id="div_show_error" style="margin-bottom: 0px">
                            <label for="reason" class="col-2 col-form-label">Danh sách các đơn hàng nhập:</label>
                            <div class="col-sm-8" id="add_order_import">

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-body">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table id="table-product" class="table box-body text-wrap table-bordered table-product">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: 80px; word-break: break-word" class="sku">STT</th>
                                                <th style="text-align: center; width: 120px; word-break: break-word" class="sku">Mã đơn NH</th>
                                                <th style="text-align: center; max-width: 90px; word-break: break-word" class="sku">Mã sản phẩm</th>
                                                <th style="text-align: center; min-width: 260px" class="product_name">Tên sản phẩm</th>
                                                <th style="text-align: center; width: auto" class="product_qty">Số lượng</th>
                                                <th style="text-align: center; width: auto" class="product_unit">Đơn vị tính</th>
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

                 <!-- /.card-body -->
                    @csrf
                    <!-- .card-footer -->
                    <div class="card-footer row">
                        <div class="col-md-12">
                            <div class="btn-group float-right">
                                <button type="button" class="btn btn-flat btn btn-primary submit-create-order">Tạo đơn</button>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
    @include($templatePathAdmin.'screen.warehouse.warehouse_transfer.includes.modal_import_order')

    @php
        $htmlSelectProduct =
                '<tr class="select-product">
                    <td class="check-num-order" style="text-align:center"></td>
                    <td></td>
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
                    <td><input type="number" onChange="checkQty($(this));"  class="add_qty form-control" name="qty[]" data-unit_type="22" value=""></td>
                    <td><input type="text" readonly class="add_unit form-control"  value=""></td>
                    <td><input class="add_comment form-control" name="comment[]" value="" autocomplete="off"></td>
                    <td style="text-align:center"><button onClick="$(this).parent().parent().remove(); checkNumberOrder()" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
                 </tr>';
        $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
    @endphp
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

            $('.select-product').each(function () {
                tr_length++;
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
                    if (tr_position == tr_length) {
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
        // Set datepicker format
        $( ".date_time" ).datepicker({
            dateFormat: "dd/mm/yy"
        });

        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });

        function checkNumberOrder() {
            var selected = [];
            $('.check-num-order').each(function (key) {
                $(this).text(key + 1)
            });

            return selected;
        }

        $('#add-item-button').click(function () {
            let html = '{!! $htmlSelectProduct !!}';
            $('#add-item').before(html);
            $("select.flexselect").flexselect({ hideDropdownOnEmptyInput: true });
            $("input:text:enabled:first").focus();
            $('#add-item-button-save').show();
            $('.add_id').focus();
            checkNumberOrder()
        });

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

        const formatter = new Intl.NumberFormat('en-US', {
        });

        $('#search_import_order').click(function () {
            let keyword = $('#keyword').val();
            if (keyword == '') {
                return alertJs('error', 'Vui lòng nhập từ khóa tìm kiếm');
            }
            $.ajax({
                url: '{{ sc_route_admin('warehouse_transfer.get_data_order_import') }}',
                method: "get",
                data: {
                    keyword: keyword,
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
                        $('#div_show_error').removeClass('d-none')
                        return;
                    } else {
                        if (data) {
                            let html = '';
                            for (let item of data.orderImport) {
                                html += '<tr>\n' +
                                    '<td><input style="width: 50px;height: 25px;" type="checkbox" class="import_order_id" name="import_order_id[]" value="'+item.id+'"></td>\n' +
                                    '<td>' + item.id_name + '</td>\n' +
                                    '<td>' + item.supplier_name + '</td>\n' +
                                    '<td>' + formatter.format(item.total) + '₫</td>\n' +
                                    '</tr>';
                            }
                            $('#detail_product_order_import').html(html);
                        }
                    }
                    $("#modalChooseImportOrder").attr("class", "modal fade show");
                }
            });
        })

        $('#close_modal_choose_import_order').click(function () {
            $("#modalChooseImportOrder").removeClass("show");
            $('#detail_product_order_import').children().remove();
        })

        $('#btn_choose_import_order').click(function () {
            let import_id = [];
            let flag_import_id = [];

            $('.id_import').each(function () {
                flag_import_id.push($(this).val());
            });

            $('.import_order_id:checked').each(function () {
                import_id.push($(this).val());
            });

            if (import_id.length == 0) {
                return alertJs('error', 'Vui lòng chọn it nhất một đơn hàng nhập');
            }
            $.ajax({
                url: '{{ sc_route_admin('warehouse_transfer.get_data_order_import_detail') }}',
                method: "get",
                data: {
                    import_id: import_id.join(),
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
                        alertMsg('error', 'Lỗi khi lấy chi tiết đơn nhập!', '{{ sc_language_render('action.warning') }}');
                        return;
                    } else {
                        $('#keyword').val('');
                        if (data) {
                            let orderImportDetail = data.orderImportDetail;
                            let importId = data.importId;

                            let htmlDetail = '';
                            let htmlImportOrder = '';
                            for (let item of orderImportDetail) {
                                if (!flag_import_id.includes(item.import_id)) {
                                    htmlDetail += `<tr class="select-product-${item.import_id}">
                                    <td class="check-num-order" style="text-align:center"></td>
                                    <td style="text-align:center">${item.import_id_name}</td>
                                    <td><input type="text" readonly="readonly" class="add_sku form-control" value="${item.product_code}"></td>
                                    <td id="add_td">
                                        <p>${item.product_name}</p>
                                        <input type="hidden" class="add_id" name="product_id[]" value="${item.product_id}">
                                    </td>
                                    <td><input type="number" onChange="checkQty($(this));" readonly class="add_qty form-control" name="qty[]" data-unit_type="22" value="${item.qty_order}"></td>
                                    <td><input type="text" readonly class="add_unit form-control"  value="${item.unit_name}"></td>
                                    <td><input class="add_comment form-control" name="comment[]" value="" autocomplete="off"></td>
                                    <td style="text-align:center"></td>
                                </tr>`;
                                }
                            }

                            for (const [key, itemId] of Object.entries(importId)) {
                                if (!flag_import_id.includes(itemId.import_id)) {
                                    htmlImportOrder += `<div class="import_order_${itemId.import_id}" style="display: flex">
                                                         <p class="text-blue">${itemId.import_id_name}</p>
                                                        <button type="button" onClick="removeImportOrder($(this), '${itemId.import_id}');" style="height: 25px; padding: 2px 5px; margin-left: 10px" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button>
                                                         <input type="hidden" class="id_import" name="id_order_import[]" value="${itemId.import_id}">
                                                    </div>`;
                                }
                            }

                            $('#add-item').before(htmlDetail);
                            $('#add-item-button-save').show();
                            $('#add_order_import').append(htmlImportOrder)
                            $("#modalChooseImportOrder").removeClass("show");
                            $('#detail_product_order_import').children().remove();
                            checkNumberOrder()
                        }
                    }
                }
            });
        })

        function removeImportOrder(e, id) {
            $('.select-product-'+id).remove();
            $('.import_order_'+id).remove();
            checkNumberOrder()
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

        $('.submit-create-order').click(function () {
            let submit = true;
            let reason = $('#reason').val();
            let title = $('#title').val();
            let checks = [];
            let productIds = [];

            $('.add_qty').each(function () {
                if ($(this).val() < 0) {
                    submit = false;
                    return alertJs('error', 'Số lượng nhỏ hơn 0');
                }
                checks.push($(this).val());
            });

            $('.add_id').each(function () {
                productIds.push($(this).val());
            });

            if (productIds.includes('') || productIds.length == 0) {
                submit = false;
                return alertJs('error', 'Sản phẩm không được để trống, vui lòng kiểm tra lại!');
            }
            if (checks.includes('') || checks.length == 0) {
                submit = false;
                alertJs('error', 'Số lượng sản phẩm không được để trống, vui lòng kiểm tra lại!');
            }
            if (reason == '') {
                submit = false;
                return alertJs('error', 'Lý do chuyển hàng trống!');
            }
            if (title == '') {
                submit = false;
                return alertJs('error', 'Tên đơn chuyển hàng trống!');
            }
            if (submit) {
                $('#form-main').submit();
            }
        });

        function checkQty(e) {
            let node = e.closest('tr');
            let product_unit_type = node.find('.add_qty').data('unit_type');
            let qty = node.find('.add_qty').eq(0).val();

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
            node.find('.add_total').eq(0).val(f_qty * f_price);
            product_qty_reality.val(node.find('.add_qty').val())
            update_sum_total();
        }

        function dateIsValid(dateStr) {
            const regex = /^\d{4}-\d{2}-\d{2}$/;
            if (dateStr.match(regex) === null) {
                return false;
            }
            const date = new Date(dateStr);
            const timestamp = date.getTime();
            if (typeof timestamp !== 'number' || Number.isNaN(timestamp)) {
                return false;
            }

            return date.toISOString().startsWith(dateStr);
        }

    </script>
@endpush
