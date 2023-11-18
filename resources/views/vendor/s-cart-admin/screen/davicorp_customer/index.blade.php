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
                        <div class="row">

                            @if (!empty($removeList))
                                <div class="menu-left">
                                    <button type="button" class="btn btn-default grid-select-all"><i
                                                class="far fa-square"></i></button>
                                </div>
                                <div class="menu-left">
                                    <span class="btn btn-flat btn-danger grid-trash" data-perm="{{empty($permGroup)?'':$permGroup.":delete"}}"
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
                                <div class="menu-left" >
                                    <div class="input-group float-right ml-1" style="width: 257px">
                                        <div class="btn-group" style="width: 210px">
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
                                    <th style="width: 5%"></th>
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
                                                   data-id="{{ $keyRow }}" data-status="{{ $tr['status_id'] ?? '' }}" data-customer_kinds="{{ $tr['customer_kind'] ?? '' }}" data-id_names="{{ $tr['id'] ?? '' }}">
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
    @if($is_orderlist ?? 0)
        <!-- Modal print -->
        <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <form class="modal-content" method="post" target="_blank"
                      action="{{ sc_route_admin('admin_order.print') }}" id="printForm">
                    @csrf
                    <input type="hidden" name="ids" id="print_ids" value="{{ $order->id ?? '' }}">
                    <input type="hidden" name="filter" id="print_filter" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i
                                    class="fas fa-print"></i>&nbsp;{{sc_language_render('order.print.title')}}</h5>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label class="col-4">Thông tin in</label>
                            <div class="col-8">
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="type" id="radio_0" type="radio" class="custom-control-input" value="1" checked>
                                    <label for="radio_0" class="custom-control-label">Đơn hàng</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input name="type" id="radio_1" type="radio" class="custom-control-input" value="2">
                                    <label for="radio_1" class="custom-control-label">Ghi chú đơn hàng</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                                    class="fa fa-undo"></i> {{sc_language_render('action.discard')}}</button>
                        <button type="button" id="btnConfirmPrint" class="btn btn-primary"><i
                                    class="fa fa-print"></i> {{sc_language_render('order.print.title')}}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
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
        th {
            white-space: nowrap;
        }
        .lockDatePicker, .multiselect-container {
            z-index: 99998 !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script>
        $(".date_time").datepicker({ dateFormat: "dd/mm/yy" });
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
            $('#loading').hide();
            $(".box-body input[type='checkbox']").iCheck("uncheck");
            $(".far", this).removeClass("fa-check-square").addClass('fa-square');
        })

        // tag a
        $(function () {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 5000; // time in milliseconds
            }
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
                {{-- sweetalert2 --}}
        var selectedRows = function () {
                var selected = [];
                $('.grid-row-checkbox:checked').each(function () {
                    selected.push($(this).data('id'));
                });

                return selected;
            }

        $('#btn-submit-search').click(function () {
            $('#end_to_time').val($('#end_to').val());
            $('#from_to_time').val($('#from_to').val());
        })

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        $('#btn_export').on('click', function () {
            let ids = selectedRows().join();
            const form = $('#form_excel_export');
            const excelIds = $('#excel_ids');
            const excelFilter = $('#excel_filter');
            excelIds.val(ids);
            excelFilter.val(JSON.stringify(convertFormToJSON($('#button_search'))));
            $('#exportDialog').modal();
        });

        $('#btnConfirmExport').on('click', function () {
            let optionValue = $('#form_excel_export').find('input[name="option"]:checked').val();
            if (optionValue == 1) {
                let ids = selectedRows().join();
                if (!ids) {
                    alertMsg('error', 'Lỗi xuất dữ liệu', 'Vui lòng chọn ít nhất một trường dữ liệu');
                    return;
                }
                $('#form_excel_export').submit();
            } else {
                $('#form_excel_export').submit();
            }
        });

        function deleteItem(ids) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn để xoá', 'Vui lòng chọn it nhât 1 bản ghi trước khi xoá đối tượng');
                return;
            }

            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ sc_language_render('action.delete_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: '{{ $method ?? 'post' }}',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    // swalWithBootstrapButtons.fire(
                    //   'Cancelled',
                    //   'Your imaginary file is safe :)',
                    //   'error'
                    // )
                }
            })
        }

        // Clone customer
        function cloneCustomer(id) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ sc_language_render('product.admin.clone_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: '{{ $method ?? 'post' }}',
                            url: '{{ sc_route_admin('admin_customer.clone') }}',
                            data: {
                                id: id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg, '{{ sc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.clone_success') }}', '');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    // swalWithBootstrapButtons.fire(
                    //   'Cancelled',
                    //   'Your imaginary file is safe :)',
                    //   'error'
                    // )
                }
            })
        }// Clone customer

        $('#btn_export_customer').on('click', function () {
            let ids = selectedRows().join();
            let keyword = $("#keyword").val();
            let href = '{{ $urlExportExcel ?? ''}}?keyword=' + keyword + '&ids=' + ids;
            window.location.href = href;
        });

        // Get order id_name by customer_kind to show notication
        function getOrderNameByCustomerKind(customer_kinds, id_names, kind) {
            var order_names = [];
            var result = '';
            for (var key in customer_kinds){
                if(customer_kinds[key] == kind) {
                    order_names.push(id_names[key]);
                }
            }
            for (var k in order_names){
                result += '- ' + order_names[k]  + '<br>';
            }
            return result;
        }

    </script>

    {!! $js ?? '' !!}
@endpush
