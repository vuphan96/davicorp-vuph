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
                    <div class="float-left">
                        @if (!empty($buttonRefresh))
                            <div class="menu-left">
                                <h5><strong>TỔNG DOANH THU:</strong> &nbsp; <strong>{{ ($total_revenue ?? '')  . '₫' }}</strong></h5>
                            </div>
                        @endif
                    </div>
                </div>


                <!-- /.card-header -->
                <div class="card-body p-0" id="pjax-container">
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr style="width: 100%">
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
                            <tr>
                                <td colspan="6" ><b>Tổng cộng</b></td>
                                <td colspan="1" style="text-align: right"><b>{{ $revenueByPage . '₫' ?? '' }}</b></td>
                            </tr>
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
            // $('#loading').show()
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide()
        })

        $('a.page-link').on('click', function () {
            $('#loading').show()
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

    </script>
    {{-- //End pjax --}}


    <script>
        function closePrint() {
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
            document.title = "BaoCaoDoanhThu-"  + startDate + " - " + endDate;
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

        $('.btn-search').on('click', function (e) {
            $('#check_filter').val('1');
            setTimeout(() => {
                location.reload()
            }, 100);
        })
        function savePdf() {
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let department = $("#department").val();
            let object = $("#object").val();
            let note = $("#note").val();
            let href = '{{ sc_route_admin('admin_report_revenue.export_pdf') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&department=' + department + '&object=' + object + '&note=' + note;
            $("#loading").show();
            printPage(href);
        }

        $('#button_export_filter').on('click', function () {
            let check_filter = $("#check_filter").val() ?? '';
            let key_request_search = '{{ request('check_filter') }}';
            if(check_filter == '' && key_request_search=='') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất")
                return;
            }
            let keyword = $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let department = $("#department").val();
            let object = $("#object").val();
            let note = $("#note").val();
            let href = '{{ sc_route_admin('admin_report_revenue.export_excel') }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to + '&department=' + department + '&object=' + object + '&note=' + note;
            window.location.href = href;
        });
    </script>
@endpush
