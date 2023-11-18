@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <form class="row" id="order_edit_form" method="post"
                      action="{{ sc_route_admin('admin_holiday.update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $editOrderWithWeekend->id }}">
                    <div class="col-sm-12 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <th class="td-title" colspan="2">Khóa đặt hàng vào T7, CN</th>
                            </tr>
                            <tr>
                                <td style="width: 80%; border-right: none">Từ 11h trưa thứ 7 đến hết ngày CN, không cho phép đặt đơn có ngày giao hàng của đơn vào chủ nhật và thứ 2 tuần tới</td>
                                <td style="border: none;" class="bootstrap-switch">
                                    <input class="order_check" type="checkbox" {{ $editOrderWithWeekend->status == 1 ? 'checked' : '' }}  data-toggle="toggle" data-id="{{ $editOrderWithWeekend->id }}" data-style="ios">
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>


                <form id="form-add-item" action="" method="">
                    @csrf
                    {{--                    <input type="hidden" name="order_id" value="{{ $listHolidays->id }}">--}}
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table id="table-product" class="table table-hover box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th class="td-title" colspan="5">Thời gian nghỉ lễ</th>
                                        </tr>
                                        <tr>
                                            {{--                                            <th style="min-width: 45px; padding: 5px; vertical-align: middle; text-align: center">STT</th>--}}
                                            <th style="min-width: 270px; word-break: break-word">Tên kỳ nghỉ</th>
                                            <th style="width: auto; min-width: 100px">Thời gian bắt đầu</th>
                                            <th style="min-width: 100px;word-break: break-word" >Thời gian kết thúc</th>
                                            <th style="max-width: 160px;word-break: break-word; text-align: center" >Trạng thái</th>
                                            <th style="min-width: 85px;word-break: break-word; text-align: center">{{ sc_language_render('action.title') }}</th>

                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($listHolidays as $item)
                                            <tr class="ordered-products">
                                                {{--                                                <td style="text-align: center">{{ $i++ }}</td>--}}
                                                <td class="overflow_prevent">
                                                    <a  href="#"
                                                        class="edit-item-detail"
                                                        data-value="{{ $item->name ?? ''}}"
                                                        data-name="name" data-type="text"
                                                        data-emptytext="Trống"
                                                        data-pk="{{ $item->id ?? ''}}"
                                                        data-url="{{ route("admin_holiday.update") }}"
                                                        data-title="Tên kỳ nghỉ">{{ $item->name }}</a>
                                                </td>
                                                <td class="product_qty"><a style="font-weight: bold"  href="#"
                                                                           class="edit-item-detail date_time"
                                                                           data-value="{{ $item->start_date }}" data-name="start_date"
                                                                           data-type="date"
                                                                           data-pk="{{ $item->id }}"
                                                                           data-url="{{ route("admin_holiday.update") }}"
                                                                           data-title="Ngày bắt đầu"> {{ date('d/m/Y', strtotime($item->start_date ?? '')) }}</a>
                                                </td>
                                                <td class="product_qty"><a style="font-weight: bold" href="#"
                                                                           class="edit-item-detail date_time"
                                                                           data-value="{{ $item->end_date }}" data-name="end_date"
                                                                           data-type="date"
                                                                           data-pk="{{ $item->id }}"
                                                                           data-url="{{ route("admin_holiday.update") }}"
                                                                           data-title="Ngày kết thúc"> {{ date('d/m/Y', strtotime($item->end_date ?? ''))  }}</a>
                                                </td>
                                                <td class="product_qty" style="cursor: pointer; text-align: center">
                                                    <input id="order_check" class="order_check " type="checkbox" {{ $item->status == 1 ? 'checked' : '' }}  data-toggle="toggle" data-id="{{ $item->id }}" data-style="ios">
                                                </td>
                                                <td style="text-align: center">
                                                    <span data-perm="order:edit_info"
                                                          onclick="deleteItem('{{ $item->id }}');"
                                                          class="btn btn-danger btn-xs" data-title="Delete"><i
                                                                class="fa fa-trash" aria-hidden="true"></i></span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr id="add-item" class="not-print">
                                            <td colspan="8">
                                                <button data-perm="order:edit_info" type="button"
                                                        class="btn btn-flat btn-success"
                                                        id="add-item-button"
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

                <form class="row" id="order_edit_form" method="post"
                      action="{{ sc_route_admin('admin_holiday.update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $getBlockByDateRange->id }}">
                    <div class="col-sm-12 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <th class="th-title">Khóa đặt hàng trên Web đặt hàng và App đặt hàng</th>
                                <th style="width: auto; min-width: 160px">Thời gian bắt đầu</th>
                                <th style="min-width: 160px;word-break: break-word" >Thời gian kết thúc</th>
                                <th style="max-width: 130px;word-break: break-word; text-align: center" >Trạng thái</th>
                            </tr>
                            <tr>
                                <td style="border-right: none">Khóa đặt hàng những đơn có ngày giao hàng, ngày đặt hàng nằm trong khoảng thời gian hiệu lực</td>
                                <td class="product_qty"><a style="font-weight: bold"  href="#"
                                                           class="edit-item-detail date_time"
                                                           data-value="{{ $getBlockByDateRange->start_date }}" data-name="start_date"
                                                           data-type="date"
                                                           data-pk="{{ $getBlockByDateRange->id }}"
                                                           data-emptytext="Trống"
                                                           data-url="{{ route("admin_holiday.update") }}"
                                                           data-title="Ngày bắt đầu"> {{ $getBlockByDateRange->start_date != '' ? date('d/m/Y', strtotime($getBlockByDateRange->start_date)) : '' }}</a>
                                </td>
                                <td class="product_qty"><a style="font-weight: bold" href="#"
                                                           class="edit-item-detail date_time"
                                                           data-value="{{ $getBlockByDateRange->end_date }}" data-name="end_date"
                                                           data-type="date"
                                                           data-emptytext="Trống"
                                                           data-pk="{{ $getBlockByDateRange->id }}"
                                                           data-url="{{ route("admin_holiday.update") }}"
                                                           data-title="Ngày kết thúc"> {{  $getBlockByDateRange->end_date != '' ? date('d/m/Y', strtotime($getBlockByDateRange->end_date)) : ''  }}</a>
                                </td>
                                <td style="border: none;text-align: center" >
                                    <input class="order_check" type="checkbox" {{ $getBlockByDateRange->status == 1 ? 'checked' : '' }}  data-toggle="toggle" data-id="{{ $getBlockByDateRange->id }}" data-style="ios">
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>


                @php
                    $htmlSelectProduct =
                            '<tr class="select-product">
                                <td><input type="text" name="name[]" class="add_name form-control" value=""></td>
                                <td style="text-align:center">
                                    <input type="text" style="width: 130px;"
                                           autocomplete="off"
                                           name="start_date[]"
                                           value=""
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm date_time add_start_date"
                                           data-date-format="dd/mm/yyyy"
                                           placeholder="Chọn ngày"/>
                                </td>
                                <td style="text-align:center">
                                    <input type="text" style="width: 130px;"
                                           autocomplete="off"
                                           name="end_date[]"
                                           value=""
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm date_time add_end_date"
                                           data-date-format="dd/mm/yyyy"
                                           placeholder="Chọn ngày"/>
                                </td>
                                <td></td>
                                <td style="text-align:center"><button onClick="$(this).parent().parent().remove(); checkRemoveDOM();" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button></td>
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
@endsection


