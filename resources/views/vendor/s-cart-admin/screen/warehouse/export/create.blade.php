@extends($templatePathAdmin.'layout')

@section('main')
{{--    {{dd($product)}}--}}
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title !!}</h3>
                </div>
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('warehouse_name') ? ' text-red' : '' }}">
                            <label for="warehouse_name" class="col-sm-2 col-form-label">Chọn kho&nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-10 col-md-8 col-lg-6 ">
                                <div class="input-group mb-3">
                                    <select onchange="selectWarehouse()" id="warehouse_name" name="warehouse_name" class="select2 form-control warehouse_name {{ $errors->has('warehouse_name') ? ' is-invalid' : '' }}" required>
                                        <option value="">-- Chọn kho --</option>
                                        @foreach ($list_warehouse as $warehouse)
                                            <option value="{{$warehouse->id}}">{!!$warehouse->name!!}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if ($errors->has('warehouse_name'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('warehouse_name') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        {{-- Customer --}}
                        <div class="form-group row {{ $errors->has('customer_id') ? ' has-error' : '' }}">
                            <label for="customer_id" class="col-sm-2 col-form-label">Xuất kho cho<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-10 col-md-8 col-lg-6 ">
                                <div class="input-group mb-3">
                                    <select required class="form-control select2" id="customer_id" name="customer_id" onchange="getAddress()">
                                        <option value="">Lý do khác</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}">{!!$customer->name!!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('customer_id'))
                                    <span class="help-block">
                                            {{ $errors->first('customer_id') }}
                                        </span>
                                @endif
                            </div>
                        </div>
                        {{-- /.Customer --}}

                        <div class="form-group row {{ $errors->has('address') ? ' text-red' : '' }}">
                            <label for="" class="col-sm-2 col-form-label">Địa chỉ</label>
                            <div class="col-sm-10 col-md-8 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="address" name="address" placeholder="Địa chỉ" disabled
                                           value="{{ old() ? old('address') : '' }}"
                                           class="form-control" autocomplete="address">
                                </div>

                                @if ($errors->has('address'))
                                    <span class="text-sm text-red">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('address') }}
                              </span>
                                @endif
                            </div>
                        </div>
                        {{-- ngày xuất--}}
                        <div class="form-group row {{ $errors->has('date_export') ? ' text-red' : '' }}">
                            <label for="" class="col-sm-2 col-form-label">Ngày xuất kho</label>
                            <div class="col-sm-10 col-md-8 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" name="date_export" id="date_export" class="form-control input-sm date_time rounded-0"  placeholder="Chọn ngày" value=""/>

                                </div>

                                @if ($errors->has('date_export'))
                                    <span class="text-sm text-red">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('date_export') }}
                              </span>
                                @endif
                            </div>
                        </div>
                        {{-- Ghi chú--}}
                        <div class="form-group row {{ $errors->has('note') ? ' text-red' : '' }}">
                            <label for="" class="col-sm-2 col-form-label">Ghi chú</label>
                            <div class="col-sm-10 col-md-8 col-lg-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="note" name="note" placeholder="Ghi chú"
                                           value="{{ old() ? old('note') : '' }}"
                                           class="form-control">
                                </div>

                                @if ($errors->has('note'))
                                    <span class="text-sm text-red">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('note') }}
                              </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card collapsed-card">
                <div class="table-responsive">
                    <form id="form-add-item-product">
                        @csrf
                        <table id="table-product" class="table box-body text-wrap table-bordered table-product">
                            <thead>
                            <tr>

                                @if (!empty($removeList))
                                    <th style="width: 5%"></th>
                                @endif
                                @foreach ($listTh as $key => $th)
                                    <th style=" {!! $cssTh[$key] ?? '' !!} ">{!! $th !!}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody id="tableData">
                                @foreach ($dataTr as $keyRow => $tr)
                                    <tr class="{{ (request('id') == $keyRow) ? 'active': '' }}">
                                        @if (!empty($removeList))
                                            <td style="text-align: center; width: 1%; white-space:nowrap;">
                                                <input class="checkbox grid-row-checkbox" type="checkbox"
                                                       data-id="{{ $keyRow }}">
                                            </td>
                                        @endif
                                        @foreach ($tr as $key => $trtd)
                                            <td>{!! $trtd !!}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                            <tr id="form_import" class="not-print">
                                <td colspan="11">
                                    <button type="button" class="btn btn-flat btn-success"
                                            id="add-item-button"
                                            title="{{sc_language_render('action.add') }}"><i
                                                class="fa fa-plus"></i> {{ sc_language_render('action.add') }}
                                    </button>

                                    <button style="margin-right: 50px;" type="button"
                                            class="btn btn-flat btn-warning" id="btnSave" title="Save"><i
                                                class="fa fa-save"></i> Lưu lại
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection


