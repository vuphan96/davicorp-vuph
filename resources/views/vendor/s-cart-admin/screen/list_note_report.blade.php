@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12 card" style="padding: 0;">
            <div class="card" style="margin-bottom: 0">
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
                                    <th></th>
                                @endif
                                @foreach ($listTh as $key => $th)
                                    <th style="{!! $cssTh[$key] ?? ''!!}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $i = $dataTr->firstItem();
                            @endphp
                            @foreach ($dataTr as $keyRow => $tr)
                                @php
                                    $orderDetailItem = [];
                                @endphp
                                @foreach($tr->details as $key => $item)
                                    @if($item->comment != '')
                                        @php
                                            $orderDetailItem[] = [
                                                            'note' => $item->comment ?? '',
                                                            'qty' => number_format( ($item->real_total_bom ?? $item->qty_reality) , 2),
                                                            'product' => $item->product_name ?? ''
                                                        ];
                                        @endphp
                                    @endif
                                @endforeach
                                @php
                                    $num = count($orderDetailItem);
                                    $name = (empty($tr['name']) ? $tr['customer_name'] : $tr['name']) . ((isset($tr['object_id']) && $tr['object_id'] == 1) ? ' - GV' : '' );
                                @endphp
                                    <tr>
                                        <td rowspan="{{ !empty($num) ? $num : 1 }}" style="text-align: center">{{ $i ?? '' }}</td>
                                        <td rowspan="{{ !empty($num) ? $num : 1 }}">{{ $name }} </td>
                                        <td rowspan="{{ !empty($num) ? $num : 1 }}" >{{ $tr->id_name ? $tr->id_name . ' - ' . $tr->explain : '' }}</td>
                                        <td>{{ isset($orderDetailItem[0]['product']) ? ( $orderDetailItem[0]['product'] .'('. $orderDetailItem[0]['qty'] . ') : ' ) : '' }}
                                            {{ !empty($orderDetailItem[0]['note']) ? '{ ' . $orderDetailItem[0]['note'] . ' }' : '' }}</td>
                                        <td rowspan="{{ !empty($num) ? $num : 1 }}">{{ empty($tr->comment) ? '' : $tr->explain . ' : ' . $tr->comment }}</td>
                                    </tr>
                                    @foreach($orderDetailItem as $keyItem => $value)
                                        @if($keyItem > 0)
                                            <tr>
                                                <td>{{ $value['product'] ?? '' }} ({{$value['qty']}}) : {{ !empty($value['note']) ? '{ ' . $value['note'] . ' }' : '' }}</td>
                                            </tr>
                                        @endif

                                    @endforeach
                                    @php
                                        $i++;
                                    @endphp
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
        .table-bordered td{
            display: table-cell;
            vertical-align: middle;
        }
        .table-bordered tr:hover{
            background-color: white !important;
        }
        table.list_table tr td:last-child {
            min-width: 180px;
            max-width: 180px;
            /*text-align: center;*/
            padding-left: 12px !important;
        }

        table.list_table tr td:first-child {
            /*text-align: center;*/
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
                /*text-align: center;*/
            }
        }
    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}

    <script type="text/javascript">

        $('.grid-refresh').click(function () {
            $.pjax.reload({container: '#pjax-container'});
        });

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

        @if ($buttonSort)
        $('#button_sort').click(function (event) {
            var url = $('#url-sort').data('urlsort') + 'sort_order=' + $('#order_sort option:selected').val();
            $.pjax({url: url, container: '#pjax-container'})
        });
        @endif

    </script>
    {{-- //End pjax --}}


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
            let now = new Date();
            let startDate = $('#from_to').val();
            let endDate = $('#end_to').val();
            document.title = "BaoCaoGhiChu-"  + startDate + " - " + endDate;
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

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        $('#btn_export').on('click', function () {
            let ids = selectedRows().join();
            const form = $('#form_ids_product_excel');
            const ids_input = $('#product_ids_excel');
            ids_input.val(ids);
            form.submit();
        });

        $('#btn_priceboard_export').on('click', function () {
            let ids = selectedRows().join();
            const form = $('#form_ids_priceboard_excel');
            const ids_input = $('#priceboard_ids_excel');
            ids_input.val(ids);
            form.submit();
        });


        $('#btnCombine').on('click', function () {
            var ids = selectedRows().join();
            combineOrder(ids);
        });

        function print(id) {
            $('#print_ids').val(id);
            $('#printDialog').modal();
        }

        function showHistory(id) {
            $('#history_table_container').html('<span>{{ sc_language_render('action.loading') }}</span>');
            $.ajax({
                method: '{{ $method ?? 'get' }}',
                url: '{{ sc_route_admin('admin_point_view.history') }}',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#history_table_container').html(data);
                }
            });

            $('#rewardDialog').modal();
        }

        function savePdf() {
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let department_id = $('#department_id').val();
            let name = $('#name').val();
            let href = '{{ sc_route_admin('admin_report_note.export_pdf') }}?name=' + name + '&from_to=' + from_to + '&end_to=' + end_to + '&department=' + department_id;
            $("#loading").show();
            printPage(href);
            // window.location.href = href;
        }

        $('#button_export_filter').on('click', function () {
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let department_id = $('#department_id').val();
            let name = $('#name').val();
            let href = '{{ sc_route_admin('admin_report_note.export_excel') }}?name=' + name + '&from_to=' + from_to + '&end_to=' + end_to + '&department=' + department_id;
            window.location.href = href;
        });
    </script>

    {!! $js ?? '' !!}
@endpush
