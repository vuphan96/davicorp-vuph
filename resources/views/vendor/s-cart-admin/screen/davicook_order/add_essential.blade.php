@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ sc_route_admin('admin.davicook_order.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form id="form-submit-order" action="" method="" class="form-horizontal" id="form-main" accept-charset="UTF-8">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('customer_id') ? ' text-red' : '' }} mt-1 ml-1">
                            <label for="customer_id" class="col-sm-2 asterisk col-form-label">Chọn khách hàng</label>
                            <div class="col-sm-8">
                                <select onChange="changeCustomer();" class="form-control customer_id select2" style="width: 100%;" name="customer_id">
                                    <option selected disabled hidden value="">{{ sc_language_render('order.admin.select_customer') }}</option>
                                    @foreach ($customers as $k => $v)
                                        <option value="{{ $v->id }}">{{ $v->name.' ('.$v->customer_code.')' }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('customer_id'))
                                    <span class="text-sm">
                                        {{ $errors->first('customer_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="input-group-append">
                                <a href="{{ sc_route_admin('admin.davicook_customer.create') }}">
                                    <button type="button" id="button-filter" class="btn btn-success  btn-flat"><i
                                                class="fa fa-plus" title="Add new"></i>
                                    </button>
                                </a>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('address') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="address" class="col-sm-2 col-form-label">Địa chỉ</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input readonly id="address" name="address" value="{{ old('address') }}" class="form-control address" placeholder=""/>
                                </div>
                                @if ($errors->has('address'))
                                    <span class="text-sm">
                                        {{ $errors->first('address') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row  {{ $errors->has('explain') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="explain" class="col-sm-2 asterisk col-form-label">Diễn giải</label>
                            <div class="col-sm-8">
                                <select class="form-control explain " style="width: 100%;" name="explain" >
                                    @foreach ($orderExplains as $explain)
                                        <option value="{{ $explain }}">{{ $explain }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('explain'))
                                    <span class="text-sm">
                                        {{ $errors->first('explain') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        @php
                            $tomorrow = date('d/m/Y', strtotime('tomorrow'));
                        @endphp
                        <div class="form-group row {{ $errors->has('delivery_time') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="delivery_time" class="col-sm-2 asterisk col-form-label">Ngày giao hàng</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input onchange="changeDeliveryTime(this)" type="text" style="width: 100px;" id="delivery_time"
                                           name="delivery_time"
                                           value="{{ $tomorrow }}"
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm delivery_time date_time"
                                           data-date-format="dd/mm/yyyy"
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
                            <label for="bill_date" class="col-sm-2 asterisk col-form-label">Ngày trên hóa đơn</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input onchange="changeBillDate(this)" type="text" style="width: 100px;" id="bill_date"
                                           name="bill_date"
                                           value="{{ $tomorrow }}"
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm bill_date date_time"
                                           data-date-format="dd/mm/yyy"
                                           placeholder="Chọn ngày"/>
                                </div>
                                @if ($errors->has('bill_date'))
                                    <span class="text-sm">
                                        {{ $errors->first('bill_date') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('comment') ? ' text-red' : '' }} mt-4 ml-1 mb-4">
                            <label for="address" class="col-sm-2 col-form-label">Ghi chú</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="comment" value="{{ old('comment') }}" name="comment" class="form-control comment"/>
                                </div>
                                @if ($errors->has('comment'))
                                    <span class="text-sm">
                                        {{ $errors->first('comment') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <hr>
                </form>

                <div class="main-order ml-2 mt-5">
                    <label style="font-size:19px; color: blue">NHU YẾU PHẨM</label>
                    <form id="form-add-item" action="" method="">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table class="table box-body text-wrap table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: auto" class="stt">STT</th>
                                                <th style="text-align: center; width: auto" class="product_id">Mã sản phẩm</th>
                                                <th style="text-align: center; min-width: 220px" class="product_name">Tên sản phẩm</th>
                                                <th style="text-align: center; min-width: 165px" class="qty">Số lượng</th>
                                                <th style="text-align: center; min-width: 165px" class="product_unit">Đơn vị</th>
                                                <th style="text-align: center; min-width: 165px" class="import_price">Giá nhập</th>
                                                <th style="text-align: center; min-width: 165px" class="total_cost">Tổng tiền</th>
                                                <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                                <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr id="add-item" class="not-print">
                                                <td colspan="11">
                                                    <button
                                                            type="button" class="btn btn-flat btn-success"
                                                            id="add-item-button"
                                                            title="{{sc_language_render('action.add') }}">
                                                        <i class="fa fa-plus"></i> Thêm sản phẩm
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
                </div>
            </div>
            <!-- /.card-body -->
            <div class="">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <h6 style="font-weight: bold">Tổng tiền giá vốn:</h6>
                        <h6 class="add-total" style="font-weight: bold; margin: 0px 13px">{{ sc_currency_render($total_cost ?? 0, 'vnd') }}</h6>
                    </div>
                </div>
            </div>
            <div class="card-footer row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <button type="submit" class="btn btn-flat btn btn-primary submit-create-order">Đặt hàng</button>
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

        @media (min-width: 768px) {
            .box-body td, .box-body th {
                word-break: break-word;
            }
        }

        @media screen and (max-width: 810px) {
            table.list_table tr td:last-child {
                width: 1%;
            }
            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
            }
        }

        th {
            white-space: nowrap;
        }

        .pro_by_dish input {
            text-overflow: ellipsis;
        }

        .select-dish select option {
            text-overflow: ellipsis;
        }

        .pro_by_dish input {
            margin-bottom: 5px;
        }

        .amount_of_product_in_order, .import_price {
            text-align: right;
        }

    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script type="text/javascript">
        //Set datepicker format
        $( ".date_time" ).datepicker({
            dateFormat: "dd/mm/yy"
        });

        $(document).ready(function () {
            $('.select2').select2()
        });
        $('[name="customer_id"]').change(function () {
            addInfo();
        });

        function addInfo() {
            id = $('[name="customer_id"]').val();
            if (id) {
                $.ajax({
                    url: '{{ sc_route_admin('admin.davicook_order.customer_info') }}',
                    type: "get",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id
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
                    }
                });
            } else {
                $('#form-main').reset();
            }
        }

        $('.submit-create-order').click(function () {
            var _token = $("input[name='_token']").val();
            var customer_id = $('[name="customer_id"]').val();
            var address = $('[name="address"]').val();
            var explain = $('[name="explain"]').val();
            var delivery_time = ($('[name="delivery_time"]').val()).split("/").reverse().join("-");
            var bill_date = ($('[name="bill_date"]').val()).split("/").reverse().join("-");;
            var comment = $('[name="comment"]').val();

            if(!customer_id) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else {
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
                                url : '{{ sc_route_admin('admin.davicook_order.essential_order_create') }}',
                                type : "post",
                                dateType:"application/json; charset=utf-8",
                                data :
                                    $('form#form-add-item, form#form-add-item-extra-order').serialize()
                                    +"&customer_id="+customer_id
                                    +"&address="+address
                                    +"&delivery_time="+delivery_time
                                    +"&bill_date="+bill_date
                                    +"&comment="+comment
                                    +"&explain="+explain
                                    +"&_token="+_token,
                                beforeSend: function(){
                                    $('#loading').show();
                                },
                                success: function(result){
                                    $('#loading').hide();
                                    if (parseInt(result.error) == 0) {
                                        window.location.href = "{{ route('admin.davicook_order.show_essential_order_detail')}}/" + result.order_id;
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
                        // Read more about handling dismissals
                        result.dismiss === Swal.DismissReason.cancel
                    ) { }
                })
            }
        });

        $(document).bind('keypress', function(e) {
            if (e.keyCode == 13) {
                $('#add-item-button').trigger('click');
            }
        });

        $('#add-item-button').click(function () {
            var customer_id = $('[name="customer_id"]').val();
            var addButton = $(this);
            if (!customer_id) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else {
                addButton.prop('disabled', true);
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_create_essential_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        customer_id : customer_id
                    },
                    success: function(returnedData){
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#add-item').before(returnedData.product);
                            $('.select2').select2();
                            updateProductNo();
                        }
                        addButton.prop('disabled', false);
                    }
                });
            }
        });

        function updateProductNo() {
            $('.product_no').each(function(i){
                $(this).html(i+1);
            });
        }

        function update_total(key) {
            var qty = $('.add_product_qty_' + key).val();
            var import_price_str = $('.add_import_price_' + key).eq(0).val();
            var import_price = Number(import_price_str.replace(/[^0-9\.-]+/g,""));

            var total = Math.round(qty * import_price);
            let formated = total.toLocaleString('en-US');
            $('.add_total_' + key).val(formated);
            update_sum_total_cost();
        }

        function update_sum_total_cost() {
            var sum = 0;
            $('.sum_total').each(function(){
                sum += Number(this.value.replace(/[^0-9\.-]+/g,""));
            });
            let formated = sum.toLocaleString('en-US') + '₫';
            $('.add-total').html(formated);
        }

        function selectProduct(key, e) {
            let node = e.closest('tr');
            var product_id = node.find('option:selected').eq(0).val();
            if (product_id == 0) {
                node.remove();
                return alertJs('error','Sản phẩm hết hàng!');
            }
            var customer_id = $('[name="customer_id"]').val() ;
            var delivery_time = ($('[name="delivery_time"]').eq(0).val()).split("/").reverse().join("-");

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
                        update_sum_total_cost();
                    }
                });
            }
        }

        function formatMoney(number) {
            return number.toLocaleString();
        }

        function changeBillDate(e) {
            var today = new Date();
            var now = today.toISOString().split('T')[0];
            today.setDate(today.getDate() + 1);
            var tomorrow = today.toISOString().split('T')[0];
            var prev_bill_date = e.oldvalue;
            var bill_date = ($('[name="bill_date"').eq(0).val()).split("/").reverse().join("-");

            if (dateIsValid(bill_date)===false) {
                alertJs('error','Ngày thay đổi không hợp lệ!');
                $('#bill_date').val(tomorrow.split("-").reverse().join("/"));
            }
        }

        function changeDeliveryTime(e) {
            var delivery_time = ($('[name="delivery_time"').eq(0).val()).split("/").reverse().join("-");
            var customer_id = $('[name="customer_id"]').val() ;
            var today = new Date();
            var now = today.toISOString().split('T')[0];
            today.setDate(today.getDate() + 1);
            var tomorrow = today.toISOString().split('T')[0];
            var prev_delivery_date = e.oldvalue;

            if (dateIsValid(delivery_time)===false) {
                alertJs('error','Ngày thay đổi không hợp lệ!');
                $('#delivery_time').val(tomorrow.split("-").reverse().join("/"));
            } else if (customer_id == null || $('.select-product').length == 0) {
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.update_price_by_delivery_time') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data :
                        $('form#form-add-item').serialize()
                        +"&delivery_time="+delivery_time
                        +"&customer_id="+customer_id,
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(returnedData){
                        $('#loading').hide();
                        if (returnedData.error == 1) {
                            for (const key in returnedData) {
                                $('.update_change_' + key).val(returnedData[key]).trigger('keyup');
                            }
                            alertJs('error', returnedData.msg);
                            update_sum_total_cost();
                        } else {
                            for (const key in returnedData) {
                                $('.update_change_' + key).val(returnedData[key]).trigger('keyup');
                            }
                            alertJs('success', returnedData.msg);
                            update_sum_total_cost();
                        }
                    }
                });
            }
        }

        function changeCustomer() {
            $('.select-product').remove();
            update_sum_total_cost();
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

        // Set limit decimal range input
        function limitDecimalPlaces(e, count) {
            if (e.target.value < 0) { e.target.value = Math.abs(e.target.value)}
            if (e.target.value.indexOf('.') == -1) { return; }
            if ((e.target.value.length - e.target.value.indexOf('.')) > count)
            {
                e.target.value = parseFloat(e.target.value).toFixed(count);
            }
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

        function checkRemoveDOM() {

        }

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }
    </script>

@endpush
