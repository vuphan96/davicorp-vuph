@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12 card">
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

                    <div class="float-left" style="margin-left: -12px">
                        <div class="row">

                            @if (!empty($removeList))
                                <div class="menu-left">
                                    <button type="button" class="btn btn-default grid-select-all"><i
                                                class="far fa-square"></i></button>
                                </div>
                            @endif
                            @if (!empty($buttonRefresh))
                                <div class="menu-left">
                                    <span class="btn btn-flat btn-primary grid-refresh"
                                          title="{{ sc_language_render('action.refresh') }}"><i class="fas fa-sync-alt"></i></span>
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
                                @if (!empty($removeList))
                                    <th style="width: 5%">
                                    </th>
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
                                                   data-id="{{ $keyRow }}" data-type="{{ $tr['type_order'] ?? '' }}">
                                        </td>
                                    @endif
                                    @foreach ($tr as $key => $trtd)
                                        @if($key != 'type_order')
                                            <td style="{!! $cssTd[$key] ?? ''!!}">{!! $trtd !!}</td>
                                        @endif
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
    <!-- Modal export excel -->
    <div class="modal fade" id="exportDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form id="form_excel_export" class="modal-content" method="post" action="{{ $urlExport ?? '' }}">
                @csrf
                <input type="hidden" name="ids" id="excel_ids" value="">
                <input type="hidden" name="filter" id="excel_filter" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i
                                class="fas fa-file-excel mr-2"></i>Tuỳ chọn xuất dữ liệu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-2">Đối tượng xuất</label>
                        <div class="col-10">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="option" id="option_filter" type="radio" class="custom-control-input"
                                       value="0" checked>
                                <label for="option_filter" class="custom-control-label">Theo bộ lọc</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input name="option" id="option_seleted" type="radio" class="custom-control-input"
                                       value="1">
                                <label for="option_seleted" class="custom-control-label">Đối tượng đã chọn</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal"><i
                                class="fa fa-undo mr-2"></i>Đóng
                    </button>
                    <button type="button" id="btnConfirmExport" class="btn btn-primary"><i
                                class="fa fa-file-export mr-2"></i>Xuất
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Modal chon san pham --}}
    @include($templatePathAdmin.'screen.warehouse.report.return_import.model_import_warehouse')
@endsection


