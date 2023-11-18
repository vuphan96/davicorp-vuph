@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    {{-- <h2 class="card-title">{{ $title_description??'' }}</h2> --}}

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
                                                class="fa fa-plus" title="Add new"></i></button>
                                </a>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('phone') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="phone" class="col-sm-2 asterisk col-form-label">Số lượng suất ăn chính</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" min="0" onkeypress="return event.charCode >= 48" onchange="changeNumberOfServings()" id="number_of_servings" name="number_of_servings" value="" class="form-control qty" placeholder=""/>
                                </div>
                                @if ($errors->has('number_of_servings'))
                                    <span class="text-sm">
                                                {{ $errors->first('number_of_servings') }}
                                     </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('phone') ? ' text-red' : '' }} mt-4 ml-1">
                            <label for="phone" class="col-sm-2 col-form-label">Số lượng suất ăn bổ sung</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" min="0" onkeypress="return event.charCode >= 48" onchange="" id="number_of_extra_servings" name="number_of_extra_servings" value="" class="form-control qty" placeholder=""/>
                                </div>
                                @if ($errors->has('number_of_extra_servings'))
                                    <span class="text-sm">
                                                {{ $errors->first('number_of_extra_servings') }}
                                     </span>
                                @endif
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
                                    <input id="comment" value="{{ old('comment') }}" name="comment" class="form-control comment" placeholder=""/>
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
                    <label style="font-size:19px; color: blue">ĐƠN CHÍNH</label>
                    <form id="form-add-item" action="" method="">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table class="table box-body text-wrap table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: auto">STT</th>
                                                <th style="text-align: center; width: auto" class="dish_code">Mã món ăn</th>
                                                <th style="text-align: center; min-width: 165px" class="dish_name">Tên món ăn</th>
                                                <th style="text-align: center; min-width: 165px" class="product_name">Tên nguyên liệu</th>
                                                <th style="text-align: left; min-width: 165px" class="bom">Định lượng</th>
                                                <th style="text-align: left; min-width: 100px" class="qty">Số lượng</th>
                                                <th style="text-align: center; min-width: 140px" class="total_bom">Nguyên liệu suất</th>
                                                <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                                <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền Cost</th>
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
                                                        <i class="fa fa-plus"></i>
                                                        Thêm món
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

                <div class="extra-order ml-2 mt-3">
                    <label style="font-size:19px; color: blue">ĐƠN BỔ SUNG</label>
                    <form id="form-add-item-extra-order" action="" method="">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card collapsed-card">
                                    <div class="table-responsive">
                                        <table class="table box-body text-wrap table-bordered">
                                            <thead>
                                            <tr>
                                                <th style="text-align: center; width: auto">STT</th>
                                                <th style="text-align: center; max-width: 100px" class="dish_code">Mã món ăn</th>
                                                <th style="text-align: center; min-width: 190px" class="dish_name">Tên món ăn</th>
                                                <th style="text-align: center; min-width: 230px" class="product_name">Tên nguyên liệu</th>
                                                <th style="text-align: center; min-width: 140px" class="total_bom">Nguyên liệu suất</th>
                                                <th style="text-align: center; min-width: 80px" class="product_unit">Đơn vị</th>
                                                <th style="text-align: right; min-width: 135px" class="import_price">Giá nhập</th>
                                                <th style="text-align: right; min-width: 140px" class="amount_of_product_in_order">Tổng tiền Cost</th>
                                                <th style="text-align: center; min-width: 150px" class="comment">Ghi chú</th>
                                                <th style="text-align: center; width: auto" class="comment">Xóa NL</th>
                                                <th style="text-align: center; width: auto" class="delete">Xóa</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr id="add-item-extra-order" class="not-print">
                                                <td colspan="12">
                                                    <button
                                                            type="button" class="btn btn-flat btn-success"
                                                            id="add-item-button-extra-order"
                                                            title="{{sc_language_render('action.add') }}">
                                                        <i class="fa fa-plus"></i> Thêm món
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
            var number_of_servings = $('[name="number_of_servings"]').val();
            var number_of_extra_servings = $('[name="number_of_extra_servings"]').val();
            var address = $('[name="address"]').val();
            var explain = $('[name="explain"]').val();
            var delivery_time = ($('[name="delivery_time"]').val()).split("/").reverse().join("-");
            var bill_date = ($('[name="bill_date"]').val()).split("/").reverse().join("-");;
            var comment = $('[name="comment"]').val();


            if(customer_id === null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else if (!number_of_servings) {
                alertJs('error','Chưa có thông tin suất ăn chính!');
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
                                url : '{{ sc_route_admin('admin.davicook_order.order_create') }}',
                                type : "post",
                                dateType:"application/json; charset=utf-8",
                                data :
                                    $('form#form-add-item, form#form-add-item-extra-order').serialize()
                                    +"&customer_id="+customer_id
                                    +"&number_of_servings="+number_of_servings
                                    +"&number_of_extra_servings="+number_of_extra_servings
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
                                        window.location.href = "{{ route('admin.davicook_order.show_detail')}}/" + result.order_id;
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

        // Enter key for #add-item-button
        $(document).bind('keypress', function(e) {
            if(e.keyCode==13){
                $('#add-item-button').trigger('click');
            }
        });

        $('#add-item-button').click(function () {
            var cId = $('[name="customer_id"]').val();
            var nOS = $('[name="number_of_servings"]').val();
            var addButton = $(this);
            if(cId === null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            }else if(!nOS) {
                alertJs('error','Chưa có thông tin suất ăn chính!');
            }else {
                addButton.prop('disabled', true);
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_dish_by_customer_create_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        cId : cId
                    },
                    success: function(returnedData){
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#add-item').before(returnedData.dish);
                            $('.select2').select2();
                            updateDishNo();
                        }
                        addButton.prop('disabled', false);
                    }
                });
            }
        });

        // show sum total cost
        function update_sum_total_cost() {
            var sum = 0;
            $('.sum_total').each(function(){
                sum += Number(this.value.replace(/[^0-9\.-]+/g,""));
            });
            let formated = sum.toLocaleString('en-US') + '₫';
            $('.add-total').html(formated);
        }

        function selectDish(e) {
            let node = e.closest('tr');
            let dId = node.find('option:selected').eq(0).val();
            let cId = $('[name="customer_id"]').val() ;
            let number_of_servings = $('[name="number_of_servings"]').eq(0).val();
            let delivery_time = ($('[name="delivery_time"]').eq(0).val()).split("/").reverse().join("-");

            if(cId === null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            }

            if (!id) {
                node.find('.pro_by_dish').remove();
                node.find('.add_dish_code').remove();
            }else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_dish_create_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        dId : dId,
                        cId : cId,
                        number_of_servings : number_of_servings,
                        delivery_time: delivery_time,
                    },
                    beforeSend: function(){
                        $('#loading').show();
                        node.find('.pro_by_dish').remove();
                    },
                    success: function(returnedData){
                        $('#loading').hide();
                        node.find('.add_dish_code').val(returnedData.dish_code);
                        node.find('#add_td').after(returnedData.products);
                        $('.numb_of_serving').val(number_of_servings);
                        update_sum_total_cost();
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

        function changeNumberOfServings() {
            var number_of_servings = $('[name="number_of_servings"]').eq(0).val();
            var num = $('[name="number_of_servings"]').eq(0).val();
            if ($('.pro_by_dish').length > 0) {
                $('.update_numb_of_serving').val(num).trigger('change');
            }
        }

        function update_total(k,ut) {
            var product_unit_type = ut;
            var k = k;
            var bom = $('.add_bom_' + k).eq(0).val();
            var qty = $('.add_qty_' + k).eq(0).val();
            var import_price_str = $('.add_import_price_' + k).eq(0).val();
            var import_price = Number(import_price_str.replace(/[^0-9\.-]+/g,""));
            $('.add_total_bom_' + k).eq(0).val(roundTotalBom(qty * bom, product_unit_type));

            var total_cost = Math.round(roundTotalBom(qty * bom, product_unit_type) * import_price);
            let formated = total_cost.toLocaleString('en-US');
            $('.add_total_cost_' + k).eq(0).val(formated);
            update_sum_total_cost();
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
            var delivery_time = ($('[name="delivery_time"]').eq(0).val()).split("/").reverse().join("-");
            var customer_id = $('[name="customer_id"]').val() ;
            var today = new Date();
            var now = today.toISOString().split('T')[0];
            today.setDate(today.getDate() + 1);
            var tomorrow = today.toISOString().split('T')[0];
            var prev_delivery_date = e.oldvalue;

            if (dateIsValid(delivery_time)===false) {
                alertJs('error','Ngày thay đổi không hợp lệ!');
                $('#delivery_time').val(tomorrow.split("-").reverse().join("/"));
            } else if (customer_id===null || $('.pro_by_dish').length == 0) {
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
                        }else {
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
            $('.select-dish').remove();
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

        function formatNumber(n) {
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function updateDishNo() {
            $('.dish_no').each(function(i){
                $(this).html(i+1);
            });
        }

    </script>

    {{--------------------------------------------- Extra order ---------------------------------------------}}
    <script>
        function selectDishExtraOrder(e) {
            let node = e.closest('tr');
            var dish_id = node.find('option:selected').eq(0).val();
            var customer_id = $('[name="customer_id"]').val() ;
            var number_of_extra_servings = $('[name="number_of_extra_servings"]').eq(0).val();
            var delivery_time = ($('[name="delivery_time"]').eq(0).val()).split("/").reverse().join("-");

            if(customer_id === null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            }

            if (!id) {
                node.find('.td-select-dish').remove();
                node.find('.add_dish_code_extra_order').remove();
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_dish_create_extra_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        dish_id : dish_id,
                        customer_id : customer_id,
                        number_of_extra_servings : number_of_extra_servings,
                        delivery_time: delivery_time,
                    },
                    beforeSend: function(){
                        $('#loading').show();
                        node.find('.td-select-dish').remove();
                    },
                    success: function(returnedData){
                        $('#loading').hide();
                        node.find('.add_dish_code_extra_order').val(returnedData.dish_code);
                        node.find('#add_td_extra_order').after(returnedData.dish);
                        if (returnedData.error == 1) {
                            alertJs('error', returnedData.msg);
                        }
                        $('.select2').select2();
                    }
                });
            }
        }

        $('#add-item-button-extra-order').click(function () {
            var customer_id = $('[name="customer_id"]').val();
            var number_of_extra_servings = $('[name="number_of_extra_servings"]').val();
            var addButton_extra = $(this);
            if(!customer_id) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else if (!number_of_extra_servings) {
                alertJs('error','Chưa có thông tin suất ăn bổ sung!');
            } else {
                addButton_extra.prop('disabled', true);
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_dish_by_customer_create_extra_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        customer_id : customer_id
                    },
                    success: function(returnedData){
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#add-item-extra-order').before(returnedData.dish);
                            $('.select2').select2();
                            updateDishNoExtraOrder();
                        }
                        addButton_extra.prop('disabled', false);
                    }
                });
            }
        });

        function addProduct(e)
        {
            let node = e.closest('tr');
            var customer_id = $('[name="customer_id"]').val();
            var number_of_extra_servings = $('[name="number_of_extra_servings"]').val();
            var delivery_time = ($('[name="delivery_time"]').val()).split("/").reverse().join("-");
            if (customer_id == null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else if (!number_of_extra_servings) {
                alertJs('error','Chưa có thông tin suất ăn bổ sung!');
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.add_product_create_extra_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        customer_id : customer_id,
                        number_of_extra_servings : number_of_extra_servings,
                        delivery_time : delivery_time
                    },
                    success: function(returnedData){
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            node.find('.add-product-name').before(returnedData.product);
                            node.find('.add-product-total_bom').before(returnedData.total_bom);
                            node.find('.add-product-unit').before(returnedData.product_unit);
                            node.find('.add-product-import_price').before(returnedData.import_price);
                            node.find('.add-product-total_cost').before(returnedData.total_cost);
                            node.find('.add-product-comment').before(returnedData.comment);
                            node.find('.add-product-delete').before(returnedData.delete);
                            $('.select2').select2();
                        }
                    }
                });
            }
        }

        function deleteProduct(key, e)
        {
            let node = e.closest('tr');
            if (node.find('.delete-item').length <= 1) {
                node.find('.delete-item').prop("disabled", true);
            } else {
                $('.product_key_' + key).remove();
            }
            node.find('.add_number_product_extra_order').val(node.find('.selected-product').length);
        }

        function selectProductExtraOrder(key, e)
        {
            let node = e.closest('tr');
            let product_id = node.find('.add_product_id_extra_order_' + key).eq(0).val();
            let customer_id = $('[name="customer_id"]').val() ;
            var delivery_time = ($('[name="delivery_time"]').eq(0).val()).split("/").reverse().join("-");
            if (customer_id == null) {
                alertJs('error','Chưa có thông tin khách hàng!');
            } else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_order.get_product_info_create_extra_order') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        product_id : product_id,
                        customer_id : customer_id,
                        delivery_time: delivery_time
                    },
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(returnedData) {
                        $('#loading').hide();
                        if (returnedData.out_of_stock == 1) {
                            node.find('.add_total_bom_extra_order_' + key).val(0);
                            node.find('.add_total_bom_extra_order_' + key).prop('readonly', true);
                            alertJs('error', returnedData.msg_out_of_stock);
                        }
                        node.find('.add_import_price_extra_order_' + key).val((returnedData.import_price).toLocaleString('en-US')).trigger('keyup');
                        $.each(node.find('.add_import_price_extra_order_' + key).attr('class').split(' '), function(id, item) {
                            if (item.indexOf('update_change_') == 0) node.find('.add_import_price_extra_order_' + key).removeClass(item);
                        });
                        node.find('.add_import_price_extra_order_' + key).addClass('update_change_' + returnedData.product_sku);
                        node.find('.add_product_name_extra_order_' + key).val(returnedData.product_name);
                        node.find('.add_product_type_extra_order_' + key).val(returnedData.product_type);
                        node.find('.add_product_unit_extra_order_' + key).val(returnedData.product_unit);
                        node.find('.delete-item').addClass('selected-product');
                        node.find('.add_number_product_extra_order').val(node.find('.selected-product').length);
                    }
                });
            }
        }

        function update_total_extra_order(key) {
            var total_bom_extra_order = $('.add_total_bom_extra_order_' + key).eq(0).val();
            var import_price_extra_order_str = $('.add_import_price_extra_order_' + key).eq(0).val();
            var import_price_extra_order = Number(import_price_extra_order_str.replace(/[^0-9\.-]+/g,""));
            var total_cost_extra_order = Math.round(total_bom_extra_order * import_price_extra_order);
            let formated = total_cost_extra_order.toLocaleString('en-US');
            $('.add_total_cost_extra_order_' + key).eq(0).val(formated);
            update_sum_total_cost();
        }

        function updateDishNoExtraOrder() {
            $('.dish_no_extra_order').each(function(i){
                $(this).html(i+1);
            });
        }
    </script>
    {{--------------------------------------------- End Extra order ---------------------------------------------}}

@endpush
