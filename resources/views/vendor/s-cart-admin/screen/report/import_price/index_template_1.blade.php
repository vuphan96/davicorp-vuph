@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools" style="width: 100%">
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
                                <span class="btn btn-flat btn-danger grid-trash"
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
                                <div class="input-group float-right ml-1" style="width: 350px;">
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
                            <tr style="width: 100%">
                                @if (!empty($removeList))
                                    <th></th>
                                @endif
                                @foreach ($listTh as $key => $th)
                                    <th style="{!! $cssTh[$key] ?? ''!!}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataTr as $keyRow => $tr)
                                <tr>
                                    @if (!empty($removeList))
                                        <td width="18px" align="center">
                                            <input class="checkbox grid-row-checkbox" type="checkbox"
                                                   data-id="{{ $keyRow }}">
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
            </div>
            <!-- /.card -->
        </div>
    </div>
    @include($templatePathAdmin.'screen.report.import_price.modal_import_order')
@endsection


@push('styles')
    <style>
        .multiselect {
            width: 200px;
        }
        .show {
            display: block;
        }
        .multiselect.dropdown-toggle.custom-select.text-center {
            width: 100% !important;
        }
        table.list_table tr td:last-child {
            min-width: 180px;
            max-width: 180px;
            text-align: center;
            padding-left: 12px !important;
        }

        table.list_table tr td:first-child {
            text-align: center;
            padding-left: 12px !important;
        }

        @media (min-width: 768px) {
            .box-body td, .box-body th {
                max-width: 888px;
                word-break: break-word;
            }
        }

        @media (max-width: 1380px) {
            .col-3-custom {
                flex: 0 0 29% !important;
                max-width: 28% !important;
            }
            .multiselect {
                width: 160px;
            }
        }

        @media screen and (max-width: 810px) {
            table.list_table tr td:last-child {
                min-width: 180px;
            }

            .table td, .table th {
                padding: .75rem;
                vertical-align: top;
                border-top: 1px solid #dee2e6;
                min-width: 128px;
            }

            .table td:first-child {
                text-align: center;
            }
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
@endpush

@push('scripts')
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script type="text/javascript">


        $(document).on('submit', '#button_search', function (event) {
            // $.pjax.submit(event, '#pjax-container')
            $('#loading').show();
        })

        $('a.page-link').click(function () {
            $('#loading').show();
        })

        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
            $('#key_export').multiselect({
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                selectAllText: 'Chọn tất cả!',
                maxHeight: 500,
                dropUp: false,
                includeResetOption: true,
                resetText: "Đặt lại"
            });
        });
    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
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
            let title_report = $('#title_report').val();
            console.log(title_report);
            document.title = "BaoCaoHangNhap" + title_report + "-" + startDate + " - " + endDate;
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
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        $('.btn-search').on('click', function (e) {
            $('#check_filter').val('1');
        })

        function savePdf() {
            let rowCount = $('#data_count').val()
            if(rowCount > 9999) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let keyword = $("#keyword").val() ?? '';
            let search_supplier = $("#search_supplier").val() ?? '';
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $("#key_export").val();
            let href = '{{ $url_export_pdf }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&key_export=' + key_export + '&search_supplier=' + search_supplier ;
            $("#loading").show();
            printPage(href);
        }

        $('#button_export_filter').on('click', function () {
            let rowCount = $('#data_count').val()
            if(rowCount > 5000) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let keyword = $("#keyword").val() ?? '';
            let search_supplier = $("#search_supplier").val() ?? '';
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $("#key_export").val();
            let href = '{{ $url_export_excel }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&key_export=' + key_export + '&search_supplier=' + search_supplier;
            window.location.href = href;
        });

        function showModalImportOrder(type_import) {
            $('#type_import').val(type_import);
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            if (from_to != end_to) {
                alertMsg("error", "Lỗi tạo đơn nhập", "Vui lòng chọn khoảng ngày lọc là 1 ngày!")
                return;
            }
            $("#modalImportOrder").attr("class", "modal fade show");
        }

        $('#close_modal_import_order').on('click', function () {
            $("#modalImportOrder").removeClass("show");
            $('#btn_submit_create_order_import').prop('disabled', false)
        });

        $('#btn_submit_create_order_import').on('click', function () {
            $("#loading").show();
            let keyword = $("#keyword").val() ?? '';
            let search_supplier = $("#search_supplier").val() ?? '';
            let type_import = $("#type_import").val() ?? '';
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $("#key_export").val();
            let import_delivery_date = $('#import_delivery_date').val()
            let warehouse_id = $('#warehouse_id').val()
            if (import_delivery_date == '' || warehouse_id =='') {
                alertMsg("error", "Lỗi tạo đơn nhập", "Vui lòng nhập ngày giao hàng và chọn kho!")
                return;
            }
            $(this).prop('disabled', true)
            $.ajax({
                method: 'POST',
                url: '{{ route("order_import.create_import_by_report") }}',
                data: {
                    'keyword': keyword,
                    'search_supplier': search_supplier,
                    'from_to': from_to,
                    'type_import': type_import,
                    'end_to': end_to,
                    'key_export': key_export,
                    'import_delivery_date': import_delivery_date,
                    'warehouse_id': warehouse_id,
                    _token: '{{ csrf_token() }}',
                },
                success: function (response) {
                    $('#loading').hide()
                    $('#btn_submit_create_order_import').prop('disabled', false)
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $("#modalImportOrder").removeClass("show");
                    } else {
                        alertJs('error', response.msg);
                    }

                }
            });
        });

    </script>
@endpush
