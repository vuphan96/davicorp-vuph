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
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr style="width: 100%" class="heading-report">
                                <th rowspan="2">STT</th>
                                <th rowspan="2">Mã vật tư</th>
                                <th rowspan="2">Tên Vật tư</th>
                                <th rowspan="2">ĐVT</th>
                                <th colspan="2">Hàng xuất theo ngân hàng TĐ ({{ $number_of_servings }})</th>
                                <th colspan="2">Hàng xuất thực tế ({{ $number_of_servings_fact }})</th>
                                <th colspan="2">Chênh lệch</th>
                            </tr>
                            <tr style="width: 100%" class="heading-report">
                                <th>Số lượng</th>
                                <th>Giá trị</th>
                                <th>Số lượng</th>
                                <th>Giá trị</th>
                                <th>Số lượng</th>
                                <th>Giá trị</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i = 1 @endphp
                            @foreach ($result as $key => $value)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>
                                        {{ $value['product_code'] }}
                                    </td>
                                    <td>
                                        <a target="_blank" href="{{ sc_route_admin('admin_report_quantity_diference.detail', $value['product_id']) . '?from_to=' . request('from_to'). '&end_to=' . request('end_to'). '&keyword=' . request('keyword') }}">
                                            {{ $value['product_name'] ?? "Tên sản phẩm bị xóa" }}
                                        </a>
                                    </td>
                                    <td>{{ $value['product_unit'] }}</td>
                                    <td style="text-align: right">{{ number_format($value['qty_total'], 2)  }}</td>
                                    <td style="text-align: right">{{ number_format($value['price_menu']) }}</td>
                                    <td style="text-align: right">{{ number_format($value['qty_total_fact'] + $value['extra_bom'], 2) }}</td>
                                    <td style="text-align: right">{{ number_format($value['price_menu_fact'] + $value['extra_bom_price']) }}</td>
                                    <td style="text-align: right">{{ number_format($value['qty_total'] - ($value['qty_total_fact'] + $value['extra_bom']), 2) }}</td>
                                    <td style="text-align: right">
                                        {{ $value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']) < 0 ? '(' . number_format(abs($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price']))) . ')'
                                    : number_format($value['price_menu'] - ($value['price_menu_fact'] + $value['extra_bom_price'])) }}
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
            </div>
            <!-- /.card -->
        </div>
    </div>
@endsection


@push('styles')
    <style>
        .heading-report th {
            text-align: center;
            vertical-align: middle !important;
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
@endpush

@push('scripts')

    <script type="text/javascript">


        $(document).on('submit', '#button_search', function (event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide()
        })

        // tag a
        $(function () {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })
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
            document.title = "BaoCaoChenhLechSuatAn-" + startDate + " - " + endDate;
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
        $('#btn_export').on('click', function () {
            let ids = selectedRows().join();
            const form = $('#form_ids_product_excel');
            const ids_input = $('#product_ids_excel');
            ids_input.val(ids);
            form.submit();
        });

        $('#submit_meal_difference').click(function () {
            $('#key_search').val('searched');
        })

        function savePdf() {
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_search = $('#key_search').val();
            let key_request_search = '{{ request('key_search') }}';
            if (key_search != 'searched' && key_request_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('admin_report_quantity_diference.export_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to;
            $("#loading").show();
            printPage(href);
        }

        $('#button_export_filter').on('click', function () {
            let keyword = $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_search = $('#key_search').val();
            let key_request_search = '{{ request('key_search') }}';
            if (key_search != 'searched' && key_request_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('admin_report_quantity_diference.export_excel') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to;
            window.location.href = href;
        });
    </script>
@endpush
