@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border">
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
                        @if (!empty($menuRight) && count($menuRight))
                            @foreach ($menuRight as $item)
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
                        @if (!empty($removeList))
                            <div class="menu-left">
                                <button type="button" class="btn btn-default grid-select-all"><i
                                            class="far fa-square"></i></button>
                            </div>
                            <div class="menu-left">
                                <span class="btn btn-flat btn-danger grid-trash" data-perm="davicook_menu_card:delete"
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
                                <div class="input-group float-right ml-1" style="width: 259px;">
                                    <div class="btn-group">
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
                                @if (!empty($removeList))
                                    <th style="width: 5%"></th>
                                @endif
                                @foreach ($listTh as $key => $th)
                                    <th style=" {{ $cssTh[$key] ?? '' }}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataTr as $keyRow => $tr)
                                <tr>
                                    @if (!empty($removeList))
                                        <td style="padding-left: 12px; text-align: center">
                                            <input class="checkbox grid-row-checkbox" type="checkbox"
                                                   data-id="{{ $keyRow }}" data-status="{{ $tr['status'] }}">
                                        </td>
                                    @endif
                                    @foreach ($tr as $key => $trtd)
                                        <td style="{!! $cssTd[$key] ?? ''!!}">{!! $trtd !!}</td>
                                    @endforeach
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

    @if($is_orderlist ?? 0)
        <!-- Modal print -->
        <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content" method="get"
                      action="{{ sc_route_admin('admin.davicook_order.print_multiple') }}" id="printForm">
                    @csrf
                    <input type="hidden" name="ids" id="print_ids" value="{{ $order->id ?? '' }}">
                    <input type="hidden" name="filter" id="print_filter" value="">
                    <input type="hidden" name="customer_ids" id="customer_ids" value="">
                    <input type="hidden" name="bill_dates" id="bill_dates" value="">
                    <input type="hidden" name="delivery_dates" id="delivery_dates" value="">
                    <input type="hidden" name="number_status" id="number_status" value="2">
                    <input type="hidden" name="type_export" id="type_export" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i
                                    class="fas fa-print"></i>&nbsp;{{sc_language_render('order.print.title')}}</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-2">Thông tin in</label>
                            <div class="col-10">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="type_print_status" id="info_0" type="radio" class="custom-control-input"
                                           value="1" checked>
                                    <label for="info_0" class="custom-control-label">Suất ăn</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="type_print_status" id="info_1" type="radio" class="custom-control-input"
                                           value="2">
                                    <label for="info_1" class="custom-control-label">Hàng tươi sống</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="type_print_status" id="info_2" type="radio" class="custom-control-input"
                                           value="3">
                                    <label for="info_2" class="custom-control-label">Hàng khô</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                                    class="fa fa-undo"></i> {{sc_language_render('action.discard')}}</button>
                        <button type="button" id="btnConfirmPrint" class="btn btn-primary" onclick="sendDataPrintOrder(1)"><i
                                    class="fa fa-print"></i> In PDF</button>
                        <button type="button" id="btnConfirmPrintExcel" onclick="sendDataPrintOrder(2)" class="btn btn-success"><i
                                    class="fa fa-file-export"></i>Xuất Excel</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    {!! $css ?? '' !!}
    <style>
        @media (min-width: 768px) {
            .box-body td, .box-body th {
                max-width: 888px;
                word-break: break-word;
            }
        }

        @media screen and (max-width: 810px) {
            table.list_table tr td:last-child {
                min-width: 150px;
                width: 1%;
            }
            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
                min-width: 128px;
            }

        }
        table.list_table tr td:last-child, table.list_table th:last-child {
            min-width: 150px;
            max-width: 150px;
            width: 1%;
            text-align: center;
            padding-left: 12px !important;
        }

        table.list_table tr td:first-child {
            padding-left: 12px !important;
        }
        th {
            white-space: nowrap;
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
            min-width: 180px;
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
        .btn-create-order {
            width: 100%;
            height: 35px;
            text-align: left;
            padding-left: 16px !important;
            padding-top: 5px !important;
        }
        .btn-create-order:hover {
            background-color: #Dfeaee !important;
            border-radius: 5px 5px 5px 5px;
            color: #2596be !important;
        }
        #create-order-dropdown {
            border-radius: 12px 12px 12px 12px;
            background-color: #fff;
            margin-top: 1px;
            z-index: 4;
        }
        .dropdown:hover .dropdown-content {
            display: block !important;
        }

        .status-fast {
            color: #3c8dbc;
            font-size: 14px;
        }
    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script>
        let originalTitle = document.title;
        function closePrint() {
            document.title = originalTitle;
            document.body.removeChild(this.__container__);
        }

        function setPrint() {
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = closePrint;
            this.contentWindow.onafterprint = closePrint;
            this.contentWindow.focus(); // Required for IE
            $("#loading").hide();
            let now = new Date();
            let startDate = $('#from_to').val();
            let endDate = $('#end_to').val();
            document.title = "PhieuMonAn-"  + startDate + " - " + endDate;
            this.contentWindow.print();
        }

        function printPage(sURL) {
            const hideFrame = document.createElement("iframe");
            hideFrame.onload = setPrint;
            hideFrame.style.position = "fixed";
            hideFrame.style.right = "0";
            hideFrame.style.bottom = "0";
            hideFrame.style.width = "0";
            hideFrame.style.height = "0";
            hideFrame.style.border = "0";
            hideFrame.src = sURL;
            document.body.appendChild(hideFrame);
        }
        $(".date_time").datepicker({ dateFormat: "dd/mm/yy" });
        $('.grid-refresh').click(function () {
            window.location.href = "{{ route('admin.davicook_menu_card.index')}}";
        });

        $(document).on('submit', '#button_search', function (event) {
            // $.pjax.submit(event, '#pjax-container')
            $('#loading').show()
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })

        $('a.page-link').on('click', function () {
            $('#loading').show()
        })

        $(document).on('pjax:complete', function () {
            $('#loading').hide();
            $(".box-body input[type='checkbox']").iCheck("uncheck");
            $(".far", this).removeClass("fa-check-square").addClass('fa-square');
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

        $('#select_limit_paginate').on('change', function () {
            let limit = $(this).val();
            $('#limit_paginate').val(limit);
            $('#button_search').submit()
        })

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteDavicookMenuCard(ids);
        });

        /**
         * Delete davicook order
         * @param ids
         */
        function deleteDavicookMenuCard(ids) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn để xoá', 'Vui lòng chọn it nhât 1 phiếu trước khi xoá');
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
                            url: '{{ sc_route_admin('admin.davicook_menu_card.delete') ?? '' }}',
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

        /**
         * Sync menu card to davicook order.
         **/
        $('#btnSync').on('click', function () {
            let ids = selectedRows().join();

            if(selectedRows().length > 10){
                return alertMsg("error", "Lỗi tạo đơn", "Dữ liệu quá tải. Vui lòng giảm bớt số lượng phiếu được chọn!");
            }

            if(!ids){
                alertMsg("error", "Lỗi tạo đơn", "Vui lòng chọn ít nhất một phiếu để tạo đơn!");
                return;
            }
            
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Bạn đồng ý xác nhận tạo đơn hàng?',
                text: "Lưu ý: Hệ thống sẽ tạo các đơn mới và xóa những đơn hàng được đồng bộ trước đó từ phiếu!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: '{{ $method ?? 'post' }}',
                            url: '{{ sc_route_admin('admin.davicook_menu_card.create_order_davicook') ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertJs('error', data.msg);
                                    $(".box-body input[type='checkbox']").iCheck("uncheck");
                                    // location.reload();
                                } else {
                                    alertJs('success', data.msg);
                                    $(".box-body input[type='checkbox']").iCheck("uncheck");
                                    location.reload();
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
            })
        });
        /**
         * Copy phiếu.
         **/
        function cloneMenuCard(id) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Bạn có muốn nhân bản phiếu này?',
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
                            method: '{{ $method ?? 'post' }}',
                            url: '{{ sc_route_admin('admin.davicook_menu_card.clone') }}',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg, '{{ sc_language_render('action.warning') }}');
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
                    alertMsg('success', '{{ sc_language_render('product.admin.clone_success') }}', '');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) { }
            })
        }

        /**
         * Xuất excel phiếu.
         */
        $('#btn_export_excel').on('click', function () {
            let ids = selectedRows().join();
            if (selectedRows().length < 1) {
                return alertMsg('error', 'Lỗi xuất Excel', 'Vui lòng chọn ít nhất 1 phiếu để xuất!');

            }
            let href = '{{ sc_route_admin('admin.davicook_menu_card.export_excel') ?? ''}}?ids=' + ids;
            window.location.href = href;
        });

        /**
         * Print Pdf phiếu.
         */
        $('#btn_preview_pdf').on('click', function () {
            let ids = selectedRows().join();
            if (selectedRows().length < 1) {
                return alertMsg('error', 'Lỗi xuất Pdf', 'Vui lòng chọn ít nhất 1 phiếu để xuất!');
            }
            let href = '{{ sc_route_admin('admin.davicook_menu_card.preview_pdf') ?? ''}}?ids=' + ids;
            printPage(href);
        });

        /**
         * Send ajax gộp hóa đơn.
         * @param ids
         * @param title
         */
        function combineMenuCard() {
            let ids = selectedRows();
            if (ids.length < 2) {
                alertMsg('error', 'Lỗi gộp phiếu', 'Chọn ít nhât 2 phiếu để gộp!');
                return;
            }
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Xác nhận gộp phiếu ?',
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
                            url: '{{ sc_route_admin('admin.davicook_menu_card.combine') ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg, '{{ sc_language_render('action.warning') }}');
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
                    alertMsg('success', 'Gộp phiếu thành công!', '');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                }
            })
        }

    </script>

@endpush
