@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-12 card">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools" style="float: none">
                        @if (!empty($topMenuRight) && count($topMenuRight))
                            @foreach ($topMenuRight as $item)
                                <div>
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
                                    <button type="button" class="btn btn-sm btn-default grid-select-all"><i
                                                class="far fa-square"></i></button>
                                </div>
                                <div class="menu-left">
                                    <span class="btn btn-sm btn-flat btn-danger grid-trash" data-perm="{{empty($permGroup)?'':$permGroup.":delete"}}"
                                          title="{{ sc_language_render('action.delete') }}"><i class="fas fa-trash-alt"></i></span>
                                </div>
                            @endif

                            @if (!empty($buttonRefresh))
                                <div class="menu-left">
                                    <span class="btn btn-flat btn-sm btn-primary grid-refresh"
                                          title="{{ sc_language_render('action.refresh') }}"><i class="fas fa-sync-alt"></i></span>
                                </div>
                            @endif

                            @if (!empty($buttonSort))
                                <div class="menu-left" >
                                    <div class="input-group float-right" style="width: 245px;">
                                        <div class="btn-group" style="width: 185px; height: 31px">
                                            <select class="form-control btn-sm rounded-0 float-right" id="order_sort" style="max-height: 31px">
                                                {!! $optionSort ?? '' !!}
                                            </select>
                                        </div>
                                        <div class="input-group-append">
                                            <button id="button_sort" type="submit" class="btn btn-sm btn-primary"><i
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
                                <th style="width: 5%"> <button type="button" class="btn btn-sm btn-default grid-select-all"><i
                                                class="far fa-square"></i></button></th>
                                @foreach ($listTh as $key => $th)
                                    <th style=" {{ $cssTh[$key] ?? '' }}">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dataTr as $keyRow => $tr)
                                <tr>
                                    <td style="padding-left: 12px; text-align: center">
                                        <input class="checkbox grid-row-checkbox" type="checkbox" data-id="{{ $keyRow }}">
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
    <div class="modal fade" id="changeDriveFormDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content" method="post" id="changeDriveForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i
                                class="fas fa-print"></i>&nbsp;Đổi NV giao hàng</h5>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-4">Chọn nhân viên giao hàng:</label>
                        <div class="col-5">
                            <select class="form-control rounded-0" name="change_drive" id="change_drive">
                                @foreach($drive as $d)
                                    <option value="{{ $d->id }}">{{ $d->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal"><i
                                class="fa fa-undo"></i> Thoát</button>
                    <button type="button" id="btnConfirmPrint1" onclick="submitDataChangeDrive();" class="btn btn-primary"><i
                                class="fa fa-print"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    {!! $css ?? '' !!}
    <link rel="stylesheet" type="text/css" href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
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
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #f1f1f1;
            min-width: 180px;
            overflow: auto;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 0px 0 10px 3px;
            text-decoration: none;
        }

        .dropdown a:hover {background-color: #ddd;}

        .show {display: block;}
        .btn-create-order {
            width: 100%;
            height: 35px;
            text-align: left;
            padding-left: 16px !important;
            padding-top: 5px !important;
        }
        .btn-create-order:hover {
            background-color: #Dfeaee !important;
            border-radius: 5px 5px 5px 5px;
            color: #2596be !important;
        }
        #create-order-dropdown {
            border-radius: 12px 12px 12px 12px;
            background-color: #fff;
            margin-top: 1px;
            z-index: 4;
        }
        .dropdown:hover .dropdown-content {
            display: block !important;
        }

        .status-fast {
            color: #3c8dbc;
            font-size: 14px;
        }
    </style>
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
            // $.pjax.submit(event, '#pjax-container')
            $('#loading').show();
        })

        $('a.page-link').on('click', function () {
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

        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 5000; // time in milliseconds
            }
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })
            $('#customer_filter').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                filterPlaceholder: 'Tìm theo khách hàng',
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                enableCaseInsensitiveFiltering: true,
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

        $('#select_limit_paginate').on('change', function () {
            let limit = $(this).val();
            $('#limit_paginate').val(limit);
            $('#button_search').submit()
        })

        $('#btn_chang_drive').click(function () {
            let ids = selectedRows().join();
            if(!ids){
                alertMsg("error", "Lỗi", "Vui lòng chọn ít nhất một bản ghi!");
                return;
            }
            $('#changeDriveFormDialog').modal();
        })

        function submitDataChangeDrive() {
            let ids = selectedRows().join();
            let drive_id = $('#change_drive').val();
            $.ajax({
                method: 'post',
                url: '{{ sc_route_admin('driver.change_drive_order_davicook') }}',
                data: {
                    ids: ids,
                    drive_id: drive_id,
                    _token: '{{ csrf_token() }}',
                },
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', "Lỗi", data.msg);
                        $.pjax.reload('#pjax-container');
                    } else {
                        alertMsg('success', data.msg);
                        location.reload();
                    }
                }
            });
        }
    </script>

    {!! $js ?? '' !!}
@endpush
