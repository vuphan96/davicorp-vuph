@extends($templatePathAdmin.'layout')

@section('main')
    @php
        $id = empty($id) ? 0 : $id;
    @endphp
    <div class="row">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {{--        {{dd($product)}}--}}
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description ?? '' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float-right mr_5">
                            <a href="{{ sc_route_admin('order_import.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main">
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('warehouse') ? ' text-red' : '' }}">
                            <label for="warehouse" class="col-sm-1 col-form-label">Chọn kho&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-6">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <select id="warehouse" name="warehouse_name"
                                            class="form-control warehouse {{ $errors->has('warehouse') ? ' is-invalid' : '' }}"
                                            required>
                                        <option value="">-- Chọn kho --</option>
                                        @foreach ($warehouseList as $warehouse)
                                            <option value="{{ $warehouse->id }}" <?php echo((isset($warehouseId) && $warehouse->id == $warehouseId) ? 'selected' : '') ?> >{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('warehouse'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('warehouse') }}
                                    </span>
                                @endif
                            </div>
                            <label for="description"
                                   class="col-sm-1 col-form-label">Ngày giao hàng
                            </label>
                            <div class="col-sm-4 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;"
                                           autocomplete="off"
                                           name="delivery_date"
                                           id="delivery_date"
                                           value="<?php echo(isset($deliveryDate) ? $deliveryDate : '') ?>"
                                           onfocus="this.oldvalue = this.value"
                                           class="form-control input-sm delivery_time date_time"
                                           data-date-format="dd/mm/yyyy"
                                           placeholder="Chọn ngày"/>
                                </div>

                                @if ($errors->has('delivery_date'))
                                    <span class="text-sm text-red">
                                <i class="fa fa-info-circle"></i> {{ $errors->first('delivery_date') }}
                              </span>
                                @endif

                            </div>
                        </div>

                    </div>
                    <!-- /.card-body -->
                    @csrf
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <section id="pjax-container" class="table-list">
                        <div class="box-body table-responsivep-0">
                            <table class="table table-hover box-body text-wrap table-bordered">
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
                            </table>
                            <button data-perm="order:edit_info" type="button"
                                    class="btn btn-flat btn-success" id="btnAddRow" title="Thêm mới"><i
                                        class="fa fa-plus"></i> Thêm mới
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <section id="pjax-container" class="table-list">
                        <div class="box-body table-responsivep-0">
                            <div>
                                <div class="total-section">
                                    <label class="red-text">Tổng tiền:</label>
                                    <label class="red-text" id="add_total">0 ₫</label>
                                </div>
                                <div class="btn-save mb-5">
                                    <button style="margin-right: 50px;" type="button"
                                            class="btn btn-flat btn-warning" id="btnSave" title="Save"><i
                                                class="fa fa-save"></i> Nhập đơn
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal chon san pham --}}
    @include($templatePathAdmin.'screen.warehouse.import.modal_select_product_import')
@endsection