@push('styles')
    {!! $css ?? '' !!}
    <link rel="stylesheet" href="{{ asset("admin/plugin/bootstrap-editable.css") }}"/>
    <style>
        table.list_table tr td:last-child, table.list_table th:last-child {
            min-width: 180px;
            max-width: 180px;
            width: 1%;
            text-align: center;
            padding-left: 12px !important;
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
        th , td {
            white-space: normal !important;
            word-break: normal !important;
        }
        .lockDatePicker, .multiselect-container {
            z-index: 99998 !important;
        }
        .editable-qty {
            width: 100%;
            box-sizing: border-box; 
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
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
            document.title = "DonNhapHang-"  + startDate + " - " + endDate;
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
        $('.grid-refresh').click(function () {
            $.pjax.reload({container: '#pjax-container'});
        });

        $(document).on('submit', '#button_search', function (event) {
            $('#loading').show()
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide();
            $(".box-body input[type='checkbox']").iCheck("uncheck");
            $(".far", this).removeClass("fa-check-square").addClass('fa-square');
        })
        $(function () {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 3000; // time in milliseconds
            }
            $(".date_time").datepicker({ dateFormat: "dd/mm/yy" });
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
        let selectedRows = function () {
            let selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });
            return selected;
        }
        // open popup
        function openPopup() {
            let ids = selectedRows().join();
            if (ids === "") {
                alertMsg('error', 'Vui lòng chọn đơn hàng để tạo phiếu!');
                return;
            }
            $('#id_order').val(ids);
            $.ajax({
                url: '{{ sc_route_admin('warehouse_report_return_import.getDataShowPopup') }}',
                method: 'POST',
                data: {
                    id: ids,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', 'Lỗi dữ liệu!');
                        return;
                    } else {
                        $('#modalSelectWarehouse').modal();
                        let html = '';
                        if(data)
                        if (data && data.detail && data.detail.length > 0) {
                            for (let datum of data.detail) {
                            html += '<tr>\n' +
                                '<td>' + datum.date_return + '</td>\n' +
                                '<td>' + datum.product_code + '</td>\n' +
                                '<td>' + datum.product_name + '</td>\n' +
                                '<td>' + datum.code_order + '</td>\n' +
                                '<td>' + datum.name_customer + '</td>\n' +
                                '<td>' + formatNumber(datum.qty) + '</td>\n' +
                                '<td><input  type="number" onchange="checkQty(this)" class="editable-qty qty-'+datum.id+'" min="0" max="' + datum.qty + '" value="' + datum.qty_entered + '"  data-row-id="' + datum.product_code + '"></td>\n' +
                                '</tr>';
                            }
                        } else {
                            html = '<tr><td colspan="6">Chưa có dữ liệu!</td></tr>';
                        }
                        $('#modalSelectWarehouse tbody').html(html);
                    }
                },
            });
        }

        function checkQty(input) {
            let $input = $(input);
            let editedValue = parseFloat($input.val());
            let maxValue = parseFloat($input.attr('max'));
            if (editedValue > maxValue) {
                alertMsg('error', 'Số lượng không được vượt quá ' + formatNumber(maxValue));
                $input.val(maxValue);
            }
        }

        $('#comfirmData').on('click', function() {
            let submitData = [];
            let warehouse = $('#warehouse').val();
            if(warehouse ==''){
                alertMsg('error', 'Vui lòng chọn kho để nhập hàng!');
                return;
            }
            $('.grid-row-checkbox:checked').each(function () {
                let id = $(this).data('id');
                let qty = $('.qty-'+id).val();
                let type = $(this).data('type');
                submitData.push([id,qty,type]);
            });
            $.ajax({
                url: "{{ route('warehouse_report_return_import.create_order_import') }}",
                method: 'POST',
                data: {
                    data: submitData,
                    warehouse : warehouse,
                    _token: '{{ csrf_token() }}'
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#loading').show();
                },
                complete: function() {
                    $('#loading').hide();
                },
                success: function(response) {
                    if (response.success) {
                        alertMsg('success', "Sản phẩm đã được nhập thành công!");
                        location.reload();
                    } else {
                        alertMsg('Có lỗi xảy ra: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alertMsg('Có lỗi xảy ra: ' + error);
                }
            });
        });
        function closePopup() {
            document.getElementById("myPopup").style.display = "none";
        }
        $('#btn-submit-search').click(function () {
            $('#end_to_time').val($('#end_to').val());
            $('#from_to_time').val($('#from_to').val());
        })

        $('.grid-trash').on('click', function () {
            let ids = selectedRows().join();
            deleteItem(ids);
        });

        $('#btn_export').on('click', function () {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let customer = $('#customer').val();
            let product_kind = $('#product_kind').val();
            let key_search = '{{ request('key_search') ?? '' }}';

            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }

            let href = '{{ sc_route_admin('warehouse_report_return_import.export') }}?keyword=' + keyword +
                '&category=' + category + '&date_start=' + date_start + '&date_end=' + date_end +
                '&product_kind=' + product_kind + '&customer=' + customer;

            window.location.href = href;
        });

        function savePdf() {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let customer = $('#customer').val();
            let product_kind = $('#product_kind').val();
            let key_search = '{{ request('key_search') ?? '' }}';

            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('warehouse_report_return_import.print') }}?keyword=' + keyword +
                '&category=' + category + '&date_start=' + date_start + '&date_end=' + date_end +
                '&product_kind=' + product_kind + '&customer=' + customer;

            $("#loading").show();
            printPage(href)
        }
    </script>

    {!! $js ?? '' !!}
@endpush
