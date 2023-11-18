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
                                <th style="width: 18px; text-align: center; padding-left: 0.75rem">
                                    <div class="menu-left">
                                        <button type="button" class="btn btn-default grid-select-all"><i class="far fa-square"></i></button>
                                    </div>
                                </th>
                                @foreach ($listTh as $key => $th)
                                    <th style="{!! $cssTh[$key] ?? ''!!}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($dataTr as $keyRow => $tr)
                                <tr>
                                    <td width="18px" align="center">
                                        <input class="checkbox grid-row-checkbox" type="checkbox" data-id="{{ $dataTr[$keyRow]['detail_id'] ?? '' }}" data-name="{{ $dataTr[$keyRow]['product_id'] ?? '' }}">

                                    </td>
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
    <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true" id="ExportModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xuất kho từ báo cáo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title">Bạn chắc chắn xuất kho theo các trường lọc như trên, vui lòng chọn kho cần xuất!</h5>
                    <br>
                    <select class="form-control select2" id="select_warehouse" style="width: 90%">
                        <option value="">-- Chọn kho --</option>
                        @foreach($dataWarehouse as $key => $warehouse)
                            <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" id="export_all_item">Xuất</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
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
        .status-1{
            border: 1px solid #94B9D6;
            border-radius: 5px;
            color: #94B9D6;
            background-color: #F2F9FF;
            padding: 3px;
            box-sizing: border-box;
        }
        .status-2{
            border: 1px solid #9CB880;
            border-radius: 5px;
            color: #9CB880;
            background-color: #F2F9FF;
            padding: 3px;
            box-sizing: border-box;
        }
        .status-3{
            border: 1px solid #94B9D6;
            border-radius: 5px;
            color: #94B9D6;
            background-color: #F2F9FF;
            padding: 3px;
            box-sizing: border-box;
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
                if($(this).data('id')) {
                    selected.push($(this).data('id'));
                }
            });
            return selected;
        }
        var selectedProduct = function () {
            var productSelected = [];
            $('.grid-row-checkbox:checked').each(function () {
                productSelected.push($(this).data('name'));
            });
            return productSelected;
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
            let from_to = $("#date_start").val();
            let order_send_time_from =  $("#order_send_time_from").val();
            let order_send_time_to = $("#order_send_time_to").val();
            let end_to = $("#date_end").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let content = $("#content").val();
            let href = '{{ sc_route_admin('admin_report_print_stamp_extra.export_excel') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_send_time_from=' + order_send_time_from + '&order_send_time_to=' + order_send_time_to + '&key_export=' + key_export + '&content=' + content;
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
            let from_to = $("#date_start").val();
            let end_to = $("#date_end").val();
            let order_send_time_from =  $("#order_send_time_from").val();
            let order_send_time_to = $("#order_send_time_to").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let content = $("#content").val();
            let href = '{{ sc_route_admin('admin_report_print_stamp_extra.download_file_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_send_time_from=' + order_send_time_from + '&order_send_time_to=' + order_send_time_to + '&key_export=' + key_export + '&content=' + content;
            $("#loading").show();
            printPage(href);
        }
        function printStampExtraPdf() {
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
            let from_to = $("#date_start").val();
            let end_to = $("#date_end").val();
            let order_send_time_from = $("#order_send_time_from").val();
            let order_send_time_to = $("#order_send_time_to").val();
            let category = $("#category option:selected").val();
            let key_export = $("#key_export").val();
            let content = $("#content").val();
            let href = '{{ sc_route_admin('admin_report_print_stamp_extra.preview_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&ids=' + ids + '&category=' + category + '&key_export=' + key_export + '&order_send_time_from=' + order_send_time_from + '&order_send_time_to=' + order_send_time_to + '&key_export=' + key_export + '&content=' + content;
            $("#loading").show();
            printPage(href);

        }

        var dataTmp = <?php echo json_encode($dataTr ?? '[]', 15, 512) ?>;
        function updateExportWarehouse() {
            let keyword =  $("#keyword").val();
            let key_export = $("#key_export").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let order_send_time_from = $("#order_send_time_from").val();
            let order_send_time_to = $("#order_send_time_to").val();
            let category = $("#category option:selected").val();
            let content = $("#content").val();
            let detail_id = selectedRows();
            let product_id = selectedProduct();
            let array_detail_id = [];
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            product_id.forEach(function (value) {
                if(value) {
                    dataTmp.forEach(function (item) {
                        if(item.check_product_id == value){
                            array_detail_id.push(item.detail_id);
                        }
                    })
                }
            })
            let arrayId = [...new Set(array_detail_id.concat(detail_id))];
            let ids = arrayId.join();
            // console.log(ids)
            $.ajax({
                url: '{{ sc_route_admin('admin_report_2target_extra.update_detail') }}',
                type: 'post',
                data: {
                    ids: ids,
                    keyword:keyword,
                    key_export:key_export,
                    date_start:date_start,
                    date_end:date_end,
                    order_send_time_from:order_send_time_from,
                    order_send_time_to:order_send_time_to,
                    category:category,
                    content:content,
                    _token: '{{ csrf_token() }}',
                },

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.error == 1) {
                        alertJs('error','Lỗi lưu dữ liệu: ' + response.message);

                    } else {
                        alertMsg('success', "Cập nhật thành công !");
                        location.reload();
                    }
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        }

    </script>
@endpush