@push('styles')
    {!! $css ?? '' !!}
    <style>
        .table-bordered th:last-child, .table-bordered td:last-child {
            text-align: center;
            white-space: nowrap;
            width: 1%;
            padding: 8px 16px;
        }

        .flex-1 {
            flex: 1;
        }

        .cursor-hover-pointer:hover {
            cursor: pointer;
        }

        .italic-text {
            font-style: italic;
            color: blue;
        }

        .gray-column {
            background-color: #e7dfdf;
        }
        .red-text {
            color: red;
            font-size: 17px;
        }
        .total-section {
            display: flex;
            justify-content: flex-end;
            margin-right: 100px;
        }
        .total-section label {
            margin-left: 10px;
        }
        .btn-save{
            float: right;
        }
    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script type="text/javascript">
        var productList = <?php echo json_encode($productList ?? [], 15, 512) ?>;
        if (Array.isArray(productList)) {
            productList.forEach(function (p) {
                p.label = p.name;
                p.value = p.name + " (" + p.sku + ")";
            });
        } else {
            console.error('productList is not an array:', productList);
        }

        var supplierList = <?php echo json_encode($supplierList ?? [], 15, 512) ?>;

        var productSelected = null;

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

        function handleReloadPageWithParams() {
            $wareHouseId = $('#warehouse').val();
            $deliveryDate = $("#delivery_date").val();
            let currentUrl = location.protocol + '//' + location.host + location.pathname;
            window.location.href = currentUrl + "?warehouseId=" + $wareHouseId + "&deliveryDate=" + $deliveryDate;
        }

        $('#warehouse').on('change', function (e) {
            handleReloadPageWithParams();
        });

        $("#delivery_date").on("change", function () {
            handleReloadPageWithParams();
        });

        var currentRowIdx = 0;
        var rowIdxArr = [];
        var totalAmount = 0;
        $('#btnAddRow').on("click", function () {
            const selectedWarehouse = $('#warehouse').val();
            const selectedDeliveryDate = $("#delivery_date").val();
            if (selectedWarehouse && selectedDeliveryDate) {
                currentRowIdx++;
                let newRowContent = '<tr id="dataRow-' + currentRowIdx + '">';
                newRowContent += '<td id="productIdx-' + currentRowIdx + '">' + currentRowIdx + '</td>';
                newRowContent += '<td class="pro"><input type="text" id="productSku-' + currentRowIdx + '" readonly class="form-control" tabindex="-1" value=""></td>';
                newRowContent += '<td class="pro"><input type="text" id="productName-' + currentRowIdx + '"  class="form-control flexselect" tabindex="2" autocomplete="off" </td>';
                newRowContent += '<td class="pro"><input type="text" id="supplierName-' + currentRowIdx + '" readonly class="form-control"  tabindex="-1" value=""><input type="hidden" value="" id="supplierId-' + currentRowIdx + '"/></td>';
                newRowContent += '<td class="pro"><input type="text" id="qty-' + currentRowIdx + '"   class="form-control"  tabindex="-1" autocomplete="off" value=""></td>';
                newRowContent += '<td class="pro"><input type="text" id="unit-' + currentRowIdx + '"  readonly class="form-control"  tabindex="-1"  value=""></td>';
                newRowContent += '<td class="pro"><input type="text" id="price-' + currentRowIdx + '"  readonly class="form-control"  tabindex="-1"  value=""></td>';
                newRowContent += '<td class="pro"><input type="text" id="amount-' + currentRowIdx + '"  readonly class="form-control"  tabindex="-1"  value=""></td>';
                newRowContent += '<td class="pro"><input type="text" id="note-' + currentRowIdx + '"   class="form-control"  value=""></td>';
                newRowContent += '<td><button class="btn btn-danger" id="actionDelete-' + currentRowIdx + '"  onclick="deleteRow(' + currentRowIdx + ')"  tabindex="-1">Xóa</button></td>';
                newRowContent += '</tr>';
                $("#tableData").append(newRowContent);
                rowIdxArr.push(currentRowIdx);
                reIndexRowNumber();
            } else {
                alertJs('error', 'Bạn phải chọn kho và ngày giao hàng !');
            }
            var tempCurrentRowIdx = currentRowIdx;
            $("#productName-" + currentRowIdx).autocomplete({
                minLength: 0,
                source: productList,
                select: function (event, ui) {
                    showModalSelectProductImport(tempCurrentRowIdx, ui.item.value);
                    return false;
                }
            }).focus(function () {
                $(this).data("uiAutocomplete").search($(this).val());
            }).autocomplete("instance")._renderItem = function (ul, item) {
                // Render the item in the autocomplete dropdown
                var listItem = $("<li>").append("<div class='autocomplete-item'><div class='flex-1'>" + item.name + " (" + item.sku + ") - Tồn kho: " + item.qty + " - SL nhập gần nhất: " + item.latest_import_qty + "</div></div>");
                ul.append(listItem);
                listItem.on("click", function () {
                    $(".autocomplete-item-details").html("<p>" + item.name + " (" + item.sku + ")</p><p>Tồn kho: " + item.qty + "</p><p>SL nhập gần nhất: " + item.latest_import_qty + "</p>");
                });
                return listItem;
            };
        })

        function showModalSelectProductImport(rowIdx, productValue) {
            let productSelect = productList.find(p => (p.name + " (" + p.sku + ")" == productValue));
            if (!productSelect) {
                return;
            }
            productSelected = productSelect;
            $('#productName').text(productSelect.name + "(" + productSelect.sku + ")");
            $('#productQty').text("Tồn: " + productSelect.qty);
            $('#productLatestImportQty').text("Số lượng nhập gần nhất: " + productSelect.latest_import_qty);
            let productPriceBoardContent = '';
            if (productSelect.priceboard) {
                productSelect.priceboard.forEach(pb => {
                    let supplierId = pb.supplier_id;
                    let supplier = supplierList.find(sp => sp.id == supplierId);
                    var formattedPrice = pb.price.toLocaleString('vi-VN', {style: 'currency', currency: 'VND'});
                    productPriceBoardContent += "<div class='d-flex cursor-hover-pointer mb-2' style='gap: 5px' onclick='handleSelectProduct(" + rowIdx + ',"' + productSelect.id + '","' + pb.supplier_id + '",' + pb.price + ")'><p>" + supplier.name + "</p> <b>" + formattedPrice + "</b></div>";
                })
            }

            $('#productPriceBoard').html(productPriceBoardContent);

            $('#modalSelectProductImport').modal();
        }

        function handleSelectProduct(rowIdx, productId, supplierId, price) {
            let productSelected = productList.find(p => (p.id == productId));
            let supplier = supplierList.find(s => s.id == supplierId);
            $('#productSku-' + rowIdx).val(productSelected.sku);
            if (productSelected.name === '') {
                alertJs('error', 'Bạn chưa chọn sản phẩm !');
            }
            $('#productName-' + rowIdx).val(productSelected.name);
            $('#supplierName-' + rowIdx).val(supplier.name);
            $('#supplierId-' + rowIdx).val(supplier.id);
            $('#unit-' + rowIdx).val(productSelected.unit_name);
            $('#price-' + rowIdx).val(price);
            $('#qty-' + rowIdx).on('change', function () {
                var qtyValue = $(this).val();
                var amountValue = qtyValue * price;
                $('#amount-' + rowIdx).val(amountValue);
                updateTotalAmount();
            });
            $('#modalSelectProductImport').modal('toggle');
            showModalSelectProductImport(rowIdx, productSelected)
        }
        function updateTotalAmount() {
            totalAmount = 0;
            for (var i = 0; i < rowIdxArr.length; i++) {
                var rowIdx = rowIdxArr[i];
                var amountValue = parseFloat($('#amount-' + rowIdx).val()) || 0;
                totalAmount += amountValue;
            }
            var formattedTotalAmount = totalAmount.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' });
            $('#add_total').text(formattedTotalAmount);
        }

        function deleteRow(rowIdx) {
            console.log(rowIdx)
            $('#dataRow-' + rowIdx).remove();
            var idx = rowIdxArr.indexOf(rowIdx);
            if (idx !== -1) {
                rowIdxArr.splice(idx, 1);
            }

            reIndexRowNumber();
        }

        function reIndexRowNumber() {
            for (let i = 0; i < rowIdxArr.length; i++) {
                $('#productIdx-' + rowIdxArr[i]).text(i + 1)
            }
        }

        $('#btnSave').on("click", function () {
            var dataToSave = [];
            var numRequests = rowIdxArr.length;
            for (var i = 0; i < numRequests; i++) {
                var rowIdx = rowIdxArr[i];
                var wareHouse = $('#warehouse').val();
                var deliveryDate = $("#delivery_date").val();
                var productSku = $('#productSku-' + rowIdx).val();
                var productName = $('#productName-' + rowIdx).val();
                var supplierID = $('#supplierId-' + rowIdx).val();
                var supplierName = $('#supplierName-' + rowIdx).val();
                var qty = $('#qty-' + rowIdx).val();
                var unit = $('#unit-' + rowIdx).val();
                var price = $('#price-' + rowIdx).val();
                var note = $('#note-' + rowIdx).val();
                // node.find('.add_total').eq(0).val(0);
                // update_sum_total();
                if (!productName) {
                    alertJs('error', 'Bạn chưa chọn sản phẩm !');
                    return;
                } else if (!qty) {
                    alertJs('error', 'Vui lòng nhập số lượng !');
                    return;
                } else {
                    var rowData = {
                        'warehouse': wareHouse,
                        'deliveryDate': deliveryDate,
                        'productSku': productSku,
                        'productName': productName,
                        'supplierID': supplierID,
                        'supplierName': supplierName,
                        'quantity': qty,
                        'unit': unit,
                        'price': price,
                        'note': note
                    };
                    dataToSave.push(rowData);
                }
            }
            if (dataToSave.length === 0) {
                alertJs('error', 'Chưa có đơn nhập để lưu trữ!');
            } else {
                $.ajax({
                    method: 'POST',
                    url: '{{sc_route_admin('order_import.store')}}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'data_to_save': dataToSave
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (response) {
                        if (response.success) {
                            alertJs('success', 'Tất cả đơn nhập đã được lưu !');
                            window.location.href = "{{ route('order_import.index') }}";
                        } else {
                            alertJs('error', 'Lỗi lưu dữ liệu: ' + response.message);
                        }
                    },
                    complete: function () {
                        $('#loading').hide();
                    }
                });
            }
        });
    </script>
    {!! $js ?? '' !!}
@endpush
