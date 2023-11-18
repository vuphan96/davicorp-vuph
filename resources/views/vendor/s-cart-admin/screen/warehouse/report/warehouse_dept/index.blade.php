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
                    <form class="table-responsive table-input">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 5%; padding: 0; text-align: center; vertical-align: middle; height: inherit">
                                    <label for="checkAll"></label><input class="selectall-checkbox grid-row-checkbox" type="checkbox" id="checkAll">
                                </th>
                                @foreach ($listTh as $key => $th)
                                    <th style="{!! $cssTh[$key] ?? ''!!}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataTr as $keyRow => $tr)
                                <tr>
                                    <td style="padding-left: 12px; text-align: center">
                                        <input class="list-checkbox grid-row-checkbox" type="checkbox"
                                               data-id="{{ $keyRow }}" data-status="{{ $tr['status_id'] ?? '' }}" data-customer_kinds="{{ $tr['customer_kind'] ?? '' }}" data-id_names="{{ $tr['id'] ?? '' }}">
                                    </td>
                                    @foreach ($tr as $key => $trtd)
                                        @if($key !== 'id')
                                            <td style="{!! $cssTd[$key] ?? ''!!}">{!! $trtd !!}</td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </form>

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
    @include($templatePathAdmin.'screen.warehouse.report.warehouse_dept.modal_create_export_order')
    @include($templatePathAdmin.'screen.warehouse.report.warehouse_dept.modal_history_report')
