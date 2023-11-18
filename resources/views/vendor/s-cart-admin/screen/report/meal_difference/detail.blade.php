@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12 card">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools" style="width: 100% !important;">
                        <div class="input-group float-left">
                            <div id="text-product" class="col-lg-10 col-md-10"
                                 style="">Báo cáo chi tiết: <p style="display: inline; text-transform: uppercase">{{ isset( $result->first()->name ) ? $result->first()->name : 'Sản phẩm đã bị xóa' }} </p>
                            </div>
                            <div id="" class="col-lg-2 col-md-2 text-right">
                                <a class="btn btn-outline-primary" href="{{ session('nameUrlReportDifference') }}"><i class="fa fa-arrow-alt-circle-left"></i> Trở lại
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="" style="margin: 0">
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
                            <tr style="width: 100%" class="heading-report">
                                <th rowspan="2">STT</th>
                                <th rowspan="2">Ngày chứng từ</th>
                                <th colspan="2">Hàng xuất theo ngân hàng TĐ</th>
                                <th colspan="2">Hàng xuất thực tế</th>
                                <th colspan="2">Chênh lệch</th>
                                <th rowspan="2">Mã khách hàng</th>
                                <th rowspan="2">Tên khách hàng</th>
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
                                    <td>{{ $key + 1}}</td>
                                    <td style="text-align: right">
                                        {{ isset($value->bill_date) ? date_format(new DateTime($value->bill_date), "d/m/Y") : '' }}
                                    </td>
                                    <td style="text-align: right">{{ number_format($value->type == 1 ? 0 : $value->qty_total, 2) }}</td>
                                    <td style="text-align: right">{{ number_format($value->type == 1 ? 0 : ($value->price_menu ?? 0)) }}</td>
                                    <td style="text-align: right">{{ number_format($value->type == 1 ? $value->real_total_bom : $value->qty_total_fact, 2) }}</td>
                                    <td style="text-align: right">{{ number_format($value->type == 1 ? ($value->real_total_bom * $value->import_price) : ($value->price_menu_fact ?? 0))}}</td>
                                    <td style="text-align: right">{{ number_format($value->type == 1 ? (0 - $value->real_total_bom) : ($value->qty_total - $value->qty_total_fact), 2) }}</td>
                                    @if($value->type == 1)
                                        <td style="text-align: right">
                                            ({{ number_format(abs($value->price_menu - $value->price_menu_fact)) }})
                                        </td>
                                    @else
                                        <td style="text-align: right">
                                            {{ $value->price_menu - $value->price_menu_fact < 0 ? '(' . number_format(abs($value->price_menu - $value->price_menu_fact)) . ')'
                                        : number_format($value->price_menu - $value->price_menu_fact) }}
                                        </td>
                                    @endif

                                    <td style="text-align: left">{{ $value->customer_code ?? "Mã khách hàng đã bị xóa" }}</td>
                                    <td style="text-align: left">{{ $value->customer_name }}</td>
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
        #text-product {
            font-size: 20px;
            font-weight: bold;
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
    </script>
    <script type="text/javascript">
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
        $('#btn_export').on('click', function () {
            let ids = selectedRows().join();
            const form = $('#form_ids_product_excel');
            const ids_input = $('#product_ids_excel');
            ids_input.val(ids);
            form.submit();
        });

        function savePdf() {
            let keyword =  $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let href = '{{ sc_route_admin('admin_report_quantity_diference.detail.export_pdf', request('id')) }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to;
            window.location.href = href;
        }

        $('#button_export_filter').on('click', function () {
            let keyword = $("#keyword").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let href = '{{ sc_route_admin('admin_report_quantity_diference.detail.export_excel', request('id')) }}?keyword=' + keyword + '&from_to=' + from_to + '&end_to=' + end_to;
            window.location.href = href;
        });
    </script>
@endpush
