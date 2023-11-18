@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-6"><h3><b>Khách hàng Davicorp</b></h3></div>
        <div class="col-md-6"><h3><b>Khách hàng Davicook</b></h3></div>
    </div>
    {{--    tổng số lượng khách hàng--}}
    <div class="row">

        @if (config('admin.admin_dashboard.total_order'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ sc_language_render('admin.dashboard.total_customer_not_order') }}</span>
                        <span class="info-box-number">{{ number_format($totalCustomerDavicorp - $totalCustomerDavicorpInDay) }}</span>
{{--                        <a href="{{ sc_route_admin('admin_customer.index') }}?delivery_date={{ $delivery_date }}" class="small-box-footer">--}}
{{--                            {{ sc_language_render('action.detail') }}&nbsp;--}}
{{--                            <i class="fa fa-arrow-circle-right"></i>--}}
{{--                        </a>--}}
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        @endif

        @if (config('admin.admin_dashboard.total_product'))
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tags"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ sc_language_render('admin.dashboard.total_customer_in_day') }}</span>
                        <span class="info-box-number">{{ number_format($totalCustomerDavicorpInDay) }}</span>
                        <a href="{{ sc_route_admin('admin_order.index') }}?from_to={{ $delivery_date }}&end_to={{ $delivery_date }}" class="small-box-footer">
                            {{ sc_language_render('action.detail') }}&nbsp;
                            <i class="fa fa-arrow-circle-right"></i>
                        </a>

                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
        @endif

        @if (config('admin.admin_dashboard.total_customer'))
        <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="ion ion-ios-people-outline"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ sc_language_render('admin.dashboard.total_customer_not_order') }}</span>
                        <span class="info-box-number">{{ number_format($totalCustomerDavicook - $totalCustomerDavicookInDay) }}</span>
{{--                        <a href="{{ sc_route_admin('admin.davicook_customer.index') }}" class="small-box-footer">--}}
{{--                            {{ sc_language_render('action.detail') }}&nbsp;--}}
{{--                            <i class="fa fa-arrow-circle-right"></i>--}}
{{--                        </a>--}}
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        @endif

        @if (config('admin.admin_dashboard.total_blog'))
        <!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tags"></i></span>

                    <div class="info-box-content">
                        <span class="info-box-text">{{ sc_language_render('admin.dashboard.total_customer_in_day') }}</span>
                        <span class="info-box-number">{{ number_format($totalCustomerDavicookInDay) }}</span>
                        <a href="{{ sc_route_admin('admin.davicook_order.index') }}?from_to={{ $delivery_date }}&end_to={{ $delivery_date }}" class="small-box-footer">
                            {{ sc_language_render('action.detail') }}&nbsp;
                            <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                    <!-- /.info-box-content -->
                </div>
                <!-- /.info-box -->
            </div>
            <!-- /.col -->
        @endif
    </div>
    <!-- /.row -->

    @if (config('admin.admin_dashboard.order_month'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ sc_language_render('admin.dashboard.order_month') }}</h5>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="chart-days" style="width:100%; height:auto;"></div>
                            </div>
                        </div>
                        <!-- /.row -->
                    </div>
                    <!-- ./card-body -->
                    <!-- /.card-footer -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
    @endif
    <div class="row">
        <div class="col-12 card">
            <div class="card customer-list">
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
                    <!-- /.box-tools -->
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0" id="pjax-container">
                    <div class="table-responsive">
                        <table class="table table-hover box-body text-wrap table-bordered list_table">
                            <thead>
                            <tr>
                                <th style="width: 20px">STT</th>
                                <th style="width: 100px">Mã khách hàng</th>
                                <th style="width: 40%">Tên khách hàng</th>
                                <th style="width: 150px">Email</th>
                                <th style="width: 150px">Số điện thoại</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i = $dataCustomers->firstItem(); @endphp
                            @foreach ($dataCustomers as $keyRow => $tr)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $tr->customer_code }}</td>
                                    <td>{{ $tr->name }}</td>
                                    <td>{{ $tr->email }}</td>
                                    <td>{{ $tr->phone }}</td>
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
@endsection

@push('styles')
    <style type="text/css">
        .info-box-text {
            white-space: pre-line !important;
        }
        .info-box {
            height: 116px;
        }
        .info-box-icon {
            min-width: 80px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ sc_file('admin/plugin/chartjs/highcharts.js') }}"></script>
    <script src="{{ sc_file('admin/plugin/chartjs/highcharts-3d.js') }}"></script>
    <script type="text/javascript">
        $('a.page-link').on('click', function () {
            $('#loading').show()
        })
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
        document.addEventListener('DOMContentLoaded', function () {
            var myChart = Highcharts.chart('chart-days', {
                credits: {
                    enabled: false
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: {!! json_encode(array_keys($orderInMonth)) !!},
                    crosshair: false

                },

                yAxis: [{
                    min: 0,
                    title: {
                        text: '{{ sc_language_render('admin.chart.order') }}'
                    },
                }, {
                    title: {
                        text: '{{ sc_language_render('admin.chart.amount') }}'
                    },
                    opposite: true
                },],

                legend: {
                    align: 'left',
                    verticalAlign: 'top',
                    borderWidth: 0
                },

                tooltip: {
                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                        '<td style="padding:0"><b>{point.y:.0f} </b></td></tr>',
                    footerFormat: '</table>',
                    shared: true,
                    useHTML: true
                },
                plotOptions: {
                    column: {
                        pointPadding: 0.2,
                        borderWidth: 0
                    },
                },
                series: [{
                    type: 'column',
                    name: '{{ sc_language_render('admin.chart.order') }}',
                    data: {!! json_encode(array_values($orderInMonth)) !!},
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.0f}'
                    }
                },
                    {
                        type: 'line',
                        name: '{{ sc_language_render('admin.chart.amount') }}',
                        color: '#c7730c',
                        yAxis: 1,
                        data: {!! json_encode(array_values($amountInMonth)) !!},
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            borderRadius: 3,
                            backgroundColor: 'rgba(252, 255, 197, 0.7)',
                            borderWidth: 0.5,
                            borderColor: '#AAA',
                            y: -6
                        }
                    },
                ]
            });
        });

        // Scroll to bottom when search customer list
        $("#button_search").submit(function() {
            sessionStorage.setItem("check", 1);
        });
        if (sessionStorage.getItem("check")) {
            $("body,html").animate({
                scrollTop: $(".customer-list").offset().top
            }, 50);
            sessionStorage.removeItem("check");
        }
    </script>
@endpush
