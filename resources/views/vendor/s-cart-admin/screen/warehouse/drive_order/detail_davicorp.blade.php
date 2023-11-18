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
                            <a href="{{ session('nameUrl') ?? sc_route_admin('driver.list_drive_order_davicorp') }}" class="btn btn-flat btn-default"><i
                                        class="fa fa-list"></i>&nbsp;{{ sc_language_render('admin.back_list') }}</a>
                        </div>
                    </div>
                </div>

                <form class="row" id="order_edit_form" method="post"
                      action="{{ sc_route_admin('admin_order.update') }}">
                    @method('put')
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <div class="col-sm-8 mt-3">
                        <table class="table table-hover box-body text-wrap table-bordered table-customer">
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_name') }}:</td>
                                <td>{{ $order->name }}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.customer_address') }}:</td>
                                <td>{!! $order->address ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.phone') }}:</td>
                                <td>{!! $order->phone ?? '' !!}</td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.object') }}</td>
                                <td>
                                    {{ $order->object_id == 1 ? 'Giáo Viên' : 'Học Sinh'}}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title">{{ sc_language_render('admin.order.explain') }}</td>
                                <td>{!! $order->explain !!}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-4 mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td class="td-title">Nhân viên giao hàng:</td>
                                <td>
                                    <a href="#" class="edit-change-drive"
                                       data-name="drive_id" data-type="select"
                                       data-source="{{ json_encode($drivers) }}" data-pk="{{ $order->id }}"
                                       data-ids="{{ $order->id }}"
                                       data-value="{!! $order->drive_id !!}"
                                       data-url="{{ route("driver.change_drive_order_davicorp") }}"
                                       data-title="Thay đổi NV giao hàng">{{ $order->drive_name }}</a>
                                </td>

                            </tr>
                            <tr>
                                <td class="td-title">Trạng thái giao hàng:</td>
                                <td>
                                    {{ $delivery_status[$order->delivery_status] }}
                                </td>
                            </tr>
                            <tr>
                                <td class="td-title"></i> {{ sc_language_render('admin.delivery_time') }}</td>
                                 <td>{{ date('d/m/Y', strtotime($order->delivery_time ?? '')) }}</td>
                            </tr>
                            <tr {{$order->status == 0 ? 'hidden' : ''}}>
                                <td class="td-title"></i> Ngày trên hóa đơn</td>
                                 <td>{{ date('d/m/Y', strtotime($order->bill_date ?? '')) }}</td>
                            </tr>
                        </table>
                    </div>
                </form>

                <form id="form-add-item" action="" method="">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card collapsed-card">
                                <div class="table-responsive">
                                    <table id="table-product" class="table table-hover box-body text-wrap table-bordered table-product">
                                        <thead>
                                        <tr>
                                            <th style="min-width: 45px; padding: 5px; vertical-align: middle; text-align: center">STT</th>
                                            <th style="min-width: 90px; word-break: break-word">{{ sc_language_render('product.sku') }}</th>
                                            <th style="width: auto; min-width: 270px">{{ sc_language_render('admin.order.product.name') }}</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">{{ sc_language_render('product.quantity') }}</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">{{ sc_language_render('product.quantity_reality') }}</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_qty">{{ sc_language_render('product.admin.unit') }}</th>
                                            <th style="min-width: 75px;word-break: break-word" class="product_price">{{ sc_language_render('product.price') }}</th>
                                            <th class="product_total" style="min-width: 80px;word-break: break-word">{{ sc_language_render('product.total_price') }}</th>
                                            <th class="product_comment" style="word-break: break-word;min-width: 90px">{{ sc_language_render('order.admin.comment') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php
                                            $i = 1;
                                             $countStt = $order->details->count();
                                        @endphp
                                        @foreach ($order->details as $item)
                                            <tr class="ordered-products">
                                                <td style="text-align: center">{{ $i++ }}</td>
                                                <td class="overflow_prevent">{{ $item->product_code ?? $item->product->sku }}</td>
                                                <td style="color: {{ (($item->product_priority_level ?? 0) === 1) ? 'red' : '' }}">
                                                    {{ $item->product->name ?? $item->product_name }}
                                                    {!! (($item->product->status ?? '') == 0 ) ? '<br><span style="color: brown">Sản phẩm tạm hết hàng!</span>' : '' !!}
                                                </td>
                                                <td class="product_qty">{{ $item->qty }}
                                                </td>
                                                <td class="product_qty">{{ $item->qty_reality }}
                                                </td>
                                                <td class="product_price">{{ $item->product_unit ?? ''}}
                                                <td class="product_price">{{ sc_currency_render($item->price, 'VND') }}
                                                </td>
                                                <td class="product_total item_id_{{ $item->id }}">{{ sc_currency_render( round($item->total_price) ?? 0, 'vnd') }}</td>
                                                <td class="product_comment" style="overflow: hidden">{{ $item->comment }}
                                                </td>
                                            </tr>
                                        @endforeach
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
                                <tr>
                                    <td class="td-title">{{ sc_language_render('order.order_note') }}:</td>
                                    <td class="order-comment"> {{ $order->comment ?? 'Trống'}}</td>
                                </tr>
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
                                    <td class="text-right td-title data-total">{{ sc_currency_render( round($order->total ?? 0), 'vnd') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    {{-- //End total --}}
                </div>
                {{--                Show hoàn trả đơn hàng--}}
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="tableFixHead">
                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="9">Lịch sử trả hàng</th>
                                </tr>
                                <tr>
                                    <th style="width: 40px; text-align: center">{{ sc_language_render('order.admin.no') }}</th>
                                    <th style="">{{ sc_language_render('order.admin.employe') }}</th>
                                    <th style="">{{ sc_language_render('product.name') }}</th>
                                    <th class="product_qty" style="word-break: normal">Số lượng ban đầu</th>
                                    <th class="product_qty" style="word-break: normal">{{ sc_language_render('order.admin.return_no') }}</th>
                                    <th class="product_price">{{ sc_language_render('product.price') }}</th>
                                    <th class="product_total">{{ sc_language_render('product.total_price') }}</th>
                                    <th class="product_comment">{{ sc_language_render('order.admin.return_date') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($order->returnHistory as $item)
                                    @php
                                        $price_product = $item->price != null ? $item->price : ($item->detail->price ?? 0);
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td style="width: 120px">{{ $item->getEditor() }}</td>
                                        <td>{{ $item->product->name ?? $item->product_name }}
                                        <td class="product_qty">{{ ($item->original_qty + $item->return_qty) ?? 0 }}</td>
                                        <td class="product_price">{{ $item->return_qty ?? 0 }}</td>
                                        <td class="product_price">{{ sc_currency_render((float)$price_product ?? 0 , 'VND') }}</td>
                                        <td class="product_price">{{ sc_currency_render(($item->return_qty ?? 0) * ((float)$price_product ?? 0), 'VND') }}</td>
                                        <td class="product_comment">{{ formatDateVn($item->created_at, true) ?? 'Trống' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="row">
                    {{-- History --}}
                    <div class="col-sm-12 mt-3">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th class="td-title" colspan="3">Lịch sử đơn hàng</th>
                                </tr>
                                <tr>
                                    <td>{{ sc_language_render('admin.order_history.time') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.actor') }}</td>
                                    <td>{{ sc_language_render('admin.order_history.action') }}</td>
                                </tr>
                                </thead>
                                <tbody  id="order-history">
                                @forelse($order->history ?? [] as $k => $v)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::make($v->add_date)->format('d/m/Y H:i:s') ?? 'Trống' }}</td>
                                        <td>{{ $v->is_admin == 1 ? '(Ad)' : '' }} {{ $v->user_name != '' ? $v->user_name : $v->getEditor()}}</td>
                                        <td>
                                            <div>
                                                {!! $v->content !!}
                                            </div>
                                        </td>
                                    </tr>
                                @empty

                                @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('styles')
    <style type="text/css">
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

        .undo_detail {
            text-align: center;
            max-width: 80px !important;
            word-break: normal !important;

        }

        .product_qty {
            width: 120px;
            text-align: right;
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
            word-break: break-word !important;
        }
        .table-product td {
            white-space: normal;
        }
        .table-customer td {
            white-space: normal;
        }
        .custom-control-label {
            font-weight: 400 !important;
        }
        .overflow_prevent {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .modal-product {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: block;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
        }
        .editableform {
            display: table-caption;
        }
        .editable-clear {
            display: none;
        }
        .icon-arrow-right {
            border: solid black !important;
            border-width: 0 3px 3px 0 !important;
            display: inline-block !important;
            padding: 3px !important;
            transform: rotate(-45deg) !important;
            -webkit-transform: rotate(-45deg) !important;
        }
        .icon-arrow-left {
            border: solid black !important;
            border-width: 0 3px 3px 0 !important;
            display: inline-block !important;
            padding: 3px !important;
            transform: rotate(135deg) !important;
            -webkit-transform: rotate(135deg) !important;
        }

        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
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

    </style>
    {{-- flexselect --}}
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/flexselect.css') }}" type="text/css" media="screen" />
    {{-- X-Editable --}}
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
@endpush

@push('scripts')
    {{-- flexselect --}}
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>

    {{-- Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js')}}"></script>

    <!-- Handle navigation with key arrow -->
    <script type="text/javascript">
        function alertJsCustom(type = 'error', msg = '') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000
            });
            Toast.fire({
                type: type,
                title: msg
            })
        }
    </script>
    <!-- /Handle navigation with key arrow -->

    <script type="text/javascript">
        $(document).ready(function () {
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
            all_editable();
        });

        function all_editable() {
            $.fn.editable.defaults.params = function (params) {
                params._token = "{{ csrf_token() }}";
                console.log(params)
                return params;
            };

            $('.edit-change-drive').editable({
                ajaxOptions: {
                    type: 'post',
                    dataType: 'json'
                },
                success: function (response) {
                    if (response.error == 0) {
                        alertJs('success', response.msg);
                    } else {
                        alertJs('error', response.msg);
                    }
                }
            });
        }
    </script>
    <script src="{{ asset("js/admin_order_helper.js") }}"></script>
@endpush