@push('styles')
    <style type="text/css">
        .td-title {
            width: 35%;
            font-weight: bold;
        }

        .th-title {
            font-weight: bold;
            max-width: 15% !important;
        }

        .product_qty {
            width: 120px;
            text-align: right;
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
        .overflow_prevent {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .date_time {
            width: 130px;
        }
        .bootstrap-switch {
            width: 100%;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: #2196F3;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
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
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20rem; }
        .toggle.ios .toggle-handle { border-radius: 20rem; }
    </style>
    <!-- Ediable -->
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    {{-- Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>

    <!-- /Handle navigation with key arrow -->

    <script type="text/javascript">
        $( ".date_time" ).datepicker({
            dateFormat: "dd/mm/yy"
        });
        $('#add-item-button').click(function () {
            var html = '{!! $htmlSelectProduct !!}';
            $('#add-item').before(html);
            $('#add-item-button-save').show();
            $('.add_id').focus();
            $( ".date_time" ).datepicker({
                dateFormat: "dd/mm/yy"
            });
        });

        $('#add-item-button-save').click(function (event) {
            let name = [];
            let start_date = [];
            let end_date = [];

            $('.add_name').each(function () {
                name.push($(this).val());
            });
            $('.add_start_date').each(function () {
                start_date.push($(this).val());
            });
            $('.add_end_date').each(function () {
                end_date.push($(this).val());
            });

            if (name.includes('')) {
                alertJs('error', 'Tên ngày lễ không được trống!');
            }  else if (start_date.includes('')) {
                alertJs('error', 'Ngày bắt đầu không được trống!');
            } else if (end_date.includes('')) {
                alertJs('error', 'Ngày kết thúc không được trống!');
            } else {
                $('#add-item-button').prop('disabled', true);
                $.ajax({
                    url: '{{ route("admin_holiday.create") }}',
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

            $('.edit-item-detail').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        location.reload()
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }


        {{-- sweetalert2 --}}
        function deleteItem(id) {
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
                            url: '{{ route("admin_holiday.delete") }}',
                            data: {
                                _method : 'delete',
                                'pId': id,
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
                ) { }
            })
        }

        function changeStatus(id) {
            $.ajax({
                method: 'POST',
                url: '{{ route("admin_holiday.change_status") }}',
                data: {
                    'id': id,
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
        }

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
        $(".order_check").change(function(){
            let id = $(this).data('id') ;
            $.ajax({
                method: 'POST',
                url: '{{ route("admin_holiday.change_status") }}',
                data: {
                    'id': id,
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    if (response.error == 0) {
                        // location.reload();
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }

                }
            });
        });
    </script>

@endpush
