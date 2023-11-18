@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools">
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
                </div>

                <!-- /.card-header -->
                <div class="card-body p-0" id="pjax-container">
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr style="width: 100%">
                                @if (!empty($selectList))
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
                                    @if (!empty($selectList))
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
{{--    @foreach(session('dataItems') as $key => $value)--}}
{{--        <form action="{{ sc_route_admin('admin_report_print_stamp.preview_pdf') }}" method="get" target="_blank" id="submitFormPrint_{{ $key }}">--}}
{{--            <input type="hidden" name="key_print" value="{{ $key }}">--}}
{{--        </form>--}}
{{--    @endforeach--}}
    @endsection

@push('styles')
    <style>
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
        .text-white{
            background-color: orange !important;
            display: block;
            border: 1px orange solid;
            border-radius: 5px;
        }
    </style>
    {{-- Timepicker css--}}
    <link rel="stylesheet" type="text/css" href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
    <link rel="stylesheet" href="{{ sc_file('admin/LTE/plugins/jquery-ui/jquery-ui-timepicker-addon.css') }}">
@endpush

@push('scripts')
    {{-- Timepicker js--}}
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script src="{{ sc_file('admin/LTE/plugins/jquery-ui/jquery-ui-timepicker-addon.min.js') }}"></script>
    <script type="text/javascript">
        // Date picker
        $('.datepicker').datetimepicker({
            dateFormat: 'dd/mm/yy',
            timeFormat: 'HH:mm:ss',
            stepHour: 1,
            stepMinute: 1,
            stepSecond: 1,
            timeText: 'Thời gian',
            hourText: 'Giờ',
            minuteText: 'Phút',
            secondText: 'Giây',
            currentText: 'Giờ hiện tại',
            closeText: 'Chọn'
        });

        $(document).on('submit', '#button_search', function (event) {
            $('#loading').show()
        })

        $('a.page-link').on('pjax:send', function () {
            $('#loading').show()
        })

        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })
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
        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }

            $('.select-custom').multiselect({
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                selectAllText: 'Chọn tất cả!',
                maxHeight: 500,
                dropDown: true,
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
            document.title = "BaoCaoInTem-"  + startDate + " - " + endDate;
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
        var selectedRows = function () {
                var selected = [];
                $('.grid-row-checkbox:checked').each(function () {
                    selected.push($(this).data('id'));
                });

                return selected;
            }
        function print(id) {
            $('#print_ids').val(id);
            $('#printDialog').modal();
        }

        function printMultiple() {
            $('#print_ids').val(selectedRows().join());
            $('#printDialog').modal();
        }


        $('.btn-search').on('click', function (e) {
            $('#check_filter').val('1');
        })
        $('#button_export_filter').on('click', function () {
            let data_count = $('#data_count').val();
            if (data_count > 9999) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let ids = selectedRows().join();
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let order_date_from =  $("#order_date_from").val();
            let order_date_to = $("#order_date_to").val();
            let end_to = $("#end_to").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let zone = $('#zone').val();
            let department = $('#department').val();
            let href = '{{ sc_route_admin('admin_report_print_stamp.export_excel') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_date_from=' + order_date_from + '&order_date_to=' + order_date_to + '&department=' + department+ '&zone=' + zone;
            window.location.href = href;
        });
        function saveFilePdf() {
            let data_count = $('#data_count').val();
            if (data_count > 9999) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let ids = selectedRows().join();
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let order_date_from =  $("#order_date_from").val();
            let order_date_to = $("#order_date_to").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let zone = $('#zone').val();
            let department = $('#department').val();
            let href = '{{ sc_route_admin('admin_report_print_stamp.download_file_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_date_from=' + order_date_from + '&order_date_to=' + order_date_to + '&department=' + department+ '&zone=' + zone;
            $("#loading").show();
            printPage(href);
            // window.location.href = href;
        }
        function printStampPdf() {
            let data_count = $('#data_count').val();
            if (data_count > 3000) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let ids = selectedRows().join();
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let order_date_from =  $("#order_date_from").val();
            let order_date_to = $("#order_date_to").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let zone = $('#zone').val();
            let department = $('#department').val();
            let href = '{{ sc_route_admin('admin_report_print_stamp.preview_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_date_from=' + order_date_from + '&order_date_to=' + order_date_to + '&department=' + department+ '&zone=' + zone;
            $("#loading").show();
            printPage(href);

        }

    </script>
@endpush
