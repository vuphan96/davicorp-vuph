@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border ">
                    <div class="card-tools" style="float: none">
                        @if (!empty($topMenuRight) && count($topMenuRight))
                            @foreach ($topMenuRight as $item)
                                <div class="menu-right">
                                    @php
                                        $arrCheck = explode('view::', $item);
                                    @endphp
                                    @if (count($arrCheck) == 2)
                                        @if (view()->exists($arrCheck[1]))
                                            @include($arrCheck[1])
                                        @endif
                                    @else
                                        {!! trim($item) !!}
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <div class="float-left">
                        @if (!empty($topMenuLeft) && count($topMenuLeft))
                            @foreach ($topMenuLeft as $item)
                                <div class="menu-left">
                                    @php
                                        $arrCheck = explode('view::', $item);
                                    @endphp
                                    @if (count($arrCheck) == 2)
                                        @if (view()->exists($arrCheck[1]))
                                            @include($arrCheck[1])
                                        @endif
                                    @else
                                        {!! trim($item) !!}
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <!-- /.box-tools -->
                </div>

                <div class="card-header with-border">
                    <div class="card-tools">
                        <a data-perm="einvoice:send" href="#" class="btn btn-flat btn btn-primary" id="btnSendRobot"><i class="fab fa-telegram-plane"></i> Gửi robot</a>
                        <a data-perm="einvoice:cancel" href="#" class="btn btn-flat btn btn-danger" id="btnCancelSync"><i class="fas fa-ban"></i> Hủy đồng bộ</a>
                        <a data-perm="einvoice:merge" href="#" class="btn btn-flat btn btn-info" id="btnCombine"><i class="fa fa-layer-group"></i> {{ sc_language_render("admin.order.combine") }}</a>
                        <a data-perm="einvoice:import" href="{{ route("admin.einvoice.import") }}" class="btn  btn-success  btn-flat" title="New" id="button_import">
                            <i class="fa fa-file-import" title="Nhập Excel"></i> Nhập Excel</a>
                        <div data-perm="einvoice:export" class="dropdown">
                            <button class="dropbtn btn btn-flat btn btn-warning text-white"><i class="fa fa-print"></i> Xuất bảng kê <i class="fas fa-caret-down"></i></button>
                            <div id="myDropdown" class="dropdown-content">
                                <div class="container export-container">
                                    <div class="panel-group" id="accordionMenu" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingOne">
                                                <h4 class="panel-title">
                                                    <a role="button" class="btn btn-flat btn-export-order" id="export_sales_invoice_list_virtual" style="margin-top: 10px;">
                                                        Mẫu kê hóa đơn bên ảo
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingTwo">
                                                <h4 class="panel-title">
                                                    <a href="javascript:void(0)" class="btn btn-flat btn-export-order" data-toggle="modal" id="js_determine_finished_volume">
                                                        Xác định khối lượng hoàn thành
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingTwo">
                                                <h4 class="panel-title">
                                                    <a class="btn btn-flat btn-export-order" data-toggle="modal" id="btnShowFormAcceptanceReport">
                                                        Mẫu biên bản nghiệm thu
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingThree">
                                                <h4 class="panel-title">
                                                    <a class="collapsed btn btn-flat btn-export-order" role="button" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        Mẫu đề nghị thanh toán &nbsp;  <i class="fas fa-caret-down"></i>
                                                    </a>
                                                </h4>
                                            </div>
                                            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                                                <div class="panel-body">
                                                    <ul class="nav">
                                                        <li><a  class="btn btn-flat btn-show-form-payment btn-export-order-children" data-toggle="modal" id="payment_by_cash" data-type_payment="1"><i class="far fa-credit-card" ></i> Thanh toán tiền mặt</a></li>
                                                        <li><a  class="btn btn-flat btn-show-form-payment btn-export-order-children" data-toggle="modal" id="payment_by_transfer" data-type_payment="2"><i class="far fa-credit-card" ></i> Thanh toán chuyển khoản</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="headingFour">
                                                <h4 class="panel-title">
                                                    <a class="collapsed btn btn-flat btn-export-order" href="javascript:void(0)" data-toggle="modal" id="js_intro_davicorp_form">
                                                        Mẫu giấy giới thiệu
                                                    </a>
                                                </h4>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="float-left">
                        <div class="row">
                            @if (!empty($removeList))
                                <div class="menu-left">
                                    <button type="button" class="btn btn-default grid-select-all"><i
                                                class="far fa-square"></i></button>
                                </div>
                                <div class="menu-left">
                                    <span class="btn btn-flat btn-danger grid-trash" data-perm="einvoice:delete"
                                          title="{{ sc_language_render('action.delete') }}"><i class="fas fa-trash-alt"></i></span>
                                </div>
                            @endif

                            @if (!empty($buttonRefresh))
                                <div class="menu-left">
                                    <span class="btn btn-flat btn-primary grid-refresh"
                                          title="{{ sc_language_render('action.refresh') }}"><i class="fas fa-sync-alt"></i></span>
                                </div>
                            @endif

                            @if (!empty($buttonSort))
                                <div class="menu-left">
                                    <div class="input-group float-right ml-1" style="width: 260px;">
                                        <div class="btn-group" style="width: 210px">
                                            <select class="form-control rounded-0 float-right" id="order_sort">
                                                {!! $optionSort ?? '' !!}
                                            </select>
                                        </div>
                                        <div class="input-group-append">
                                            <button id="button_sort" type="submit" class="btn btn-primary"><i
                                                        class="fas fa-sort-amount-down-alt"></i></button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if (!empty($menuLeft) && count($menuLeft))
                                @foreach ($menuLeft as $item)
                                    <div class="menu-left">
                                        @php
                                            $arrCheck = explode('view::', $item);
                                        @endphp
                                        @if (count($arrCheck) == 2)
                                            @if (view()->exists($arrCheck[1]))
                                                @include($arrCheck[1])
                                            @endif
                                        @else
                                            {!! trim($item) !!}
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>


                <!-- /.card-header -->
                <div class="card-body p-0" id="pjax-container">
                    @php
                        $urlSort = $urlSort ?? '';
                    @endphp
                    <div id="url-sort" data-urlsort="{!! strpos($urlSort, "?")?$urlSort."&":$urlSort."?" !!}"
                         style="display: none;"></div>
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 10px"></th>
                                <th style="min-width: 100px">ID</th>
                                <th style="min-width: 110px">Mã đơn hàng</th>
                                <th style="min-width: 110px">Số hóa đơn</th>
                                <th style="text-align: left; min-width: 180px">Khách hàng</th>
                                <th style="min-width: 100px; max-width: 100px">Loại khách hàng</th>
                                <th style="min-width: 110px">Tổng tiền</th>
                                <th style="min-width: 120px;word-break: break-word !important;">Ngày trên hóa đơn</th>
                                <th style="min-width: 90px">HT đồng bộ</th>
                                <th style="min-width: 110px">Trạng thái đồng bộ</th>
                                <th style="min-width: 120px">Thời gian phát hành</th>
                                <th style="width: 115px !important;">Thao tác</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataInvoices as $keyRow => $tr)
                                <tr>
                                    <td style="padding-left: 12px; text-align: center">
                                        <input class="checkbox grid-row-checkbox" type="checkbox"
                                               data-id="{{ $tr->id }}" data-customer_code="{{ $tr->customer_code }}" data-status="{{ $tr->process_status }}" data-sign_status="{{$tr->sign_status}}">
                                    </td>
                                    <td> {{$tr->id_name}}</td>
                                    <td> {{$tr->order_id}}</td>
                                    <td>{{ $tr->einv_id }}</td>
                                    <td style="text-align: left; !important">{{ $tr->customer_name }}</td>
                                    <td>{{ $customer_kind[$tr->customer_kind] ?? '' }}</td>
                                    <td>{{ number_format($tr->total_amount) }}</td>
                                    <td>{{ date('d-m-Y H:i', strtotime($tr->invoice_date)) }}</td>
                                    <td>
                                        {{ $tr->sync_system }}
                                    </td>
                                    <td class = "invoice-info">
                                        @if($tr->process_status == 0)
                                            <span style="display:inline-block; width:80px" class="badge-secondary">Chưa làm</span>
                                        @elseif($tr->process_status == 1)
                                            <span style="display:inline-block; width:80px" class="badge-warning">Đã gửi</span>
                                        @elseif($tr->process_status == 2)
                                            <span style="display:inline-block; width:80px" class="badge-primary">Đang làm</span>
                                        @elseif($tr->process_status == 3)
                                            <span style="display:inline-block; width:80px" class="badge-danger">Thất bại</span>
                                        @elseif($tr->process_status == 4)
                                            @if($tr->sign_status == 4)
                                                <span style="display:inline-block; width:94px" class="badge-success">Đã phát hành</span>
                                            @else
                                                <span style="display:inline-block; width:80px" class="badge-info">Đã tạo</span>
                                            @endif

                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($tr->plan_sign_date))
                                            <span>{{ date('d-m-Y H:i', strtotime($tr->plan_sign_date)) }}</span>
                                        @endif

                                    </td>
                                    <td>
                                        <a  data-perm="einvoice:edit" href="{{ sc_route_admin('admin.einvoice.detail', ['id' => $tr->id ? $tr->id : 'not-found-id']) }}"><span title="{{ sc_language_render('action.edit') }}" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>
                                        {{--                                        <button onclick="printModal({{$tr->id}});"  title="' . sc_language_render('order.print.title') . '" class="btn btn-flat btn-sm btn-warning text-white"><i class="fas fa-print"></i></button>--}}
                                        <span  data-perm="einvoice:delete" onclick="deleteInvoices({{$tr->id}}, {{$tr->process_status}});"  title="{{ sc_language_render('action.delete') }}" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="block-pagination clearfix m-10">
                        <div class="ml-3 float-left">
                            {!! $resultItems??'' !!}
                        </div>
                        <div class="pagination pagination-sm mr-3 float-right">
                            {!! $pagination??'' !!}
                        </div>
                    </div>

                </div>
                <!-- /.card-body -->

                <div class="card-footer clearfix">
                    @if (!empty($blockBottom) && count($blockBottom))
                        @foreach ($blockBottom as $item)
                            <div class="clearfix">
                                @php
                                    $arrCheck = explode('view::', $item);
                                @endphp
                                @if (count($arrCheck) == 2)
                                    @if (view()->exists($arrCheck[1]))
                                        @include($arrCheck[1])
                                    @endif
                                @else
                                    {!! trim($item) !!}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <!-- /.card -->
        </div>
    </div>

    {{-- Form gửi robot --}}
    @include($templatePathAdmin.'screen.e_invoice.includes.form_send_robot')

    {{-- Form cáo cáo nghiệm thu--}}
    @include($templatePathAdmin.'screen.e_invoice.includes.form_acceptance_report')

    {{-- Form cáo cáo thanh toán--}}
    @include($templatePathAdmin.'screen.e_invoice.includes.form_payment_offer_by_cash_report')
    @include($templatePathAdmin.'screen.e_invoice.includes.form_payment_offer_by_transfer_report')

    <!-- Form submit export sales invoice list virtual -->
    <form action="{{ sc_route_admin('admin.einvoice.export_sales_invoice_list_virtual') }}" method="post" id="form_export_sales_invoice_list_virtual">
        @csrf
        <input type="hidden" name="ids" class="order_ids" value="">
        <input type="hidden" name="from_to_time" id="from_to_time" value="{{ request('from_to') }}">
        <input type="hidden" name="end_to_time" id="end_to_time" value="{{ request('end_to') }}">
    </form>

    <!-- Form submit preview print pdf sales invoice list virtual -->
    <form action="{{ sc_route_admin('admin.einvoice.print_sales_invoice_list_virtual') }}" target="_blank" method="post" id="form_print_sales_invoice_list_virtual">
        @csrf
        <input type="hidden" name="ids" class="order_ids" value="">
    </form>

    {{-- Form mẫu xác định khối lượng hoàn thành --}}
    @include($templatePathAdmin.'screen.e_invoice.includes.form_determine_volume_report')

    {{-- Form mẫu giới thiệu --}}
    @include($templatePathAdmin.'screen.e_invoice.includes.form_intro_report')

