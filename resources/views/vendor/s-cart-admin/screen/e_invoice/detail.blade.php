@extends($templatePathAdmin.'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">{{ sc_language_render('einvoice.detail') }}
                    </h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ session('nameUrlEinvoiceIndex') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a class="btn btn-flat btn btn-primary" id="export_sales_invoice_detail_virtual"><i
                                        class="fa fa-print"></i>&nbsp;{{ sc_language_render('admin.order.print_invoice') }}
                            </a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="#" class="btn btn-flat btn btn-primary" onclick="location.reload()"><i
                                        class="fa fa-sync-alt"></i></a>
                        </div>
                    </div>
                </div>

                <form class="row" id="order_edit_form" method="post"
                      action="">
                    <input type="hidden" name="customer_kind" id="customer_kind" value="{{ $einvoice->customer_kind }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table box-body text-wrap table-bordered">
                            <tr>
                                <td class="td-title">{{ sc_language_render('customer.code') }}</td>
                                <td>
                                    <p>
                                        {{ $einvoice->customer_code }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_name') }}</td>
                                <td>
                                    <p>
                                        {{ $einvoice->customer_name }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.object') ?? 'Loại khách hàng' }}</td>
                                <td>
                                    <p>
                                        @foreach($customer as $key => $value)
                                            @if($key == $einvoice->customer_kind)
                                                {{ $value }}
                                            @endif
                                        @endforeach
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Mã số thuế</td>
                                <td>
                                    <p>
                                        {!! $einvoice->tax_code !!}
                                    </p>

                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">Ngày xuất HĐ điện tử</td>
                                <td>
                                    <p>
                                        {{ $einvoice->plan_sign_date != '' ? date('d-m-Y H:i:s', strtotime($einvoice->plan_sign_date)) : '' }} &nbsp;
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">ID</td>
                                <td>
                                    {{ $einvoice->id_name}}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{  "Mã đơn đặt hàng" }}:</td>
                                <td>
                                    <a data-perm="order:detail" class="td-title" href="{{ sc_route_admin('admin_order.detail', ['id' => $einvoice->order_id ? $einvoice->order_id : 'not-found-id']) }}">
                                        {{ $einvoice->order_id ?? '' }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('einvoice.sync_system') ?? "Hệ thống đồng bộ" }}</td>
                                <td>
                                    {{ $einvoice->sync_system}}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"> {{ sc_language_render('einvoice.status') ?? "Trạng thái" }}</td>
                                <td>
                                    <input type="hidden" name="process-status" value="{{ $einvoice->process_status }}">
                                    <p>
                                        @foreach($status as $key => $value)
                                            @if($key == $einvoice->process_status)
                                                {{ $value }}
                                            @endif
                                        @endforeach
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('einvoice.number') ?? "Số HĐ Einvoice" }}</td>
                                <td>
                                    {{ $einvoice->einv_id ?? ''}}
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>


                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="einv_id" value="{{ $einvoice->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table class="table table-hover box-body text-wrap table-bordered">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 45px; padding: 5px; vertical-align: middle; text-align: center">STT</th>
                                            <th style="width: 120px">{{ sc_language_render('product.sku') }}</th>
                                            <th style="width: auto !important; min-width: 230px">{{ sc_language_render('admin.order.product.name') }}</th>
                                            <th class="product_qty" style="width: 115px">{{ sc_language_render('product.admin.unit') }}</th>
                                            <th class="product_qty" style="width: 100px">{{ sc_language_render('product.quantity') }}</th>
                                            <th  style="width: 120px; text-align: center">Giá bán</th>
                                            <th  style="width: 85px; text-align: center">{{ sc_language_render('einvoice.number_tax') }}</th>
                                            <th  style="width: 145px">Giá trước thuế</th>
                                            <th  style="width: 120px">Tiền hàng</th>
                                            <th  style="min-width: 100px">Tiền thuế</th>
                                            <th  style="width: 120px">{{ sc_language_render('product.total_price') }}</th>
                                            <th style="min-width: 65px;word-break: break-word; text-align: center">{{ sc_language_render('action.title') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                        @endphp
                                        @foreach ($details as $item)
                                            <tr>
                                                <td style="text-align: center">{{ $i++ }}</td>
                                                <td class="overflow_prevent">
                                                    <p>
                                                        {{ $item->product_code ?? 'Sản phẩm bị xoá' }}
                                                    </p>
                                                </td>
                                                <td>
                                                    {{ $item->product_name ?? 'Sản phẩm bị xoá'}}
                                                </td>
                                                <td class="product_price">
                                                    <p >
                                                        {{ $item->unit ?? ''}}
                                                    </p>
                                                </td>
                                                <td class="product_qty">
                                                    <a href="#"
                                                       class="edit-item-detail"
                                                       data-value="{{ $item->qty }}" data-name="qty"
                                                       data-step="any"
                                                       data-type="text" data-min="0"
                                                       data-pk="{{ $item->id }}"
                                                       data-url="{{ route("admin.einvoice.update") }}"
                                                       data-title="{{ sc_language_render('order.qty') }}"> {{ $item->qty }}</a>
                                                </td>
                                                <td class="product_price">
                                                    <a href="#"
                                                       class="edit-item-detail"
                                                       data-value="{{ $item->price }}"
                                                       data-name="price" data-type="number"
                                                       data-min="0"
                                                       data-pk="{{ $item->id }}"
                                                       data-url="{{ route("admin.einvoice.update") }}"
                                                       data-title="{{ sc_language_render('product.price') }}">{{ sc_currency_render($item->price, 'VND') }}</a>
                                                </td>
                                                <td class="product_qty">
                                                    <a href="#"
                                                       class="edit-item-detail"
                                                       data-value="{{ $item->tax_no }}" data-name="tax_no"
                                                       data-step="any"
                                                       data-type="text" data-min="0"
                                                       data-pk="{{ $item->id }}"
                                                       data-url="{{ route("admin.einvoice.update") }}"
                                                       data-title="Thuế suất"> {{ $item->tax_no }}</a>
                                                </td>
                                                <td class="product_price">
                                                    <p>
                                                        {{ number_format($item->pretax_price, 3) . '₫'}}
                                                    </p>
                                                </td>
                                                <td class="product_price">
                                                    <p>
                                                        {{ sc_currency_render(($item->qty * $item->price - $item->tax_amount) , 'vnd')  }}
                                                    </p>
                                                </td>
                                                <td class="product_total">
                                                    {{ sc_currency_render($item->tax_amount, 'vnd') }}
                                                </td>
                                                <td class="product_total">
                                                    {{ sc_currency_render(($item->qty * $item->price) , 'vnd') }}
                                                </td>
                                                <td style="text-align: center">
                                                        <span onclick="deleteItem('{{ $item->id }}');" class="btn btn-danger btn-xs" data-title="Delete">
                                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                                        </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr id="add-item" class="not-print">
                                            <td colspan="12">
                                                <button data-perm="einvoice:edit_info" type="button"
                                                        class="btn btn-flat btn-success"
                                                        id="add-item-button"
                                                        title="{{sc_language_render('action.add') }}"><i
                                                            class="fa fa-plus"></i> {{ sc_language_render('action.add') }}
                                                </button>
                                                &nbsp;&nbsp;&nbsp;<button style="display: none; margin-right: 50px"
                                                                          type="button" class="btn btn-flat btn-warning"
                                                                          id="add-item-button-save" title="Save"><i
                                                            class="fa fa-save"></i> {{ sc_language_render('action.save') }}
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>

                <div class="row">
                    {{-- Comment --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="3">{{ sc_language_render('einvoice.combine.order_detail') }}</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; width: auto">{{ sc_language_render('admin.order.id') }}</th>
                                    <th style="text-align: center; width: auto">{{ "Mã đơn đặt hàng" }}</th>
                                    <th style="text-align: center; width: auto">{{ sc_language_render('product.total_price') }}</th>
                                </tr>
                                </thead>
                                @foreach($einvoice->multipleEinvoices as $key => $value)
                                    <tr>
                                        <td data-perm="einvoice:edit" class="td-title"><a href="{{ sc_route_admin('admin.einvoice.detail', ['id' => $value->id ? $value->id : 'not-found-id']) }}">{{ $value->id_name }}</a></td>
                                        <td data-perm="order:detail" class="td-title"><a href="{{ sc_route_admin('admin_order.detail', ['id' => $value->order_id ? $value->order_id : 'not-found-id']) }}">{{ $value->order_id }}</a></td>
                                        <td class="order-comment">
                                            {{ number_format($value->total_amount, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    {{-- //End comment --}}
                    {{-- total --}}
                    <div class="col-sm-6 mt-3">
                        <div class="table-responsive">
                            <table class="table table-borderless table-striped">
                                <tr>
                                    <td class="td-title">{{ sc_language_render('admin.order_total_price') }}:</td>
                                    <td class="text-right td-title data-total">{{ sc_currency_render(($einvoice->total_amount ?? 0), 'vnd') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End total --}}
                </div>
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="4">{{ sc_language_render('einvoice.history') }}</th>
                                </tr>
                                <tr>
                                    <td style="text-align: center; width: 70px;">Ngày</td>
                                    <td style="text-align: center; width: 50px;">Trạng thái</td>
                                    <td style="text-align: center; width: auto">Thông tin</td>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($histories as $k => $v)
                                    <tr>
                                        <td style="text-align: center">{{ $v->start_date }}</td>
                                        <td style="text-align: center">
                                            @foreach($historyStatus as $key => $value)
                                                @if($key == $v->status)
                                                    {{ $value }}
                                                @endif
                                            @endforeach
                                        </td>
                                        <td>{{ $v->error }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">Chưa có lịch sử làm lệnh</td>
                                    </tr>
                                @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                    {{-- //End history --}}

                </div>
                @php
                    $htmlSelectProduct =
                            '<tr class="select-product">
                                 <td><input type="text" readonly="readonly" class="add_sku form-control" value=""></td>
                                <td><input type="text" readonly="readonly" class="add_sku form-control" value=""></td>
                                <td id="add_td">
                                    <input type="text" name="add_id[]" onChange="selectProduct($(this));" class="add_id form-control" list="product-list" data-id="" data-product_name="" autocomplete="off" placeholder="Chọn sản phẩm">
                                        <datalist id="product-list">';
                                        if(isset($products)) {
                                            foreach ($products as $pId => $product) {
                                                ($product['status'] == 0)
                                                ?
                                                $htmlSelectProduct .='<option disabled data-value="'.$product['product_id'].'" value="'.$product['product_name'].' ('.$product['product_code'].') - Sản phẩm tạm hết hàng!"></option>'
                                                :
                                                $htmlSelectProduct .='<option data-value="'.$product['product_id'].'" value="'.$product['product_name'].' ('.$product['product_code'].')"></option>';
                                            }
                                        }
                    $htmlSelectProduct .='
                                        </datalist>
                                    </input>
                                </td>
                                <td><input type="text" readonly class="add_unit form-control" name="unit" value=""></td>
                                <td><input style="padding: 0.25rem !important;" type="number" onChange="update_total($(this));"  class="add_qty form-control" name="add_qty[]" value="0"></td>
                                <td><input style="padding: 0.25rem !important;" type="number" onChange="update_total($(this));" id="add_price" class="add_price form-control" name="add_price[]" value="0"></td>
                                <td><input style="padding: 0.25rem !important;" type="number" onChange="update_total($(this));" class="add_tax form-control" name="add_tax[]"  value="0"></td>
                                <td><input type="number" readonly class="add-price-before-tax form-control"  value="0"></td>
                                <td><input type="number" readonly class="price-product form-control" value="0"></td>
                                <td><input type="number" readonly class="form-control tax-money" name="tax_money[]" value="0" ></td>
                                <td><input class="total-price form-control" readonly name="total_price[]" value="" autocomplete="off"></td>
                                <td style="text-align:center"><button onClick="$(this).parent().parent().remove(); checkRemoveDOM(); checkNumberOrder()" class="btn btn-danger btn-md btn-flat" data-title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                             </tr>';
                    $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
                    $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
                @endphp
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="printDialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form class="modal-content" method="post" target="_blank" id="printForm"
                  action="{{ sc_route_admin('admin_order.print') }}">
                @csrf
                <input type="hidden" name="ids" value="">
                <input type="hidden" name="option" value="1">
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
                                <input name="type" id="radio_0" type="radio" class="custom-control-input" value="1">
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

    <!-- Form submit export sales invoice list virtual -->
    <form action="{{ sc_route_admin('admin.einvoice.export_sales_invoice_detail_virtual') }}" method="post" id="form_export_sales_invoice_detail_virtual">
        @csrf
        <input type="hidden" name="order_id" id="customer_code" value="{{ $einvoice->id }}">
        <input type="hidden" name="from_to_time" id="from_to_time" value="{{ request('from_to') }}">
        <input type="hidden" name="end_to_time" id="end_to_time" value="{{ request('end_to') }}">
    </form>
    <!-- Form submit preview print pdf sales invoice list virtual -->
    <form action="{{ sc_route_admin('admin.einvoice.print_sales_invoice_detail_virtual') }}" target="_blank" method="post" id="form_print_sales_invoice_detail_virtual">
        @csrf
        <input type="hidden" name="order_id" id="customer_code" value="{{ $einvoice->id }}">
        <input type="hidden" name="from_to_time" id="from_to_time" value="{{ request('from_to') }}">
        <input type="hidden" name="end_to_time" id="end_to_time" value="{{ request('end_to') }}">
    </form>

@endsection


@push('styles')
    <style type="text/css">
        .custom-text-decoration {
            color: #1589DB !important;
            border-bottom: 0.5px solid #1589DB !important;
            display: inline !important;
        }

        .td-title {
            width: 35%;
            font-weight: bold;
        }

        .product_qty {
            width: 120px;
            text-align: center;
        }

        .product_price, .product_total {
            text-align: center;
        }

        .box-body td, .box-body th {
            max-width: 150px;
            word-break: normal!important;
        }

        table {
            width: 100%;
        }

        table td {
            word-wrap: normal;
        }

        table td:last-child {
            width: 120px;
        }

        .custom-control-label {
            font-weight: 400 !important;
        }

        .overflow_prevent {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

    </style>
    <!-- Ediable -->
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>



    <script type="text/javascript">

        function update_total(e) {
            node = e.closest('tr');
            let validateTax = ['0', '5', '8', '10'];
            let qty = node.find('.add_qty').eq(0).val();
            let price = node.find('.add_price').eq(0).val();
            let tax = node.find('.add_tax').eq(0).val();

            if(Number(price)<0) {
                node.find('.add_price').val(0);
            }
            if(validateTax.includes(tax)) {
                let priceBeforeTax = Number(price/(1 + tax/100)).toFixed(2);
                let priceProduct = Number(price/(1 + tax/100) * qty).toFixed(2);
                let totalPrice = Number(price * qty).toFixed(2);
                let taxMoney = Number(price * qty - price/(1 + tax/100) * qty).toFixed(2);
                node.find('.add-price-before-tax ').eq(0).val(priceBeforeTax);
                node.find('.price-product').eq(0).val(priceProduct);
                node.find('.total-price').eq(0).val(totalPrice);
                node.find('.tax-money').eq(0).val(taxMoney);
            } else {
                alertJs('error', 'Thuế suất quy định: 0, 5, 8, 10 !');
            }


        }
        // Check value có tồn tại trong ds sản phẩm
        function checkExists(inputValue) {
            var x = document.getElementById("product-list");
            var i;
            var flag;
            for (i = 0; i < x.options.length; i++) {
                if(inputValue == x.options[i].value){
                    flag = true;
                }
            }
            return flag;
        }
        function selectProduct(e) {
            node = e.closest('tr');
            var value = node.find('.add_id').val();
            var id = $('#product-list [value="'+ value +'"]').data('value');
            let customer_kind = $('#customer_kind').val();
            node.find('.add_id').attr("data-id", id);
            node.find('.add_qty').data('check', 3); // Set trạng thái lại trạng thái khi số lượng 0
            node.find('.add_qty').css("border", ""); // Ẩn border red
            if (checkExists(value) === true) {
                node.find('.add_id').attr('data-product_name', value);

                $.ajax({
                    url: '{{ sc_route_admin('admin_einvoice.product_info') }}',
                    type: "get",
                    dateType: "application/json; charset=utf-8",
                    data: {
                        id: id,
                        customer_kind: customer_kind,
                    },
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (returnedData) {
                        node.find('.add_sku').val(returnedData.sku);
                        node.find('.add_qty').val();
                        node.find('.add_price').val();
                        node.find('.add_unit').val(returnedData.unit);
                        node.find('.add_tax').val(returnedData.tax_no);
                        $('#loading').hide();
                    }
                });
            } else {
                // Trả lại giá trị trước đó nếu không chọn sản phẩm từ danh sách
                var prev_product_name = node.find('.add_id').attr('data-product_name');
                node.find('.add_id').val(prev_product_name);
            }

        }

        $('#add-item-button').click(function () {
            var html = '{!! $htmlSelectProduct !!}';
            $('#add-item').before(html);
            $('.select2').select2();
            $('#add-item-button-save').show();
        });

        $('#add-item-button-save').click(function (event) {
            var qty = [];
            var products_id = [];
            let price = [];
            let tax = [];
            $('.add_qty').each(function () {
                qty.push($(this).val());
            });
            $('.add_tax').each(function () {
                tax.push($(this).val());
            });
            $('.add_id').each(function () {
                products_id.push($(this).attr('data-id'));
            });
            $('.add_price').each(function () {
                price.push($(this).val());
            });
            let checkDataQty = qty.every(item => {
                return item <= 0;
            });
            let checkDataPrice = price.every(item => {
                return item <= 0;
            });
            if (checkDataQty) {
                return alertJs('error', 'Số lượng phải lớn hơn 0, vui lòng kiểm tra lại chi tiết hóa đơn!');
            }
            if (checkDataPrice) {
                return alertJs('error', 'Giá tiền phải lớn hơn 0, vui lòng kiểm tra lại chi tiết hóa đơn!');
            }
            if (products_id.includes('')) {
                return alertJs('error', 'Sản phẩm không được để trống, vui lòng kiểm tra lại chi tiết hóa đơn!');
            }
            if (!tax.includes('0') && !tax.includes('5') && !tax.includes('8') && !tax.includes('10')) {
                return alertJs('error', 'Thuế suất quy định: 0, 5, 8, 10 !');
            }

            $('#add-item-button').prop('disabled', true);
            $('#add-item-button-save').button('loading');

            $('#add-item-button').prop('disabled', true);
            $.ajax({
                url: '{{ route("admin.einvoice.create_item") }}',
                type: 'post',
                dataType: 'json',
                data:
                    $('form#form-add-item').serialize()
                    +"&products_id="+products_id
                ,
                beforeSend: function () {
                    $('#loading').show();
                }
                ,success: function (result) {
                    $('#loading').hide();
                    if (parseInt(result.error) == 0) {
                        location.reload();
                    } else {
                        alertJs('error', result.msg);
                        $('#add-item-button').prop('disabled', false);
                    }
                }
            });

        });

        $(document).ready(function () {
            all_editable();
        });

        function all_editable() {

            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                return params;
            };

            $('.edit-item-detail').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                validate: function (value) {
                    if (value == '') {
                        return '{{  sc_language_render('admin.not_empty') }}';
                    }
                    if (!$.isNumeric(value)) {
                        return '{{  sc_language_render('admin.only_numeric') }}';
                    }
                    if (Number(value) < 0) {
                        return 'Số không được âm!';
                    }
                },
                success: function (response, newValue) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                        $('#order-history').html(response.history);
                        location.reload()
                    } else {
                        alertJs('error', response.msg);
                        setTimeout(function() {
                            location.reload()
                        }, 1000);
                    }
                }
            });
        }


        {{-- sweetalert2 --}}
        function deleteItem(id) {
            if (!id) {
                return alertJs('error', 'Đơn gộp không thể xóa sản phẩm!');
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
                            method: 'POST',
                            url: '{{ route("admin.einvoice.delete_item_detail") }}',
                            data: {
                                'pId': id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (response) {
                                if (response.error == 0) {
                                    location.reload();
                                    alertJs('success', response.msg);
                                } else {
                                    alertJs('error', response.msg);
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

        $('#delete_product').on('click', function (e) {
            let id = $('#id_product').val();
            window.location.href = '{{ sc_route_admin('admin_order.delete_product_old') }}?id=' + id;
        })
        $('.close-modal').on('click', function (e) {
            $('.modal-product').css("display", "none");
        })
    </script>

    <!-- Preview print PDF and export excell virtual detail order e-invoice -->
    <script type="text/javascript">
        $('#export_sales_invoice_detail_virtual').on('click', function () {
            var process_status  = $('[name="process-status"]').val();
            if(process_status !== "4") {
                alertMsg('error', 'Hóa đơn chưa có trạng thái đồng bộ thành công không thể xuất bản kê', '{{ sc_language_render('action.warning') }}');
                return;
            }
            const form = $('#form_print_sales_invoice_detail_virtual');
            form.submit();
            setTimeout(exportExcellVirtualReport, 250);
        });
        function exportExcellVirtualReport() {
            const form = $('#form_export_sales_invoice_detail_virtual');
            form.submit();
        }
    </script>

@endpush
