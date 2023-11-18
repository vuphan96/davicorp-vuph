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


    <div class="modal fade" id="exportWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  export-modal-container" role="document" >
            <form class="modal-content form-export-warehouse" method="post"
                  action="javascript:void(0)"
                  multiple="" style="height:700px;">
                <?php echo csrf_field(); ?>
                <div style="padding: 0 10px; box-sizing: border-box">
                    <div class="modal-header row" style="padding: 1rem">
                        <div class="col-md-4 col-sm-5">
                            <h5 class="modal-title" id="exampleModalLongTitle">Xuất kho từ báo cáo</h5>
                        </div>
                        <div class="col-md-4 col-sm-3 pb-2">
                            <select class="form-control select2" id="select_customer" style="width: 100%">
                                <option selected value=""> Chọn khách hàng</option>

                            </select>
                        </div>
                        <div class="col-md-4 col-sm-4 text-right">
                            <div class="input-group">
                                <input type="text" name="keySearchModal" id="keySearchModal" class="form-control rounded-0 float-right" placeholder="Tên, mã sản phẩm" value="">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary  btn-flat" id="submit_modal_search"><i class="fas fa-search"></i></button>
                                </div>
                                <button type="button" class="close close-modal" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" style="overflow-y: scroll; max-height:700px;">
                    <h5 class="modal-title">Vui lòng chọn kho cần xuất: </h5>
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <select class="form-control select2" id="select_warehouse" style="width: 90%">
                                <option value="">-- Chọn kho --</option>
                            </select>
                        </div>
                    </div>
                    <br/>
                    <div class="form-group row">
                        <table class="col-12 table table-hover box-body text-wrap table-bordered list_table" >
                            <thead>
                                <tr>
                                    <th class="text-center">
                                        <div class="menu-left">
                                            <button type="button" class="btn btn-default grid-select-all" id="grid-select-all"><i class="far fa-square"></i></button>
                                        </div>
                                    </th>
                                    <th class="text-center">Mã SP</th>
                                    <th class="text-center">Tên sản phẩm</th>
                                    <th class="text-center">Mã ĐH</th>
                                    <th class="text-center">Tên khách hàng</th>
                                    <th class="text-center" style="width: 100px">Số lượng xuất</th>
                                    <th class="text-center" style="width: 130px">Số lượng xuất thực tế</th>
                                    <th class="text-center" style="width: 130px">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody id="data_target">
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Thoát</button>
                    <button type="submit" class="btn btn-primary btn-create-export_order">Xuất</button>
                </div>
            </form>
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

        #key_export {
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
        .export-modal-container {
            width: 100%;
            max-width: 1300px;
        }
        @media (max-width: 992px) {
            .export-modal-container {
                max-width: 900px;
            }
        }
        @media (max-width: 768px) {
            .export-modal-container {
                max-width: 600px;
            }
            .close-modal {
                display: none;
            }
        }
        .list_order_target td{
            vertical-align: middle;
        }
        .list_order_target td p{
            margin-bottom: 0px !important;
        }
        input[type='number'] {
            -moz-appearance:textfield;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
        }
        #loading{
            z-index: 10000 !important;
        }
    </style>
    <link rel="stylesheet" type="text/css"
          href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>
@endpush

