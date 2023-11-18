@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card p-3">
                <div class="card-header with-border">
                    <h3 class="card-title"
                        style="font-size: 18px !important;">{{ sc_language_render('order.order_detail') }}
                        #{{ $order->id_name }}</h3>
                    <div class="card-tools not-print">
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a class="btn btn-outline-primary" href="{{ route('admin_order.detail', ['id' => request('id')]) }}"><i
                                        class="fa fa-arrow-alt-circle-left"></i> {{ sc_language_render('action.back') }}
                            </a>
                        </div>
                        <div class="btn-group float-right" style="margin-right: 10px">
                            <a href="{{ sc_route_admin('admin_order.print_return', ['id' => $order->id]) }}" target="_blank" class="btn btn btn-primary"><i
                                        class="fa fa-print"></i>&nbsp;{{ sc_language_render('admin_order.print_return') }}
                            </a>
                        </div>

                    </div>
                </div>

                <form id="form-add-item" action="" method="post">
{{--                    {{ dd('ok') }}--}}
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table class="table table-hover box-body text-wrap table-bordered">
                                        <thead>
                                        <tr>
                                            <th style="width: 120px">{{ sc_language_render('product.sku') }}</th>
                                            <th style="width: auto">{{ sc_language_render('admin.order.product.name') }}</th>
                                            <th class="product_qty">{{ sc_language_render('product.quantity') }}</th>
                                            <th class="product_qty">{{ sc_language_render('order.admin.return_no') }}</th>
                                            <th class="product_price">{{ sc_language_render('product.price') }}</th>
                                            <th class="product_total">{{ sc_language_render('product.total_price') }}</th>
                                            <th class="product_comment">{{ sc_language_render('order.admin.comment') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($order->details as $item)
                                            <tr>
                                                <td class="overflow_prevent">{{ $item->product->sku ?? 'Sản phẩm bị xoá' }}</td>
                                                <td>{{ $item->product->name ?? 'Sản phẩm bị xoá'}}
                                                <td class="product_qty">{{ $item->qty_reality ?? 0 }}</td>
                                                <td class="product_price"><input name="qty[{{ $item->id }}]"
                                                                                 id="qty_{{ $item->id }}" type="number"
                                                                                 class="form-control"
                                                                                 oninput="returnHandle({{ $item->price }}, {{ $item->qty_reality }}, '{{ $item->id }}')"
                                                                                 value="0" min="0" step="any"/></td>
                                                <td class="product_price">{{ sc_currency_render($item->price, $order->currency ?? 'VND') }}</td>
                                                <td class="product_total item_id_{{ $item->id }}"><span
                                                            id="calculated_price_{{ $item->id }}">0</span>₫
                                                </td>
                                                <td class="product_comment">{{ $item->comment ?? 'Trống' }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div style="display: flex; flex-direction: row; justify-content: end" class="p-3">
                                    <button type="submit" id="submitReturnOrder" class="btn btn-primary mr-3"><i
                                                class="fa fa-save"></i> {{ sc_language_render('action.save') }}</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="tableFixHead">
                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 120px">{{ sc_language_render('order.admin.no') }}</th>
                                    <th style="">{{ sc_language_render('order.admin.employe') }}</th>
                                    <th style="">{{ sc_language_render('product.name') }}</th>
                                    {{--                                    <th class="product_qty">{{ sc_language_render('admin.order.qty') }}</th>--}}
                                    <th class="product_qty" style="min-width: 150px">{{ "Số lượng ban đầu" }}</th>
                                    <th class="product_qty">{{ sc_language_render('order.admin.return_no') }}</th>
                                    <th class="product_price">{{ sc_language_render('product.price') }}</th>
                                    <th class="product_total">{{ sc_language_render('product.total_price') }}</th>
                                    <th class="product_comment">{{ sc_language_render('order.admin.return_date') }}</th>
                                    <th class="undo_detail">Thao tác</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order->returnHistory as $item)
                                    @php
                                        $price_product = $item->price != null ? $item->price : ($item->detail->price ?? 0);
                                    @endphp
                                    <tr id="row_return_{{$item->id}}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="width: 120px">{{ $item->getEditor() }}</td>
                                        <td>{{ $item->product->name ?? $item->product_name }}
                                        <td class="product_qty">{{ ($item->original_qty + $item->return_qty) ?? 0 }}</td>
                                        <td class="product_price">{{ $item->return_qty ?? 0 }}</td>
                                        <td class="product_price">{{ sc_currency_render($price_product ?? 0 , 'VND') }}</td>
                                        <td class="product_price">{{ sc_currency_render(($item->return_qty ?? 0) * ($price_product ?? 0), 'VND') }}</td>
                                        <td class="product_comment">{{ formatDateVn($item->created_at, true) ?? 'Trống' }}</td>
                                        <td class="undo_detail"><span onclick="undoReturnOrder('{{$item->id}}', '{{$item->detail_id}}')" class="btn btn-sm btn-sm btn-warning"><i class="fas fa-undo text-white"></i></span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- //End history --}}

                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style type="text/css">
        .tableFixHead {
            overflow: auto;
            max-height: 400px;
        }

        .tableFixHead thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        /* Just common table stuff. Really. */
        table {
            width: 100%;
        }

        th, td {
            padding: 8px 16px;
        }

        .undo_detail {
            text-align: center;
            max-width: 80px;
            word-break: normal !important;

        }


        .history {
            max-height: 50px;
            max-width: 300px;
            overflow-y: auto;
        }

        .td-title {
            width: 35%;
            font-weight: bold;
        }

        .td-title-normal {
            width: 35%;
        }

        .product_qty {
            width: 135px;
            text-align: right;
            word-break: normal !important;
        }

        .product_price, .product_total {
            width: 160px;
            text-align: right;
        }

        .product_comment {
            width: 480px !important;
        }

        table {
            width: 100%;
        }

        table td {
            white-space: nowrap; /** added **/
        }

        table td:last-child {
            width: 85px;
        }

        .custom-control-label {
            font-weight: 400 !important;
        }
        .overflow_prevent {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .table-bordered td {
            display: table-cell;
            vertical-align: middle;
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
        function submitOrder() {
            $('#order_edit_form').submit();
        }



        function returnHandle(price, default_qty, id) {
            const hintLabel = $('#hint_qty_' + id);
            const qtyInput = $('#qty_' + id);
            const calculatedLabel = $('#calculated_price_' + id);
            if (qtyInput.val() > default_qty) {
                alertMsg('error', 'Số lượng trả lại không được lớn hơn số đã đặt')
                qtyInput.val(0);
                calculatedLabel.html(formatNumber(price * qtyInput.val(), '.', ','));
            } else {
                $('#hint_qty_' + id).hide();
                calculatedLabel.html(formatNumber(price * qtyInput.val(), '.', ','));
            }
        }


        {{-- sweetalert2 --}}
        function deleteItem(id) {
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
                            url: '{{ route("admin_order.delete_item") }}',
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

        {{--/ sweetalert2 --}}


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }

        });
        
        function undoReturnOrder(id, detail_id) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: 'Bạn muốn hoàn tác lại ?',
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
                            url: '{{ route("admin_order.undo_return_order") }}',
                            data: {
                                'return_id': id,
                                'detail_id': detail_id,
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
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })
        }

        function order_print() {
            $('.not-print').hide();
            window.print();
            $('.not-print').show();
        }

        $('#submitReturnOrder').click(function () {
            setTimeout(function() {
                $('#submitReturnOrder').attr('disabled', true)
            }, 100);
        })
    </script>

@endpush
