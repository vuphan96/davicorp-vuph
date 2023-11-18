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
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                @foreach ($listTh as $key => $th)
                                    <th style="{!! $cssTh[$key] ?? ''!!}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataTr as $keyRow => $tr)

                                <tr>
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
            document.title = "BaoCaoNhapHang-" + startDate + " - " + endDate;
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
        $(document).on('submit', '#button_search', function (event) {
            $('#loading').show()
        })

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
        function savePdf() {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let warehouse = $('#warehouse').val();
            let supplier = $('#supplier').val();
            let key_search = '{{ request('key_search') ?? '' }}';
            let zone = $('#zone').val();
            let department = $('#department').val();
            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('warehouse_report_export.print') }}?keyword=' + keyword + '&category=' + category + '&date_start=' +
                date_start + '&date_end=' + date_end + '&key_search=' + key_search +
                '&zone=' + zone + '&department=' + department + '&warehouse=' + warehouse + '&supplier=' + supplier;
            $("#loading").show();
            printPage(href);
        }

        $('#button_export_filter').on('click', function () {
            let keyword = $("#keyword").val();
            let category = $("#category").val();
            let date_start = $("#date_start").val();
            let date_end = $("#date_end").val();
            let warehouse = $('#warehouse').val();
            let supplier = $('#supplier').val();
            let key_search = '{{ request('key_search') ?? '' }}';
            let zone = $('#zone').val();
            let department = $('#department').val();
            if (key_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }

            let href = '{{ sc_route_admin('warehouse_report_export.export') }}?keyword=' + keyword +
                    '&category=' + category + '&date_start=' + date_start + '&date_end=' + date_end +
                    '&key_search=' + key_search + '&zone=' + zone + '&department=' + department +
                    '&warehouse=' + warehouse + '&supplier=' + supplier;

            window.location.href = href;
        });

    </script>
@endpush
