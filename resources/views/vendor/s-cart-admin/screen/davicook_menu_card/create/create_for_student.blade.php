@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    {{-- <h2 class="card-title">{{ $title_description??'' }}</h2> --}}

                    <div class="card-tools">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ sc_route_admin('admin.davicook_menu_card.index') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                @php
                    $tomorrow = date('d/m/Y', strtotime('now'));
                @endphp
                <form id="form-submit-all-menu-card" action="{{ sc_route_admin('admin.davicook_menu_card.store_menu_card') }}" method="post" class="form-horizontal"
                      id="form-main" accept-charset="UTF-8" enctype='multipart/form-data'>
                    @csrf
                    <input type="hidden" name="type_object" value="2">
                    <div class="" style="display: flex">
                        <div class="col-sm-7" >
                            <table class="table box-body text-wrap table-bordered">
                                <tr>
                                    <td class="td-title">Mã phiếu</td>
                                    <td>
{{--                                        <input type="text">--}}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Tên phiếu</td>
                                    <td>
                                        <input id="card_name" name="card_name" value="" class="form-control card_name"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Chọn khách hàng</td>
                                    <td>
                                        <select onChange="changeCustomer();" class="form-control customer_id select2" style="width: 100%;" name="customer_id" id="customer_id">
                                            <option selected disabled hidden value="">{{ sc_language_render('order.admin.select_customer') }}</option>
                                            @foreach ($customers as $k => $v)
                                                <option value="{{ $v->id }}">{{ $v->name.' ('.$v->customer_code.')' }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Ngày bắt đầu</td>
                                    <td>
                                        <input type="text" style="width: 200px;" id="start_date"
                                               name="start_date"
                                               value="{{ $tomorrow }}"
                                               onfocus="this.oldvalue = this.value"
                                               class="form-control start-date date_time"
                                               data-date-format="dd/mm/yyyy"
                                               placeholder="Chọn ngày"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Ngày kết thúc</td>
                                    <td>
                                        <input type="text" style="width: 200px;" id="end_date"
                                               name="end_date"
                                               value="{{ $tomorrow }}"
                                               onfocus="this.oldvalue = this.value"
                                               class="form-control input-fat end-date date_time"
                                               data-date-format="dd/mm/yyyy"
                                               placeholder="Chọn ngày"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Số tuần</td>
                                    @php $year = now()->year @endphp
                                    <td>
                                        <input type="number" min="0" oninput="validity.valid||(value='');" style="width: 200px" id="week_no"
                                               name="week_no" value="" placeholder="Nhập số tuần năm {{ $year }}"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Số lượng suất ăn mỗi phiếu</td>
                                    <td>
                                        <input type="number" min="0" oninput="validity.valid||(value='');" style="width: 200px"
                                               value="" id="num_for_menu_card" placeholder="Nhập số suất ăn"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title"></td>
                                    <td>
                                        <input type="button" id="create_menu_card_children" class="" value="Tạo mới phiếu con">
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-sm-5" style="height: 350px">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="td-title">Tổng tiền cost</td>
                                    <td><input readonly id="total_cost" name="total_cost" value="" class="form-control card_name"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="td-title">Tổng suất ăn</td>
                                    <td><input readonly id="total_number_of_servings" name="total_number_of_servings" value="" class="form-control card_name"/></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div id="menu-card">
{{--                        Body --}}
                    </div>

                </form>

            </div>

            <!-- /.card-footer -->
            {{--                     Nút đặt hàng--}}
            <div class="card-footer row">
                <div class="col-md-12">
                    <div class="btn-group float-right">
                        <button type="button" disabled class="btn btn-flat btn btn-primary submit-create-order">Đặt hàng</button>
                    </div>
                </div>
            </div>
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
        .td-title {
            width: 170px!important;
        }
        .total_bom {
            min-width: 120px;
            max-width: 120px;
            white-space: normal;
        }
        .bom {
            min-width: 120px;
            max-width: 120px;
            white-space: normal;
        }

        .main-sub-menu-card-by-date {
            margin-top: 90px;
        }

        .input-readonly {
            margin-bottom: 0px!important;
            background-color: #e9ecef;
        }

        #dish_code {
            padding: 0;
            padding-left: 5px;
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

        $(document).ready(function () {
            $('.select2').select2()
        });

        $('[name="customer_id"]').change(function () {
            addInfo();
        });

        // Check remove dom
        function checkRemoveDOM(menu_card_id) {

        }

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
                        $('#loading').hide();
                    }
                });
            } else {
                $('#form-main').reset();
            }

        }

        /**
         * Tạo các phiếu con theo từng ngày.
         */
        $('#create_menu_card_children').click(function () {
            let arrDate = [];
            let number_meal = $('#num_for_menu_card').val();
            let customer = $('#customer_id').val();
            let card_name = $('#card_name').val();
            let start_date = new Date($('#start_date').val().split("/").reverse().join("-"));
            // let year = new Date(start_date.getFullYear(), 0, 1);
            // let days = Math.floor((start_date - year) / (24 * 60 * 60 * 1000));
            // let week = Math.ceil(( start_date.getDay() + 1 + days) / 7);
            let end_date = new Date($('#end_date').val().split("/").reverse().join("-"));
            if (start_date > end_date) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Ngày bắt đầu phải nhỏ hơn ngày kết thúc!');
            }
            var diffDate = (end_date - start_date)/86400000;
            for ($i = 0; $i <= diffDate; $i++) {
                arrDate[$i] = addDays(start_date, $i)
            }

            if(diffDate > 6) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Khoản ngày quá lớn vui lòng chọn lại!');
            }

            if (number_meal <= 0) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Vui lòng nhập số suất ăn');
            }

            if(!customer) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa chọn khách hàng!');
            }

            if(!card_name) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa nhập tên phiếu!');
            }

            if ($('div').hasClass('main-sub-menu-card-by-date')) {
                Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                }).fire({
                    title: 'Đã tạo phiếu con. Xác nhận tạo mới lại!',
                    text: "Lưu ý: Các phiếu tạo trước đó sẽ bị xóa đi",
                    showCancelButton: true,
                    confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                    reverseButtons: true,
                    preConfirm: function () {
                        $('.main-sub-menu-card-by-date').remove();
                        createSubMenuCardWithDay(arrDate, number_meal);
                    }

                }).then((result) => {
                })
            } else {
                createSubMenuCardWithDay(arrDate, number_meal)
            }
            $('.submit-create-order').attr('disabled', false);
        });

        /**
         * Lọc từng ngày
         * @param date
         * @param days
         * @returns {string}
         */
        function addDays(date, days) {
            const dateCopy = new Date(date);
            dateCopy.setDate(date.getDate() + days);
            return dateCopy.toLocaleDateString('vi');
        }

        /**
         * Tạo các phiếu con từ mảng Date
         * @param arrDate
         * @param number_meal
         */
        function createSubMenuCardWithDay(arrDate, number_meal) {
            let html = '';
            let sum_number_meal = 0;
            for (const [key, value] of Object.entries(arrDate)) {
                sum_number_meal += parseInt(number_meal);
                html += '<div class="main-sub-menu-card-by-date mt-10 mb-5" id="sub_card_' + key + '" style="margin-top: 90px;">\n' +
                            '<div class="main-order ml-2 mt-5">\n' +
                                '<div class="title-sub-card d-flex mb-2 mt-2 pr-2">\n' +
                                    '<div class="col-sm-4">\n' +
                                        '<lable>Ngày</lable>\n' +
                                        '<input type="text" readonly name="date['+key+']" value="'+ value +'" id="date_sub_card_'+key+'" class="date_time">\n' +
                                    '</div>\n' +
                                    '<div class="col-sm-4">\n' +
                                        '<lable>Số lượng suất ăn</lable>\n' +
                                        '<input type="number" min="1" class="check-number-of-servings" id="number_of_servings_sub_card_'+key+'" oninput="changeNumberOfServings('+key+'); updateSubTotalCostAndTotalCost('+key+')" name="number_of_servings['+key+']" value="'+number_meal+'">\n' +
                                    '</div>\n' +
                                    '<div class="col-sm-4 " style="text-align: right">\n' +
                                        '<input type="button" class="btn-danger btn-remove-item-card" onclick="removeItemSubCard('+key+');" value="Xóa phiếu">\n' +
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
                                                        '<th style="text-align: center; max-width: 55px!important; min-width: 55px;">STT</th>\n' +
                                                        '<th style="text-align: center; max-width: 120px!important; min-width: 120px; white-space: normal;" class="dish_delivery_time">Ngày giao hàng</th>\n' +
                                                        '<th style="text-align: center; max-width: 85px!important; min-width: 85px; white-space: normal;" class="dish_code">Mã món ăn</th>\n' +
                                                        '<th style="text-align: center; min-width: 175px" class="dish_name">Tên món ăn</th>\n' +
                                                        '<th style="text-align: center; min-width: 120px" class="product_gift">Loại</th>\n' +
                                                        '<th style="text-align: center; max-width: 120px!important; min-width: 120px; white-space: normal;" class="product_delivery_time">Ngày</th>\n' +
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
                                                    '<tr id="add-item_'+key+'" class="not-print">\n' +
                                                        '<td colspan="12">\n' +
                                                            '<button type="button" class="btn btn-flat btn-success" onclick="getSelectDishByCustomer('+key+');" id="btn_add_menu_key_' + key + '"><i class="fa fa-plus"></i>Thêm món</button>\n' +
                                                        '</td>\n' +
                                                    '</tr>\n' +
                                                '</tbody>\n' +
                                            '</table>\n' +
                                        '</div>\n' +
                                    '</div>\n' +
                                '</div>\n' +
                            '</div>\n' +

                            '<div class="sub-total-cost">\n' +
                                '<div class="btn-group float-right">\n' +
                                    '<h6 style="font-weight: bold">Tổng tiền giá vốn:</h6>\n' +
                                        '<input type="hidden" class="" name="sub_total_cost['+key+']"  id="number_sub_total_cost_'+key+'" value="0">\n' +
                                        '<input type="text" class="sub_total_cost sub_total_cost_key_'+key+'" readonly id="sub_total_cost_'+key+'" style="font-weight: bold; margin: 0px 13px">\n' +
                                '</div>\n' +
                            '</div>\n' +
                        '</div>'
                ;
            }
            $('#menu-card').append(html)
            $('#total_number_of_servings').val(sum_number_meal);
        }

        /**
         * Xóa các phiếu con.
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
                    $('#sub_card_'+ key).remove();
                }
            })
        }

        /**
         * Select món ăn theo khách hàng
         * @param key
         */
        function getSelectDishByCustomer(key)
        {
            let customer_id = $('[name="customer_id"]').val();
            let type_object = $('[name="type_object"]').val();
            let number_of_servings = $('#number_of_servings_sub_card_'+key).val();
            if(number_of_servings <= 0) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Vui lòng nhập số lượng suất ăn!');
            }
            $('#btn_add_menu_key_' + key).prop('disabled', true);
            if(customer_id === null) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa có thông tin khách hàng!');
            }else {
                $.ajax({
                    url : '{{ sc_route_admin('admin.davicook_menu_card.get_dish_by_customer') }}',
                    type : "get",
                    dateType:"application/json; charset=utf-8",
                    data : {
                        customer_id : customer_id,
                        type_object : type_object,
                        key : key,
                    },
                    beforeSend: function(){
                        $('#loading').show();
                    },
                    success: function(returnedData){
                        $('#loading').hide();
                        $('#btn_add_menu_key_' + key).prop('disabled', false);
                        if (returnedData.error == 1) {
                            alertMsg('error', returnedData.msg);
                        } else {
                            $('#add-item_' + key).before(returnedData.dish);
                            updateDishNo(key);
                            $('.select2').select2();
                        }
                    }
                });
            }
            $('.select2_'+key).select2();

        }

        /**
         * Request lấy nguyên liệu theo món ăn đã chọn
         * @param e
         * @param key
         */
        function getProductBySelectDish(e, key) {
            let arrDish = [];
            let node = e.closest('tr');
            let add_delivery_time_for_dish = node.find('.add_date_for_dish_'+key);
            add_delivery_time_for_dish.children().remove();
            $('.select_add_dish_id_'+key).each(function () {
                if ($(this).val() != null && $(this).val() !== '') {
                    arrDish.push($(this).val());
                }
            })
            let uniqueArrDish = unique(arrDish);
            if (arrDish.length !== uniqueArrDish.length) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Món ăn đã tồn tại. Vui lòng chọn món khác!');
            }
            let dish_id = node.find('.select_add_dish_id_'+key).val();
            let is_spice = node.find('.select_add_is_spice_'+key).val();
            let customer_id = $('[name="customer_id"]').val() ;
            let number_of_servings = $('#number_of_servings_sub_card_'+key).val();
            let date = $('#date_sub_card_'+key).val();
            let type_object = $('[name="type_object"]').val();
            if(customer_id == null) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Chưa có thông tin khách hàng!');
            }
            if(number_of_servings <= 0) {
                return alertMsg('error', 'Lỗi tạo phiếu', 'Vui lòng nhập số lượng suất ăn!');
            }
            $.ajax({
                url : '{{ sc_route_admin('admin.davicook_menu_card.get_product_by_dish') }}',
                type : "get",
                dateType:"application/json; charset=utf-8",
                data : {
                    dish_id : dish_id,
                    is_spice : is_spice,
                    key : key,
                    customer_id : customer_id,
                    number_of_servings : number_of_servings,
                    date: date,
                    type_object : type_object,
                },
                beforeSend: function(){
                    $('#loading').show();
                    node.find('.pro_by_dish_'+key).remove();
                },
                success: function(returnedData){
                    $('#loading').hide();
                    if (returnedData.error == 1) {
                        alertJs('error', returnedData.msg);
                    }
                    add_delivery_time_for_dish.append(
                        '<input type="text" name="delivery_time['+key+']['+returnedData.dish_id+']" value="'+ date +'" id="delivery_time_'+key+'_'+returnedData.dish_id+'" class="form-control date_time">'
                    )
                    node.find('.add_dish_code_'+ key).val(returnedData.dish_code);
                    node.find('#add_td_product_' + key).after(returnedData.products);
                    // $('.number_of_servings' + key).val(number_of_servings);

                    if (returnedData.msgProductOff) {
                        CustomShowMsgAlertJs('error', returnedData.msgProductOff);
                    }

                    updateSubTotalCostAndTotalCost(key);
                    $( ".date_time" ).datepicker({
                        dateFormat: "dd/mm/yy"
                    });
                }
            });

        }

        /**
         * Function xử lý khi thay đổi số lượng suất ăn
         */
        function changeNumberOfServings(key) {
            let sum = 0;
            var num = $('#number_of_servings_sub_card_'+key).val();
            if ($('.pro_by_dish_'+key).length > 0) {
                $('.change_num_of_ser_'+key).val(num).trigger('change');
            }

            $('.check-number-of-servings').each(function () {
                sum = sum + parseFloat($(this).val());
            });

            $('#total_number_of_servings').val(sum);
        }
        /**
         * Function xử lý cập nhập lại :
         * Nguyên liệu suất
         * Giá tiền cost
         */
        function updateTotalAmountInline(key_card, key_index, unit_type) {
            var product_unit_type = unit_type;
            var bom = $('.add_bom_' + key_card + '_'+key_index).eq(0).val();
            var qty = $('#number_of_servings_sub_card_' + key_card).val();
            let import_price = $('.add_import_price_' + key_card + '_' + key_index).eq(0).val();

            $('.add_total_bom_' + key_card + '_'+key_index).eq(0).val(roundTotalBom(qty * bom, product_unit_type));

            var total_cost = Math.round(roundTotalBom(qty * bom, product_unit_type) * import_price);
            let formated = total_cost.toLocaleString('en-US');
            $('.number_amount_of_product_in_order_' + key_card + '_' + key_index).eq(0).val(total_cost);
            $('.amount_of_product_in_order_' + key_card + '_' + key_index).eq(0).html(formated);
            // updateSubTotalCostAndTotalCost(key_card, key_index);
        }

        /**
         *Update giá tiền cost từng phiếu và tổng tiền tất cả
         */
         function updateSubTotalCostAndTotalCost(key) {
            let sum = 0;
            let total_cost = 0;

            $('.number_amount_of_product_in_order_'+key).each(function(){
                sum = sum + parseFloat($(this).val());
            });
            $('#number_sub_total_cost_'+key).val(sum);

            sum = sum.toLocaleString('en-US') + '₫';
            $('#sub_total_cost_'+key).val(sum);

            $('.number_amount_of_product_in_order').each(function(){
                total_cost += parseFloat($(this).val());
            });
            total_cost = total_cost.toLocaleString('en-US') + '₫';
            $('#total_cost').val(total_cost);
        }

        /**
         * Check số thứ tự
         * @param key
         */
        function updateDishNo(key) {
            $('.dish_no_' + key).each(function(i){
                $(this).html(i+1);
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

        $('.submit-create-order').click(function () {
            let _token = $("input[name='_token']").val();
            let customer_id = $('#customer_id').val();
            let number_of_servings = 100;
            let flag = $('div').hasClass('main-sub-menu-card-by-date');
            let flag_not_product = false;
            $('.main-sub-menu-card-by-date').each(function (i) {
               if (!$('div').hasClass('add_pro_id_'+i)) {
                   return flag_not_product = true;
               }
            })

            if (flag_not_product) {
                return alertMsg('error','Chưa tạo sản phẩm cho phiếu con!');
            }

            if (!flag) {
                return alertMsg('error','Chưa tạo phiếu con!');
            }

            if(customer_id === null) {
                return alertJs('error','Chưa có thông tin khách hàng!');
            } else if (!number_of_servings) {
                return alertJs('error','Chưa có thông tin suất ăn chính!');
            } else {
                Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                }).fire({
                    title: 'Xác nhận tạo Phiếu',
                    text: "",
                    showCancelButton: true,
                    confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                    reverseButtons: true,

                    preConfirm: function () {
                        $('#loading').show();
                        $('#form-submit-all-menu-card').submit();
                    }
                }).then((result) => {
                })
            }
        });

        // Enter key for #add-item-button
        $(document).bind('keypress', function(e) {
            if(e.keyCode==13){
                $('#add-item-button').trigger('click');
            }
        });

        function formatMoney(number) {
            return number.toLocaleString();
        }

        function changeCustomer() {
            $('.select-dish').remove();
            updateSubTotalCostAndTotalCost();
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


    </script>
@endpush