@endsection


@push('styles')
    {!! $css ?? '' !!}
    <style>
        table.list_table tr td:last-child, table.list_table th:last-child {
            min-width: 115px;
            max-width: 115px;
            width: 1%;
            text-align: center;
            padding-left: 12px !important;
        }

        .input-payment-price-cash {
            width: 90% !important;
        }

        table.list_table tr td:first-child {
            padding-left: 12px !important;
        }

        @media (min-width: 768px) {
            .box-body td, .box-body th {
                max-width: 888px;
                word-break: break-word;
            }
        }

        @media screen and (max-width: 810px) {
            table.list_table tr td:last-child {
                min-width: 180px;
                width: 1%;
            }

            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
                min-width: 128px;
            }

        }
        th {
            /*white-space: nowrap;*/
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f1f1f1;
            min-width: 260px;
            overflow: auto;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 0px 0 10px 3px;
            text-decoration: none;
        }

        .dropdown a:hover {background-color: #ddd;}

        .show {display: block;}
        /*.modal-content {*/
        /*    padding: 15px 40px 15px 20px;*/
        /*    box-sizing: border-box;*/
        /*}*/
        .modal-title-header {
            /*background-color: yellow;*/
            text-align: right;
            padding-right: 10px;
        }
        .modal-title {
            text-align: center;
        }
        .modal-title h5 {
            font-weight: bold;
        }
        .modal-content-body {
            width: 100%;
        }
        .price-payment-order {
            font-weight: bold;
            width: 415px;
            display: inline-block;
            text-align: right
        }
        .table tr td {
            text-align: center;
        }
        .table tr th {
            text-align: center;
        }
        .nav li a {
            font-size: 16px;
            padding-left: 20px;

        }
        .btn-export-order {
            width: 100%;
            text-align: left;
            padding-left: 15px !important;
            padding-top: 5px !important;
        }
        .btn-export-order-children{
            text-align: left;
            padding-left: 30px !important;
            padding-top: 5px !important;
            padding-bottom: 10px !important;
            width: 245px !important;
        }
        .btn-export-order-children:hover {
            background-color: #Dfeaee !important;
            border-radius: 5px 5px 5px 5px;
            color: #2596be !important;
        }
        .btn-export-order:hover {
            background-color: #Dfeaee !important;
            border-radius: 5px 5px 5px 5px;
            color: #2596be !important;
        }
        #myDropdown {
            border-radius: 12px 12px 12px 12px;
            background-color: #fff;
            margin-top: 1px;
            z-index: 4;
        }
        .dropdown:hover .dropdown-content {
            display: block !important;
        }
        .modal{
            background-color: #11141687 !important;
        }

        .status-sign {
            font-size: 14px;
        }

        .status-sign.wait {
            color: orange;
        }

        .status-sign.success {
            color: #3c8dbc;
        }

        .status-sign.error {
            color: red;
        }


    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script>
        /* When the user clicks on the button,
        toggle between hiding and showing the dropdown content */
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
        function myFunctionClickPrint() {
            document.getElementById("myDropdown").classList.toggle("show");
        }
    </script>
    <script>
        $(".date_time").datepicker({ dateFormat: "dd/mm/yy" });
        $('.grid-refresh').click(function () {
            $.pjax.reload({container: '#pjax-container'});
        });

        $(document).on('submit', '#button_search', function (event) {
            // $.pjax.submit(event, '#pjax-container')
            $('#loading').show()
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide();
            // $(".box-body input[type='checkbox']").iCheck("uncheck");
            // $(".far", this).removeClass("fa-check-square").addClass('fa-square');
        })

        $('a.page-link').on('click', function () {
            $('#loading').show()
        })

        // tag a
        $(function () {
            // $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 5000; // time in milliseconds
            }
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })

        });

        @if ($buttonSort)
        $('#button_sort').click(function (event) {
            var url = $('#url-sort').data('urlsort') + 'sort_order=' + $('#order_sort option:selected').val();
            $.pjax({url: url, container: '#pjax-container'})
        });
        @endif
    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
                {{-- sweetalert2 --}}
        var selectedRows = function () {
                var selected = [];
                $('.grid-row-checkbox:checked').each(function () {
                    selected.push($(this).data('id'));
                });

                return selected;
            }
        let selectCustomerRows = function () {
            let arrCustomer = [];
            $('.grid-row-checkbox:checked').each(function () {
                arrCustomer.push($(this).data('customer_code'));
            });

            return arrCustomer;
        }

        let selectStatus = function () {
            let arrStatus = [];
            $('.grid-row-checkbox:checked').each(function () {
                arrStatus.push($(this).data('status'));
            });

            return arrStatus;
        }

        let selectSignStatus = function () {
            let arrStatus = [];
            $('.grid-row-checkbox:checked').each(function () {
                arrStatus.push($(this).data('sign_status'));
            });

            return arrStatus;
        }

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteInvoices(ids, '');
        });

        /**
         *Bắt sự kiên click Gộp hóa đơn.
         */
        $('#btnCombine').on('click', function () {
            var ids = selectedRows();
            let arrStatus = [];
            let arrCustomer = selectCustomerRows();

            if (ids.length < 2) {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Chọn it nhât 2 hóa đơn để gộp');
                return;
            }

            $('.grid-row-checkbox:checked').each(function () {
                arrStatus.push($(this).data('status'));
            });

            let customer_codes_unique = Array.from(new Set(arrCustomer));
            if(customer_codes_unique.length !== 1 ||
                (arrCustomer.length > 1 && customer_codes_unique.length === 1 && arrCustomer.includes("")))
            {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Chọn cùng khách hàng để gộp!');
                return;
            }

            if (arrStatus.includes(1) ) {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Có hóa đơn trạng thái đã gửi. Không thể gộp!');
                return;
            }

            if (arrStatus.includes(2) ) {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Có hóa đơn trạng thái đang làm. Không thể gộp!');
                return;
            }

            if (arrStatus.includes(3) ) {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Có hóa đơn trạng thái thất bại. Không thể gộp!');
                return;
            }

            if (arrStatus.includes(4) ) {
                alertMsg('error', 'Lỗi gộp hóa đơn', 'Có hóa đơn đã xuất. Không thể gộp!');
                return;
            }

            combineInvoices(ids.join());
        });

        /**
         * Send ajax gộp hóa đơn.
         * @param ids
         * @param title
         */
        function combineInvoices(ids) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Xác nhận gộp hóa đơn ?',
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
                            method: 'post',
                            url: '{{ sc_route_admin('admin.einvoice.merge') ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
                                }
                                alertMsg('success', data.msg);
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('einvoice.combined') }}', '');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                }
            })
        }

        /**
         * Delete davicook order
         * @param ids
         * @param status
         */
        function deleteInvoices(ids, status) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn để xoá', 'Vui lòng chọn it nhât 1 bản ghi trước khi xoá đối tượng');
                return;
            }
            let arrStatus = [];
            $('.grid-row-checkbox:checked').each(function () {
                arrStatus.push($(this).data('status'));
            });

            if (arrStatus.includes(4) || status == 4) {
                alertMsg('error', 'Lỗi xóa', 'Hóa đơn đã xuất, không thể xóa!');
                return;
            }

            if (arrStatus.includes(2) || status == 2) {
                alertMsg('error', 'Lỗi xóa', 'Hóa đơn đang xuất, không thể xóa!');
                return;
            }

            if (arrStatus.includes(1) || status == 1) {
                alertMsg('error', 'Lỗi xóa', 'Hóa đơn chờ xuất, không thể xóa. Vui lòng hủy đồng bộ trước khi thực hiện!');
                return;
            }

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
                            method: 'delete',
                            url: '{{ sc_route_admin('admin.einvoice.delete') ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
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
        }

    </script>


    <script type="text/javascript">

        // Paginate
        $('#select_limit_paginate').on('change', function () {
            let limit = $(this).val();
            $('#limit_paginate').val(limit);
            $('#button_search').submit()
        })

        /**
         * Export sales e-invoice list virtual order
         */
        $('#export_sales_invoice_list_virtual').on('click', function () {
            let ids = selectedRows().join();
            let customer_codes = [];
            let process_status = [];

            $('.grid-row-checkbox:checked').each(function () {
                customer_codes.push($(this).data('customer_code'));
                process_status.push($(this).data('status'));
            });

            if(!ids) {
                alertMsg('error', 'Vui lòng chọn ít nhất 1 hóa đơn', '{{ sc_language_render('action.warning') }}');
                return;
            }

            let customer_codes_unique = Array.from(new Set(customer_codes));
            if(customer_codes_unique.length !== 1 ||
                (customer_codes.length > 1 && customer_codes_unique.length === 1 && customer_codes.includes("")))
            {
                alertMsg('error', 'Các hóa đơn được chọn không cùng khách hàng!', 'Cảnh báo');
                return;
            }
            if (process_status.includes(0) || process_status.includes(1) || process_status.includes(2) || process_status.includes(3)) {
                alertMsg('error', 'Có hóa đơn chưa được đồng bộ thành công nên không thể xuất <br> bản kê!', 'Cảnh báo');
                return;
            }
            const form = $('#form_print_sales_invoice_list_virtual');
            const ids_input = $('.order_ids');
            ids_input.val(ids);
            form.submit();
            setTimeout(exportExcellVirtualReport, 250);
        });

        function exportExcellVirtualReport() {
            const form = $('#form_export_sales_invoice_list_virtual');
            form.submit();
        }

        $('#js_determine_finished_volume').on('click', function (e) {
            let ids = selectedRows().join();
            if(!ids) {
                alertMsg('error', 'Vui lòng chọn ít nhất 1 hóa đơn', '{{ sc_language_render('action.warning') }}');
                return;
            }
            $('#id_invoice').val(ids);
            $.ajax({
                url: '{{ sc_route_admin('admin.davicook.determine_finished_volume') }}',
                method: "POST",
                data: {
                    id: ids,
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
                        alertMsg('error', '{{ sc_language_render('action.warning.invoice.different.customer') }}', '{{ sc_language_render('action.warning') }}');
                        return;
                    } else {
                        $("#volume-modal").attr("class", "modal fade show");
                        if (data) {
                            let detailHtml = [];
                            let stt = 0;
                            let sumPrice = 0;
                            $('.auto_customer_name_determine').val(data.customer.name)
                            $('.auto_customer_department_determine').val(data.customer.department)
                            $('.auto_date').val(data.customer.date)
                            for (let datum of data.detail) {
                                console.log(datum)
                                stt++;
                                sumPrice = sumPrice + datum.total_price;
                                let html = '';
                                html += '<tr>\n' +
                                    '<td>' + stt + '</td>\n' +
                                    '<td>' + datum.product_name + '</td>\n' +
                                    '<td>' + datum.unit + '</td>\n' +
                                    '<td>' + datum.qty + '</td>\n' +
                                    '<td>' + formatNumber(datum.price) + '</td>\n' +
                                    '<td>' + formatNumber(datum.total_price) + '</td>\n' +
                                    '</tr>';
                                detailHtml.push(html);
                            }
                            for (let i = 0; i <= 3; i++) {
                                let html = '';
                                html += '<tr>\n' +
                                    '<td></td>\n' +
                                    '<td></td>\n' +
                                    '<td></td>\n' +
                                    '<td></td>\n' +
                                    '<td></td>\n' +
                                    '<td></td>\n' +
                                    '</tr>';
                                detailHtml.push(html);
                            }
                            detailHtml.push(
                                '<tr>\n' +
                                '<td colspan="1">Tiền hàng</td>\n' +
                                '<td colspan="4"></td>\n' +
                                '<td colspan="1">' + formatNumber(sumPrice) + '</td>\n' +
                                '</tr>' +
                                '<tr>\n' +
                                '<td colspan="1">Tổng cộng</td>\n' +
                                '<td colspan="4"></td>\n' +
                                '<td colspan="1">' + formatNumber(sumPrice) + '</td>\n' +
                                '</tr>' +
                                '<tr>\n' +
                                '<td colspan="1">Bằng chữ</td>\n' +
                                '<td colspan="4">' + DocTienBangChu(sumPrice) + '</td>\n' +
                                '<td colspan="1"></td>\n' +
                                '</tr>'
                            );
                            $('#table_invoice_detail').html(detailHtml);
                            $('.price-payment-order').html(formatNumber(sumPrice));
                        }
                    }
                }
            });
        });
        $('.close-modal').on('click', function () {
            $("#volume-modal").removeClass("show");
            $("#form_intro_davicorp").removeClass("show");
            $('.form-determine-volume').trigger("reset");
            $('.form-intro-davicorp').trigger("reset");
        });
        $(".form-determine-volume button").click(function(ev){
            ev.preventDefault()// cancel form submission
            if($(this).attr("value")=="export_excel"){
                let formSubmit = $('.form-determine-volume').serialize();
                let hrefExcel = '{{ sc_route_admin('admin.einvoice.determine.volume.excel') }}?' + formSubmit;
                window.location.href = hrefExcel;
            }
            if($(this).attr("value")=="export_pdf"){
                let formSubmit = $('.form-determine-volume').serialize();
                let hrefPdf = '{{ sc_route_admin('admin.einvoice.determine.volume.pdf') }}?' + formSubmit;
                window.open(hrefPdf, '_blank');
            }
        });

        /**
         * Xử lý và show form send robot.
         */
        $('#btnSendRobot').click(function () {
            let ids = selectedRows().join();
            let arrStatus = selectStatus();
            if(!ids){
                alertMsg("error", "Lỗi gửi Robot", "Vui lòng chọn ít nhất một hóa đơn để gửi!");
                return;
            }

            if (arrStatus.includes(4)) {
                alertMsg("error", "Lỗi gửi Robot", "Có hóa đơn trạng thái đã tạo!");
                return;
            }

            if (arrStatus.includes(2)) {
                Swal.mixin({
                    customClass: {
                        confirmButton: 'btn btn-success',
                        cancelButton: 'btn btn-danger'
                    },
                    buttonsStyling: true,
                }).fire({
                    title: 'Hóa đơn đang xuất. Xác nhận Gửi lại hóa đơn?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                    confirmButtonColor: "#DD6B55",
                    cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                    reverseButtons: true,
                    preConfirm: function () {
                        $('#sendRobotIds').val(ids);
                        $('#sendRobot').modal();
                    }
                })
            } else {
                const d = new Date();
                let hourSendRobot = d.getHours(); //mặc định là 15h30 chiều nha cưng
                let miSendRobot = 30;
                if (hourSendRobot < 15) {
                    hourSendRobot = 15;
                    miSendRobot = 30;
                } else {
                    ++hourSendRobot;
                    miSendRobot = '00';
                }


                $('#hour_start').val(hourSendRobot);
                $('#minute_start').val(miSendRobot);
                $('#sendRobotIds').val(ids);
                $('#sendRobot').modal();
            }
        })
        /**
         * Submit form send robot
         */
        $('#btnSubmitSendRobot').click(function () {
            let startDate = $('#date_start').val();
            let startHour = $('#hour_start').val();
            let startMinute = $('#minute_start').val();
            let startDateCheck = new Date(Number(startDate.split("/")[2]), Number(startDate.split("/")[1]) -1, Number(startDate.split("/")[0]), Number(startHour), Number(startMinute));

            let dateNow = Date.now();
            if (startDateCheck < dateNow) {
                alertMsg("error", "Lỗi gửi Robot", "Thời gian gửi Robot không hợp lệ!");
                return;
            } else {
                $('#formSendRobot').submit();
                alertMsg('success', 'Gửi robot thành công');
                // $.pjax.reload('#pjax-container');
            }
        })

        function formatDate(date) {
            var d = new Date(date),
                month = '' + (d.getMonth() + 1),
                day = '' + d.getDate(),
                year = d.getFullYear();

            if (month.length < 2)
                month = '0' + month;
            if (day.length < 2)
                day = '0' + day;

            return [year, month, day].join('-');
        }

        /**
         * Xử lý hủy đồng bộ hóa đơn.
         */
        $('#btnCancelSync').click(function () {
            let ids = selectedRows().join();
            let arrStatus = selectSignStatus();
            if(!ids){
                alertMsg("error", "Lỗi hủy!", "Vui lòng chọn ít nhất một hóa đơn để hủy!");
                return;
            }

            if (arrStatus.includes(1)) {
                alertMsg("error", "Lỗi hủy đồng bộ", "Hóa đơn đã phát hành không thể hủy!");
                return;
            }

            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Xác nhận Hủy?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,
                preConfirm: function () {
                    $.ajax({
                        method: 'post',
                        url: '{{ sc_route_admin('admin.einvoice.cancel_sync') ?? '' }}',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}',
                        },
                        success: function (data) {
                            if (data.error == 1) {
                                alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                $.pjax.reload('#pjax-container');
                                return;
                            } else {
                                alertMsg('success', data.msg);
                                $.pjax.reload('#pjax-container');
                                resolve(data);
                            }
                        }
                    });
                }
            })
        })

        /**
         *
         */
        $('#btnShowFormAcceptanceReport').on('click', function (e) {
            let ids = selectedRows().join();
            if(!ids) {
                alertMsg('error', 'Vui lòng chọn ít nhất 1 hóa đơn', '{{ sc_language_render('action.warning') }}');
                return;
            }

            let arrCustomer = selectCustomerRows();
            let customer_codes_unique = Array.from(new Set(arrCustomer));
            if(customer_codes_unique.length !== 1 ||
                (arrCustomer.length > 1 && customer_codes_unique.length === 1 && arrCustomer.includes("")))
            {
                alertMsg('error', 'Lỗi in hóa dơn', 'Vui lòng chọn hóa đơn cùng khách hàng!');
                return;
            }

            $('#id_invoice').val(ids);
            $.ajax({
                url: '{{ sc_route_admin('admin.einvoice.acceptance_report') }}',
                method: "POST",
                data: {
                    id: ids,
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
                        alertMsg('error', '{{ sc_language_render('action.warning.invoice.different.customer') }}', '{{ sc_language_render('action.warning') }}');
                        return;
                    } else {
                        $("#acceptance-modal").attr("class", "modal fade show");
                        if (data) {
                            let detailHtml = [];
                            let stt = 0;
                            let sumPrice = 0;
                            for (let datum of data.einvoice) {
                                stt++;
                                sumPrice = sumPrice + datum.total_price;
                                let html = '';
                                html += '<tr>\n' +
                                    '<td>' + stt + '</td>\n' +
                                    '<td>' + datum.product_name + '</td>\n' +
                                    '<td>' + datum.unit + '</td>\n' +
                                    '<td>' + datum.qty + '</td>\n' +
                                    '<td style="text-align: right; padding-right: 8px">' + formatNumber(datum.price) + '</td>\n' +
                                    '<td style="text-align: right; padding-right: 8px">' + formatNumber(datum.total_price) + '</td>\n' +
                                    '</tr>';
                                detailHtml.push(html);
                            }
                            $('.auto_customer_name_acceptance').val(data.customer.name);
                            $('.auto_customer_department_acceptance').val(data.department);
                            $('#customer_name').html(data.customer.name);
                            $('.auto_month').val(data.date_month)
                            $('.auto_year').val(data.date_year)
                            $('#customer_phone').html(data.customer.phone);
                            $('#customer_tax_code').html(data.customer.tax_code);
                            $('#customer_address').html(data.customer.address);

                            detailHtml.push(
                                '<tr>\n' +
                                '<td colspan="1" style="font-weight: bold">Tiền hàng</td>\n' +
                                '<td colspan="4"></td>\n' +
                                '<td colspan="1" style="text-align: right; padding-right: 8px">' + formatNumber(sumPrice) + '</td>\n' +
                                '</tr>' +
                                '<tr>\n' +
                                '<td colspan="1" style="font-weight: bold">Tổng cộng</td>\n' +
                                '<td colspan="4"></td>\n' +
                                '<td colspan="1" style=" text-align: right; padding-right: 8px">' + formatNumber(sumPrice) + '</td>\n' +
                                '</tr>' +
                                '<tr>\n' +
                                '<td colspan="1" style="font-weight: bold">Bằng chữ</td>\n' +
                                '<td colspan="4">' + DocTienBangChu(sumPrice) + ' đồng</td>\n' +
                                '<td colspan="1"></td>\n' +
                                '</tr>'
                            );

                            $('#table_invoice_detail_acceptance').html(detailHtml);
                            $('.price-payment-order').html(formatNumber(sumPrice));
                            $('.price-in-words').html(DocTienBangChu(sumPrice) + ' đồng)');
                        }
                    }
                }
            });
            $('#close_acceptance_modal').on('click', function () {
                $("#acceptance-modal").removeClass("show");
            });

        });

        /**
         * JS Xử lý show form mẫu đề nghị thanh toán
         */
        $('.btn-show-form-payment').on('click', function (e) {
            let ids = selectedRows().join();
            let type_payment = $(this).data('type_payment');
            if(!ids) {
                alertMsg('error', 'Vui lòng chọn ít nhất 1 hóa đơn', '{{ sc_language_render('action.warning') }}');
                return;
            }

            let arrCustomer = selectCustomerRows();
            let customer_codes_unique = Array.from(new Set(arrCustomer));
            if(customer_codes_unique.length !== 1 ||
                (arrCustomer.length > 1 && customer_codes_unique.length === 1 && arrCustomer.includes("")))
            {
                alertMsg('error', 'Lỗi in hóa dơn', 'Vui lòng chọn hóa đơn cùng khách hàng!');
                return;
            }

            $('#id_invoice').val(ids);
            $.ajax({
                url: '{{ sc_route_admin('admin.einvoice.payment_offer_report') }}',
                method: "POST",
                data: {
                    id: ids,
                    type_payment: type_payment,
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
                        alertMsg('error', '{{ sc_language_render('action.warning.invoice.different.customer') }}', '{{ sc_language_render('action.warning') }}');
                        return;
                    } else {
                        if (data) {
                            let department_customer = data.customer.department_id;
                            console.log(department_customer);
                            // department_customer = 1 => Cty Davicorp Hà Nội
                            // department_customer = 1 => Cty Davicorp Vietnam
                            // department_customer = 3 => khách hàng thuộc cửa hàng
                            // department_customer = 5 => khách hàng thuộc cửa hàng Vũ Trường Giang
                            console.log(data.department_name);
                            $('#location_payment').text(data.department_name);

                            if ( department_customer == 1 || department_customer == 2 ) {
                                $('#title-payment-offer-by-transfer').removeClass('d-none');
                                $('#title-payment-offer-by-cash').removeClass('d-none');
                                $('#sign_payment_cash_store').addClass('d-none');
                                $('#sign_payment_cash_company').removeClass('d-none');
                                $('#payment_transfer_company').removeClass('d-none');
                                $('#payment_transfer_store').addClass('d-none');
                                $('#sign_payment_by_transfer_store').addClass('d-none');
                                $('#sign_payment_by_transfer_company').removeClass('d-none');
                                $('#title-payment-offer-by-transfer-vtg').addClass('d-none');
                                $('#payment_transfer_store_vtg').addClass('d-none');
                            } else if (department_customer == 3) {
                                $('#sign_payment_by_transfer_store').removeClass('d-none');
                                $('#sign_payment_by_transfer_company').addClass('d-none');
                                $('#title-payment-offer-by-transfer').addClass('d-none');
                                $('#title-payment-offer-by-cash').addClass('d-none');
                                $('#payment_transfer_store').removeClass('d-none');
                                $('#payment_transfer_company').addClass('d-none');
                                $('#sign_payment_cash_store').removeClass('d-none');
                                $('#sign_payment_cash_company').addClass('d-none');
                                $('#title-payment-offer-by-transfer-vtg').addClass('d-none');
                                $('#payment_transfer_store_vtg').addClass('d-none');
                            } else {
                                $('#sign_payment_by_transfer_store').removeClass('d-none');
                                $('#sign_payment_by_transfer_company').addClass('d-none');
                                $('#title-payment-offer-by-transfer').addClass('d-none');
                                $('#title-payment-offer-by-cash').addClass('d-none');
                                $('#payment_transfer_store').addClass('d-none');
                                $('#payment_transfer_company').addClass('d-none');
                                $('#sign_payment_cash_store').removeClass('d-none');
                                $('#sign_payment_cash_company').addClass('d-none');
                                $('#title-payment-offer-by-transfer-vtg').removeClass('d-none');
                                $('#payment_transfer_store_vtg').removeClass('d-none');
                            }

                            // type_payment = 1 => thanh toán bằng tiền mặt
                            // type_payment = 2 => thanh toán bằng chuyển khoản
                            if (type_payment == 1) {
                                $("#thePaymentByCashModal").attr("class", "modal fade show");
                                $('#customer_payment_by_cash_name').html(data.customer_name);
                                $('#customer_payment_by_cash_address').html(data.address);
                                $('#payment_by_cash_total_amount').html(Intl.NumberFormat('en-US').format(data.number_total_amount));
                                $('#payment_by_cash_input_total_amount').val(data.number_total_amount);
                            } else {
                                $("#thePaymentByTransferModal").attr("class", "modal fade show");
                                $('#customer_payment_by_transfer_name').html(data.customer_name);
                                $('#customer_payment_by_transfer_address').html(data.address);
                                $('#payment_by_transfer_no_total_price').html(Intl.NumberFormat('en-US').format(data.number_total_amount))
                                $('#payment_by_transfer_total_amount').html(Intl.NumberFormat('en-US').format(data.number_total_amount))
                                $('#payment_by_transfer_text_total_price').html(data.text_total_amount)
                            }
                        }
                    }
                }
            });
            $('.close_report_payment_offer').on('click', function () {
                $("#thePaymentByCashModal").removeClass("show");
                $("#thePaymentByTransferModal").removeClass("show");
            });

        });

        /**
         * JS Xử lý mẫu giấy giới thiệu
         */
        $('#js_intro_davicorp_form').on('click', function (e) {
            let ids = selectedRows().join();
            if(!ids) {
                alertMsg('error', 'Vui lòng chọn ít nhất 1 hóa đơn', '{{ sc_language_render('action.warning') }}');
                return;
            }
            $('id_intro_davicorp').val(ids);
            $.ajax({
                url: '{{ sc_route_admin('admin.davicook.intro_davicorp_form') }}',
                method: "POST",
                data: {
                    id: ids,
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
                        if (data.messages) {
                            alertMsg('error', 'Lỗi chọn hóa đơn', data.messages);
                        } else {
                            alertMsg('error', 'Lỗi chọn hóa đơn', '{{ sc_language_render('action.warning') }}');
                        }
                        return;
                    } else {
                        $("#form_intro_davicorp").attr("class", "modal fade show");
                        if (data) {
                            $('#invoice_date').val(data.invoice_date);
                            $('#id_customer').val(data.customer_name);
                            $('#object_name').val(data.object_represent);
                            $('.customer-name').html(data.customer_name);
                            $('.object-represent').html(data.object_represent);
                            $('#object_header').html(data.object_header);
                            $('.object-header').val(data.object_header);
                        }
                    }
                }
            });
        });
        $(".form-intro-davicorp button").click(function(ev){
            ev.preventDefault()// cancel form submission
            if($(this).attr("value")=="export_excel_intro_davicorp"){
                let formSubmit = $('.form-intro-davicorp').serialize();
                let hrefExcel = '{{ sc_route_admin('admin.einvoice.intro_davicorp_form.excel') }}?' + formSubmit;
                window.location.href = hrefExcel
            }
            if($(this).attr("value")=="export_pdf_intro_davicorp"){
                let formSubmit = $('.form-intro-davicorp').serialize();
                let hrefPdf = '{{ sc_route_admin('admin.einvoice.intro_davicorp_form.pdf') }}?' + formSubmit;
                window.open(hrefPdf, '_blank');
            }
        });
    </script>


@endpush