@push('scripts')
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script type="text/javascript">
        var arrKeyExport = <?php echo json_encode(sc_clean(request('key_export') ?? []), 15, 512) ?>;
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
            document.title = "BaoCaoBanHang2ChiTieu-" + startDate + " - " + endDate;
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
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })
            $('#key_export').multiselect({
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
        var dataExportModal;
        var selectedRows = function () {
            var selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });

            return selected;
        }

        $('#submit_report_target').click(function () {
            $('#key_search').val('searched');
        })

        function savePdf() {
            let data_count = $('#data_count').val();
            if (data_count > 9999) {
                alertMsg("error", "Lỗi xuất dữ liệu", "Dữ liệu quá tải !");
                return;
            }
            let keyword = $("#keyword").val();
            let category = $("#category option:selected").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $('#key_export').val();
            let key_search = $('#key_search').val();
            let key_zone = $('#key_zone').val();
            let key_department = $('#key_department').val();
            let key_request_search = '{{ request('key_search') }}';
            if (key_search != 'searched' && key_request_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            let href = '{{ sc_route_admin('admin_report_2target.export_pdf') }}?keyword=' + keyword + '&category=' + category + '&from_to=' + from_to + '&end_to=' + end_to + '&key_export=' + key_export + '&key_search=' + key_search + '&key_zone=' + key_zone + '&key_department=' + key_department;
            $("#loading").show();
            printPage(href);
            // window.location.href = href;
        }

        $('#button_export_filter').on('click', function () {
            let data_count = $('#data_count').val();
            let keyword = $("#keyword").val();
            let category = $("#category option:selected").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $('#key_export').val();
            let key_search = $('#key_search').val();
            let key_zone = $('#key_zone').val();
            let key_department = $('#key_department').val();
            let key_request_search = '{{ request('key_search') }}';
            if (key_search != 'searched' && key_request_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }

            if (data_count < 20000) {
                let href = '{{ sc_route_admin('admin_report_2target.export_excel') }}?keyword=' + keyword + '&category=' + category + '&from_to=' + from_to + '&end_to=' + end_to + '&key_export=' + key_export + '&key_search=' + key_search + '&key_zone=' + key_zone + '&key_department=' + key_department;
                window.location.href = href;
            } else {
                $("#loading").show();
                $.ajax({
                    method: 'post',
                    url: '{{ sc_route_admin('admin_report_2target.get_and_chunk_data') ?? '' }}',
                    data: {
                        keyword: keyword,
                        category: category,
                        from_to: from_to,
                        end_to: end_to,
                        key_export: key_export,
                        key_search: key_search,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if (response.error === 1) {
                            $("#loading").hide();
                            return alertMsg("error", response.msg);
                        }
                        let href = '{{ sc_route_admin('admin_report_2target.save_file_to_storage') }}?key=0&max_item='+response;
                        window.open(href);
                        for (let i = 1; i < response; i++) {
                            let time = 3000 + i*2000;
                            setTimeout(function () {
                                let href = '{{ sc_route_admin('admin_report_2target.save_file_to_storage') }}?key='+ i +'&max_item='+response;
                                window.open(href);
                            }, time)
                        }
                        $("#loading").hide();
                    }
                });

            }
        });

        var CustomerByProduct = [] ;
        function getListExportModal() {
            let data_count = $('#data_count').val();
            let keyword = $("#keyword").val();
            let category = $("#category option:selected").val();
            let from_to = $("#from_to").val();
            let end_to = $("#end_to").val();
            let key_export = $('#key_export').val();
            let key_search = $('#key_search').val();
            let key_zone = $('#key_zone').val();
            let key_department = $('#key_department').val();
            let key_request_search = '{{ request('key_search') }}';
            if (key_search != 'searched' && key_request_search == '') {
                alertMsg("error", "Lỗi xuất dữ liệu", "Vui lòng lọc dữ liệu trước khi xuất");
                return;
            }
            if(from_to && end_to){
                if(from_to !== end_to) {
                    alertMsg("error", "Vui lòng chỉ chọn xuất dữ liệu trong ngày");
                    return;
                }
            }
            $("#loading").show();
            if(dataExportModal==undefined) {
                $.ajax({
                    method: 'post',
                    url: '{{ sc_route_admin('admin_report_2target.get_list_data_modal') ?? '' }}',
                    data: {
                        keyword: keyword,
                        category: category,
                        from_to: from_to,
                        end_to: end_to,
                        key_export: key_export,
                        key_zone: key_zone,
                        key_department: key_department,
                        key_search: key_search,
                        _token: '{{ csrf_token() }}',
                    },
                    success: function (response) {
                        if (response.error === 1) {
                            $("#loading").hide();
                            return alertMsg("error", response.msg);
                        }
                        dataExportModal = response.data;
                        showModalExport(dataExportModal)
                    }
                });
            } else {
                $('#exportWarehouseModal').modal('show')
                $("#loading").hide();
                // showModalExport(dataExportModal)
            }


        }
        $('#submit_modal_search').on('click', function () {
            $('.list-order').hide();
            let customer_code = $('#select_customer').val();
            let keySearchModal = $('#keySearchModal').val();
            var arrKeyExport = CustomerByProduct;
            if(customer_code){
                arrKeyExport = arrKeyExport.filter(v => v.customer_code === customer_code);
            }
            if(keySearchModal){
                arrKeyExport = arrKeyExport.filter(item => String(remove_accents(item.product_name)).toLowerCase().includes(String(remove_accents(keySearchModal)).toLowerCase()) || String(remove_accents(item.product_code)).toLowerCase().includes(String(remove_accents(keySearchModal)).toLowerCase()));
            }
            $.each(arrKeyExport, function(key,val) {
                $('.' + val.product_id).show();
            });

        })
        function showModalExport(dataExport) {
            $('#exportWarehouseModal').modal('show')
            let customer = dataExport.dataCustomer;
            let warehouse = dataExport.dataWarehouse;
            let exportTmp = dataExport.dataExportTmp;
            let optionCustomerHtml = '';
            let optionWarehouseHtml = '';
            let dataExportHtml = '';
            if (customer) {
                $.each( customer, function( key, value ) {
                    optionCustomerHtml += '<option value="' + value.customer_code + '">' + value.customer_name + '</option>'

                });
                $('#select_customer').append(optionCustomerHtml);
            }
            if (warehouse) {
                $.each( warehouse, function( key, value ) {
                    optionWarehouseHtml += '<option value="' + value.id + '">' + value.name + '</option>'
                });
                $('#select_warehouse').append(optionWarehouseHtml);
            }
            if (exportTmp) {
                $.each( exportTmp, function( index, value ) {
                    dataExportHtml += '<tr class="list-order '+ value.product_id +'">';
                    dataExportHtml += '<td>';
                    dataExportHtml += '<div>';
                    dataExportHtml += '<input type="checkbox" name="order_checkbox" id="order_checkbox-'+ value.product_id +'" data-id="'+ value.product_id +'" class="checkbox grid-row-checkbox" value="'+ value.product_id +'">';
                    dataExportHtml += '</div>';
                    dataExportHtml += '</td>';
                    dataExportHtml += '<input type="hidden" id="order_product_unit-'+ value.product_id +'" value="'+ (value.product_unit ?? '') +'"/>';
                    dataExportHtml += '<td>';
                    dataExportHtml += '<p id="order_product_code-'+ value.product_id +'">'+ (value.product_code  ?? '') +'</p>';
                    dataExportHtml += '</td>';
                    dataExportHtml += '<td>';
                    dataExportHtml += '<p id="order_product_name-'+ value.product_id +'">'+ (value.product_name  ?? '') +'</p>';
                    dataExportHtml += '</td>';
                    dataExportHtml += '<td></td><td></td>';
                    dataExportHtml += '<td>';
                    dataExportHtml += '<div>';
                    dataExportHtml += '<p id="order_qty-'+ value.product_id +'" class="text-right">'+ (value.qty  ?? '') +'</p>';
                    dataExportHtml += '</div>';
                    dataExportHtml += '<td>';
                    dataExportHtml += '<div>';
                    dataExportHtml += '<p class="text-right" id="order_qty_reality-'+ value.product_id +'" style="font-weight: bold">'+ (value.qty  ?? '') +'</p>';
                    dataExportHtml += '<div>';
                    dataExportHtml += '</td>';
                    dataExportHtml += '<td></td>';
                    dataExportHtml += '</tr>';
                    if(value.customer){
                        $.each( value.customer, function( key, item ) {
                            CustomerByProduct.push({
                                'customer_code':item.customer_code,
                                'product_id': item.product_id,
                                'product_code':item.product_code,
                                'product_name': item.product_name
                            })
                            dataExportHtml += '<tr class="list-order '+ item.product_id +'" id="product_'+ value.product_id +'">';
                            dataExportHtml += '<td></td><td></td><td></td>';
                            dataExportHtml += '<td>';
                            dataExportHtml += '<div>';
                            dataExportHtml += '<p class="order_code-'+ item.product_id +'">'+ (item.order_code ?? '') +'</p>';
                            dataExportHtml += '</div>';
                            dataExportHtml += '</td>';
                            dataExportHtml += '<td>';
                            dataExportHtml += '<p class="order_customer_name-'+ item.product_id +'">'+ (item.customer_name ?? '') +'</p>';
                            dataExportHtml += '</td>';
                            dataExportHtml += '<input type="hidden" name="order_customer_code"  class="form-control text-right order_customer_code-'+ item.product_id +'" value="'+ (item.customer_code ?? '')  +'">';
                            dataExportHtml += '<input type="hidden" name="order_id"  class="form-control text-right order_id-'+ item.product_id +'" value="'+ (item.order_id ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_object_id"  class="form-control text-right order_object_id-{'+ item.product_id +'" value="'+ (item.object_id ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_delivery_date"  class="form-control text-right order_delivery_date-'+ item.product_id +'" value="'+ (item.delivery_date ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_explain"  class="form-control text-right order_explain-'+ item.product_id +'" value="'+ (item.explain ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_department_id"  class="form-control text-right order_department_id-'+ item.product_id +'" value="'+ (item.department_id ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_zone_id"  class="form-control text-right order_zone_id-'+ item.product_id +'" value="'+ (item.zone_id ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_category_id"  class="form-control text-right order_category_id-'+ item.product_id +'" value="'+ (item.category_id ?? '')+'">';
                            dataExportHtml += '<input type="hidden" name="order_product_kind"  class="form-control text-right order_product_kind-'+ item.product_id +'" value="'+ (item.product_kind ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_detail_id"  class="form-control text-right order_detail_id-'+ item.product_id +'" value="'+ (item.order_detail_id ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_customer_short_name"  class="form-control text-right order_customer_short_name-'+ item.product_id +'" value="'+ (item.customer_short_name ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="order_id_barcode"  class="form-control text-right order_id_barcode-'+ item.product_id +'" value="'+ (item.id_barcode ?? '') +'">';
                            dataExportHtml += '<input type="hidden" name="customer_num"  class="form-control text-right customer_num-'+ item.product_id +'" value="'+ (item.customer_num ?? '') +'">';
                            dataExportHtml += '<td>';
                            dataExportHtml += '<div class="input-group">';
                            dataExportHtml += '<input type="text" name="order_qty"  class="form-control text-right order_qty-'+ item.product_id +'" value="'+ (item.qty ?? 0) +'" disabled>';
                            dataExportHtml += '</div></td>';
                            dataExportHtml += '<td>';
                            dataExportHtml += '<div class="input-group">';
                            dataExportHtml += '<input type="text" name="order_qty_reality" onkeyup="changeAmountQty(\''+item.product_id+'\')"  class="form-control text-right order_qty_reality-'+ item.product_id +'" value="'+(item.qty ?? 0)   +'">';
                            dataExportHtml += '</div>';
                            dataExportHtml += '</td>';
                            dataExportHtml += '<td>';
                            dataExportHtml += '<div class="input-group">';
                            dataExportHtml += '<input type="text" name="order_note"  class="form-control order_note-'+ item.product_id +'" value="'+ (item.note ?? '') +'">';
                            dataExportHtml += '</div>';
                            dataExportHtml += '</td>';
                            dataExportHtml += '</tr>';
                        });
                    }
                });
                $('#data_target').append(dataExportHtml);
            }
            $("#loading").hide();
        }
        function changeAmountQty(product_id) {
            let amount = 0;
            var value = $('.order_qty_reality-' + product_id)
            for(var i = 0; i < value.length; i++){
                let qty = $(value[i]).val()
                amount += Number(qty);
            }
            $('#order_qty_reality-'+product_id).text(amount)
        }
        $('.btn-create-export_order').on('click', function () {
            let ids = selectedRows().join();
            if(ids == '') {
                alertMsg("error", "Vui lòng ấn chọn dữ liệu trước khi xuất !");
                return;
            }
            let arrayId = ids.split(',')
            let dataSendExport = [];
            let warehouse = $('#select_warehouse').val();
            if (!warehouse) {
                alertMsg("error", "Vui lòng chọn kho cần xuất !");
                return;
            }
            for(let i = 0; i<arrayId.length; i++){
                let product_code = $('#order_product_code-' + arrayId[i]).text();
                let product_name = $('#order_product_name-' + arrayId[i]).text();
                let product_unit = $('#order_product_unit-' + arrayId[i]).val();
                let list_order_code = $('.order_code-' + arrayId[i]);
                let list_order_id = $('.order_id-' + arrayId[i]);
                let list_order_object_id = $('.order_object_id-' + arrayId[i]);
                let list_order_delivery_date = $('.order_delivery_date-' + arrayId[i]);
                let list_order_explain = $('.order_explain-' + arrayId[i]);
                let list_order_department_id = $('.order_department_id-' + arrayId[i]);
                let list_order_zone_id = $('.order_zone_id-' + arrayId[i]);
                let list_order_category_id = $('.order_category_id-' + arrayId[i]);
                let list_order_product_kind = $('.order_product_kind-' + arrayId[i]);
                let list_order_detail_id = $('.order_detail_id-' + arrayId[i]);
                let list_order_id_barcode = $('.order_id_barcode-' + arrayId[i]);
                let list_order_customer_short_name = $('.order_customer_short_name-' + arrayId[i]);
                let list_customer_num = $('.customer_num-' + arrayId[i]);
                let list_customer_name = $('.order_customer_name-' + arrayId[i]);
                let list_customer_code = $('.order_customer_code-' + arrayId[i]);
                let list_qty = $('.order_qty-' + arrayId[i]);
                let list_qty_reality = $('.order_qty_reality-' + arrayId[i]);
                let list_note = $('.order_note-' + arrayId[i]);
                if(list_order_code.length > 0){
                    for(let j =0 ; j< list_order_code.length ; j++) {
                        let rowData = {
                            'product_id': arrayId[i],
                            'product_name': product_name,
                            'product_code': product_code,
                            'product_unit': product_unit,
                            'order_id': $(list_order_id[j]).val(),
                            'order_id_name': $(list_order_code[j]).text(),
                            'order_object_id': $(list_order_object_id[j]).val() ?? '',
                            'order_delivery_date': $(list_order_delivery_date[j]).val(),
                            'department_id': $(list_order_department_id[j]).val(),
                            'zone_id': $(list_order_zone_id[j]).val(),
                            'category_id': $(list_order_category_id[j]).val(),
                            'product_kind': $(list_order_product_kind[j]).val(),
                            'order_detail_id': $(list_order_detail_id[j]).val(),
                            'order_id_barcode': $(list_order_id_barcode[j]).val(),
                            'order_customer_short_name': $(list_order_customer_short_name[j]).val(),
                            'customer_num': $(list_customer_num[j]).val(),
                            'order_explain': $(list_order_explain[j]).val(),
                            'customer_name': $(list_customer_name[j]).text(),
                            'customer_code': $(list_customer_code[j]).val(),
                            'qty': $(list_qty[j]).val(),
                            'qty_reality': $(list_qty_reality[j]).val(),
                            'note': $(list_note[j]).val(),
                        };
                        dataSendExport.push(rowData);
                    }
                }
            }
            var dataInsert = JSON.stringify(dataSendExport);
            $.ajax({
                url: '{{ sc_route_admin('admin_report_2target.create_order_export_all_item') }}',
                type: 'post',
                data: {
                    data_export: dataInsert,
                    warehouse: warehouse,
                    _token: '{{ csrf_token() }}',
                },

                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.success) {
                        alertMsg('success', "Xuất kho thành công !");
                        location.reload();
                    } else {
                        alertJs('error','Lỗi lưu dữ liệu: ' + response.message);
                    }
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        })
    </script>
@endpush