@endsection
@push('styles')
    <style>
        @media (min-width: 1250px) {
            .grid-template {
                display: grid;
                grid-template-columns: 10% 14% 10% 30px 10% 15% 17%;
                grid-column-gap: 10px;
                justify-content: right;
            }

            .item-one {
                display: inline-grid;
                justify-content: right;
            }
        }

        .select-custom {
            display: none !important;
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
    <link rel="stylesheet" type="text/css"
          href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
@endpush

@push('scripts')
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script type="text/javascript">
        function closePrint() {
            document.body.removeChild(this.__container__);
        }

        function setPrint() {
            this.contentWindow.__container__ = this;
            this.contentWindow.onbeforeunload = closePrint;
            this.contentWindow.onafterprint = closePrint;
            this.contentWindow.focus(); // Required for IE
            $("#loading").hide();
            let startDate = $('#from_to').val();
            let endDate = $('#end_to').val();
            document.title = "BaoCaoNoHang-" + startDate + " - " + endDate;
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

        $(".date_time").datepicker({dateFormat: "{{ config('admin.datepicker_format') }}"});

        $('a.page-link').on('click', function () {
            $('#loading').show()
        })

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

            $('#customer').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                filterPlaceholder: 'Tìm theo khách hàng',
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                selectAllText: 'Chọn tất cả!',
                maxHeight: 400,
                dropUp: true,
                includeResetOption: true,
                resetText: "Đặt lại"
            });
        });

    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
        var checked = [];
        var originalList = @json(array_values($originalList));
        var checkedList = [];

        let selectedRows = function () {
            let selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });

            return selected;
        }
        function savePdf() {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let warehouse = $('#warehouse').val();
            let key_search = '{{ request('key_search') ?? '' }}';
            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('warehouse_report_product_dept.print') }}?keyword=' + keyword + '&category=' + category + '&date_start=' +
                date_start + '&date_end=' + date_end + '&key_search=' + key_search + '&warehouse=' + warehouse;
            $("#loading").show();
            printPage(href);
        }

        $('#button_export').on('click', function () {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let warehouse = $('#warehouse').val();
            let key_search = '{{ request('key_search') ?? '' }}';
            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }

            let href = '{{ sc_route_admin('warehouse_report_product_dept.export') }}?keyword=' + keyword +
                '&category=' + category + '&date_start=' + date_start + '&date_end=' + date_end +
                '&key_search=' + key_search + '&warehouse=' + warehouse;

            window.location.href = href;
        });
        $('#submit_report').click(function () {
            let product = $('#product_id').val();
            if(product == ''){
                $('#key_search').val(null);
                alertMsg("error", "Vui lòng chọn sản phẩm trước khi lọc !");
                return  false;
            }
            $( "#button_search" ).on( "submit", function() {
                $('#loading').show()
            } );

        })

        $('.list-checkbox, .list-checkbox-modal').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */,
        });

        $('.selectall-checkbox, .selectAll-checkbox-modal').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%',
            labelHover: true,
        });

        $('.selectall-checkbox').on('ifClicked', function(event) {
            if(!$('.selectall-checkbox').is(":checked")){
                $('.list-checkbox').iCheck('check');
            } else {
                $('.list-checkbox').iCheck('uncheck');
            }
        });

        $('.list-checkbox').on('ifChanged', function(event) {
            let localChecked = []
            let selected = $('.table-input input[type="checkbox"]:checked');
            selected.each((index, item) => {
                let id = $(item).attr('data-id');
                localChecked.push(id);
            });
            checked = localChecked.filter(i => i);
            if(checked.length === $('.list-checkbox').length){
                $('.selectall-checkbox').iCheck('check');
            } else {
                $('.selectall-checkbox').iCheck('uncheck');
            }
        });

        $('.selectAll-checkbox-modal').on('ifClicked', function(event) {
            if(!$('.selectAll-checkbox-modal').is(":checked")){
                $('.list-checkbox-modal').iCheck('check');
            } else {
                $('.list-checkbox-modal').iCheck('uncheck');
            }
        });

        function handleWarehouseExport() {
            if(checked.length < 1){
                alertMsg('error', 'Vui lòng chọn ít nhất một mặt hàng');
                return;
            }
            $('#exportWarehouseModal').modal('show');
            let trData = ``;
            originalList.filter(item => checked.includes(item.id)).forEach((item, key) => {
                // checkedList.push(item);
                const qtyOrigin = item.qty_dept - item.qty_export;
                trData += `
                    <tr>
                        <td>${formatDateVn(item.export_date)}</td>
                        <td>${item.product_code}</td>
                        <td>${item.product_name}</td>
                        <td>${item.export_code}</td>
                        <td>${item.order_id_name}</td>
                        <td>${item.customer_name}</td>
                        Number.parseFloat(datum.qty_dept_origin - datum.qty_export_current).toFixed(2)
                        <td>${Number.parseFloat(qtyOrigin).toFixed(2)}</td>
                        <td>
                            <input
                                type="number"
                                id="input-${item.id}"
                                max="${item.qty_dept - item.qty_export}"
                                min="0"
                                step="0.01"
                                class="form-control modal-export-select"
                                onchange="
                                    const action = () => {
                                    const value = Number(this.value);
                                        if (value > ${qtyOrigin})
                                        {
                                            this.value = ${qtyOrigin};
                                            alertJs('error', 'Số lượng xuất không được lớn hơn số lượng nợ');
                                        }
                                        if (value < 0){
                                            this.value = 0;
                                            alertJs('error', 'Số lượng xuất phải lớn hơn 0');
                                        }
                                    };
                                    action();
                                "
                            />
                        </td>
                    </tr>
                `;
            });

            $('#data_modal_row').html(trData);
        }
        function handleSubmitWarehouseExport(){
            const warehouse = $('#select_warehouse').val();
            if(warehouse == ''){
                alertMsg("error", "Vui lòng chọn kho trước khi xuất")
                return;
            }
            const submitData =  originalList.filter(item => checked.includes(item.id)).map((item) => {
                let qty = $('#input-' + item.id).val();
                if(Number(qty) === 0){
                    alertMsg("error", "Số lượng trả trống", item.product_name + " thiếu thông tin số lượng trả");
                    return;
                }
                return {
                    id: item.id,
                    qty_reality: qty,
                    note: ''
                }
            });
            $.ajax({
                url: '{{ route('warehouse_report_product_dept.create_export_order') }}',
                method: 'POST',
                contentType:"application/json",
                data: JSON.stringify({
                    data_export: JSON.stringify(submitData),
                    warehouse: warehouse,
                    _token: '{{ csrf_token() }}'
                }),
                success: function(data, textStatus, jqXHR) {
                    // When AJAX call is successfuly
                    if((textStatus == 'success') && data.success){
                        $('#exportWarehouseModal').modal('hide');
                        alertMsg("success", "Xuất kho thành công");
                        location.reload();
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // When AJAX call has failed
                    console.log('AJAX call failed.');
                    console.log(textStatus + ': ' + errorThrown);
                },
                complete: function() {
                    console.log('AJAX call completed');
                }
            });
        }

        function showModalHistory(id) {
            $.ajax({
                method: 'get',
                url: '{{ sc_route_admin('warehouse_report_product_dept.getDataShowHistory') }}',
                data: {
                    id: id,
                },
                success: function (data) {
                    console.log(data);
                    let html = '';
                    if (data.report.history) {
                        $('#nameProduct').text(data.report.product_name)
                        $('#nameIdOrder').text(data.report.order_id_name)
                        $('#nameCustomer').text(data.report.customer_name)
                        for (let datum of data.report.history) {
                            let date = new Date(datum.created_at);
                            html += `<tr>
                                <td><input class="list-checkbox-modal" type="checkbox" value="${datum.id}"></td>
                                <td>${formatDateVn(datum.created_at)}</td>
                                <td>${datum.user_name}</td>
                                <td align="center">${datum.qty_dept_origin}</td>
                                <td align="center">${datum.qty_export_current}</td>
                                <td align="center">${ Number.parseFloat(datum.qty_dept_origin - datum.qty_export_current).toFixed(2)}</td>
                                </tr>`;
                        }
                    } else {
                        alertMsg("error", "Lịch sử nợ trống", "Không tìm thấy lịch sử sản phẩm này");
                    }
                    $('#data_modal_history').html(html);
                }
            });
            $('#modalHistoryReportDept').modal();
        }

        $('#btnPrintStamps').click(function () {
            let ids = [];
            $('.list-checkbox-modal:checked').each(function () {
                ids.push($(this).val());
            });
            console.log(ids)
            let href = '{{ sc_route_admin('warehouse_report_product_dept.print_tem') }}&ids=' + ids;
            printPage(href);
        })

        /**
         * Intem bổ sung
         */
        function printStampPdf() {
            let ids = '';
            if(selectedRows().length >0) {
                let arrayId = selectedRows().filter(Boolean);
                ids = arrayId.join();
            }

            let keyword = $("#keyword").val() ?? '';
            let category = $("#category").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let product_kind = $('#product_kind').val();
            let customer = $('#customer').val();
            let key_search = '{{ request('key_search') ?? '' }}';
            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('warehouse_report_product_dept.print_tem') }}?keyword=' + keyword + '&category=' + category + '&from_to=' +
                from_to + '&end_to=' + end_to + '&key_search=' + key_search +
                '&product_kind=' + product_kind + '&customer=' + customer + '&ids=' + ids;
            printPage(href);
        }
    </script>
@endpush