@push('styles')
    <style type="text/css">
        td selection {
            border-radius: 5px!important;
        }
              /* Chrome, Safari, Edge, Opera */
          input::-webkit-outer-spin-button,
          input::-webkit-inner-spin-button {
              -webkit-appearance: none;
              margin: 0;
          }

        .color-red {
            color: red !important;
        }

        /* Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }

        input { width: 400px; }
        label, input { display: block; }
        label { font-weight: bold; }
        input, .flexselect_dropdown li { font-size: 1rem; }
        small { color: #999; }
        .flexselect_selected small { color: #ddd; }
    {{-- flexselect --}}
    </style>
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/flexselect.css') }}" type="text/css" media="screen" />
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>

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
        });

    </script>
    <script type="text/javascript">
        {{-- sweetalert2 --}}
        var selectedRows = function () {
            let selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });
            return selected;
        }

        $('.grid-trash').on('click', function () {
            let ids = selectedRows().join();
            deleteItem(ids);
        });

        function deleteItem(ids) {
            if (ids === "") {
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
                            method: 'delete',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    window.location.replace('{{ sc_route_admin('admin_zone.index') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })
        }
        var rowIdxArr = [];
        var currentRowIdx = 0;
        var htmlOption = '';
        var productList = <?php echo json_encode($dataProduct ?? '[]', 15, 512) ?>;
        var customerList = <?php echo json_encode($customers ?? '[]', 15, 512) ?>;

        if (Array.isArray(productList)) {
            productList.forEach(function (p) {
                p.unit_name = p.unit?.name ?? ''
                htmlOption += '<option value="' + p.id + '">' + p.name +'</option>'
            });
        } else {
            console.error('productList is not an array:', productList);
        }
        {{--/ sweetalert2 --}}

        $('#add-item-button').click(function () {
            let warehouse   = $('#warehouse_name').val();
            if(!warehouse) {
                alertJs('error','Vui lòng chọn kho trước khi xuất đơn!');
                return;
            }
            currentRowIdx++;
            var html = '';
            if(productList){
                html +=  '<tr id="dataRow-' + currentRowIdx + '" class="select-product">';
                html +=  '<td id="productIdx-' + currentRowIdx + '" class="check-num-order" style="text-align:center">' + currentRowIdx + '</td>';
                html +=  '<td><input type="text" id="productSku-' + currentRowIdx + '" readonly class="add_sku form-control" value=""></td>';
                html +=  '<input type="hidden" id="productKind-' + currentRowIdx + '" readonly class="add_product_kind form-control" value="">';
                html +=  '<input type="hidden" id="categoryId-' + currentRowIdx + '" readonly class="add_category_id form-control" value="">';
                html +=  '<td id="add_td">';
                html +=  '<select onchange="selectProduct(' + currentRowIdx + ')" name="product_id[]" id="productName-' + currentRowIdx + '"  class="add_id form-control flexselect" tabindex="2">';
                html +=  '<option value="" selected></option>';
                html +=   htmlOption;
                html +=   '</select>';
                html +=  '</td>';
                html +=  '<td class="pro"><input onkeyup="checkQtyWarehouse(' + currentRowIdx + ')" type="text" id="qty-' + currentRowIdx + '" class="form-control"  tabindex="-1" autocomplete="off" value=""></td>';
                html +=  '<td class="pro"><input type="text" id="unit-' + currentRowIdx + '"  readonly class="form-control"  tabindex="-1"  value=""></td>';
                html +=  '<td class="pro"><input type="text" id="note-' + currentRowIdx + '"   class="form-control"  value=""></td>';
                html += '<td><button class="btn btn-danger" id="actionDelete-' + currentRowIdx + '"  onclick="deleteRow(' + currentRowIdx + ')"  tabindex="-1"><i class="fa fa-times" aria-hidden="true"></i></button></td>';
                html += '</tr>'
            }

            $("#tableData").append(html);
            rowIdxArr.push(currentRowIdx);
            reIndexRowNumber();

            $("select.flexselect").flexselect({ hideDropdownOnEmptyInput: true });
        });

        function deleteRow(rowIdx) {
            $('#dataRow-' + rowIdx).remove();
            var idx = rowIdxArr.indexOf(rowIdx);
            if (idx !== -1) {
                rowIdxArr.splice(idx, 1);
            }

            reIndexRowNumber();
        }
        function reIndexRowNumber() {

            for (let i = 0; i < rowIdxArr.length; i ++) {
                $('#productIdx-' + rowIdxArr[i]).text(i + 1)
            }
        }
        function selectProduct(rowId) {
            var id = $("#productName-" + rowId).val();
            var productItem = productList.find(item => item.id === id);
            $("#productSku-" + rowId).val(productItem.sku);
            $("#productKind-" + rowId).val(productItem.kind);
            $("#categoryId-" + rowId).val(productItem.category_id);
            $("#unit-" + rowId).val(productItem.unit_name);
            checkQtyWarehouse(rowId)
        }
        function checkNumberOrder() {
            var selected = [];
            $('.check-num-order').each(function (key) {
                $(this).text(key + 1)

            });

            return selected;
        }

        $('#btnSave').click(function () {
            var form_data = $("#form-add-item-product").serialize();
            var dataToSave = [];
            var warehouse   = $('#warehouse_name').val();
            var customer_id = $("#customer_id").val() ?? '';
            var address     = $("#address").val();
            var date_export = $("#date_export").val();
            var note        = $('#note').val();
            for (var i = 0; i < rowIdxArr.length; i++) {
                var rowIdx = rowIdxArr[i];
                var productSku = $('#productSku-' + rowIdx).val();
                var productId = $('#productName-' + rowIdx).val();
                var productKind = $('#productKind-' + rowIdx).val();
                var categoryId = $('#categoryId-' + rowIdx).val();
                var productName = productList.find(item => item.id === productId);
                var qty = $('#qty-' + rowIdx).val();
                var unit = $('#unit-' + rowIdx).val();
                var comment = $('#note-' + rowIdx).val();
                if (productName && qty) {
                    var rowData = {
                        'productSku': productSku,
                        'productId': productId,
                        'productName': productName.name,
                        'productKind': productKind,
                        'categoryId': categoryId,
                        'qty': qty,
                        'unit': unit,
                        'note': comment
                    };
                    dataToSave.push(rowData);
                } else {
                    alertJs('error','Vui lòng chọn đủ thông tin sản phẩm trước khi xuất đơn!');
                    return;
                }
            }
            $.ajax({
                url: '{{ sc_route_admin('admin_warehouse_export.create') }}',
                type: 'post',
                data: {
                    data_save:dataToSave,
                    warehouse:warehouse,
                    customer_id:customer_id,
                    address:address,
                    date_export:date_export,
                    note:note,
                    _token: '{{ csrf_token() }}',
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.success) {
                        alertJs('success', "Đơn xuất kho đã được tạo !");
                        window.location.replace('{{ sc_route_admin('admin_warehouse_export.index') }}');
                    } else {
                        alertJs('error','Lỗi lưu dữ liệu: ' + response.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alertJs('error','Lỗi dữ liệu khi nhập vui lòng kiểm tra!');
                    console.error('Status:', status);
                    console.error('Error:', error);
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        })
        function getAddress() {
            var customer_id = $('#customer_id').val();
            if(customer_id) {
                var customer_addr = customerList.find(item => item.id === customer_id).address;
                $('#address').val(customer_addr);
            } else {
                $('#address').val('');
            }

        }
        var dataWarehouse = []
        function selectWarehouse() {
            var dataProductWarehouse = <?php echo json_encode($dataProductWarehouse ?? '[]', 15, 512) ?>;
            let id_warehouse = $('#warehouse_name').val();
            if(id_warehouse) {
                dataProductWarehouse = dataProductWarehouse.filter(item => item.warehouse_id == id_warehouse)
                dataWarehouse = dataProductWarehouse;
            }
        }
        function checkQtyWarehouse(rowId) {
            let qtyInput = $('#qty-' + rowId).val();
            let id_product = $("#productName-" + rowId).val();

            if(qtyInput && id_product) {
                let dataWarehouseByProduct = dataWarehouse.find(item => item.product_id == id_product);
                if(dataWarehouseByProduct) {
                    let qty = dataWarehouseByProduct.qty;
                    if (qtyInput > qty) {
                        alertJs('warning','Cảnh báo: Số lượng sản phẩm lớn hơn số lượng tồn!');
                        return;
                    }
                }
            }

        }
    </script>
@endpush
