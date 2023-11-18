@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_priceboard.index') }}"
                               class="btn  btn-flat btn-default" title="List">
                                <i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                @php
                    $id_priceBoard = $userPriceboard->id ?? '';
                @endphp
                <form action="{{ $actionRoute }}" autocomplete="off" method="post" name="form_name" accept-charset="UTF-8"
                      class="form-horizontal" id="form-main" enctype="multipart/form-data">
{{--                    @if (isset($method))--}}
{{--                        @method($method)--}}
{{--                    @endif--}}
                    <div id="main-add" class="card-body">
                        {{-- name --}}
                        <div class="form-group row kind  {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="sku" class="col-sm-2 col-form-label">Tên nhóm báo giá
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="name" name="name"
                                           value="{!! old('name', $userPriceboard->name ?? '') !!}"
                                           class="form-control input-sm sku"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('name'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //name --}}
                        {{-- code --}}
                        <div class="form-group row kind  {{ $errors->has('priceboard_code') ? ' text-red' : '' }}">
                            <label for="sku" class="col-sm-2 col-form-label">Mã nhóm báo giá
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="priceboard_code" name="priceboard_code"
                                           value="{!! old('priceboard_code', $userPriceboard->priceboard_code ?? '') !!}"
                                           class="form-control input-sm sku"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('priceboard_code'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('priceboard_code') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //code --}}
                        {{-- select priceboard --}}
                        <div class="form-group row kind  {{ $errors->has('product_price_id') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">Báo giá
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-7">
                                <div class="input-group">
                                    <select class="form-control input-sm select2" id="select_product_price_id"
                                            name="product_price_id" required>
                                        <option value="">---</option>
                                        @if($currentPrice)
                                            <option value="{{$currentPrice->id}}"
                                                    selected>{{$currentPrice->name}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($errors->has('product_price_id'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('product_price_id') }}
                                </span>
                                @endif
                            </div>
                            <div class="col-md-1 text-right {{ $id_priceBoard ? '' : 'display' }}" id="display_button_detail">
                                <a id="price_board_detail" href="#" class="btn btn-info">Chi tiết</a>
                            </div>
                        </div>
                        {{-- //select priceboard --}}
                        {{-- start date --}}
                        <div class="form-group row kind   {{ $errors->has('start_date') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('priceboard.start_date') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="start_date" name="start_date"
                                           value="{!! old('start_date', $userPriceboard->start_date ?? '') !!}"
                                           class="form-control input-sm date_time"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('start_date'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('start_date') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //start date --}}
                        {{-- due date --}}
                        <div class="form-group row kind   {{ $errors->has('due_date') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('priceboard.due_date') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="due_date" name="due_date"
                                           value="{!! old('due_date', $userPriceboard->due_date ?? '') !!}"
                                           class="form-control input-sm sku date_time"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('due_date'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('due_date') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //due date --}}
                        {{-- select customer --}}
                        <div class="form-group row kind  {{ $errors->has('unit_id') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('priceboard.customer_list') }}</label>
                            <div class="col-sm-8">
                                <input name="customer_data" id="customer_data" value="" hidden>
                                <div class="customer-container mt-3">
                                    <div id="treeview" class="hummingbird-treeview-converter" data-boldParents="true">
                                        @foreach($customers as $id => $customer)
                                            <li data-id="{{ $id }}">{{ $customer }}</li>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- //select customer --}}
                    </div>
                    <!-- /.card-body -->


                    <div class="card-footer kind   row" id="card-footer">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button data-perm="priceboard:edit" type="submit"
                                        class="btn btn-primary">{{ sc_language_render('action.submit') }}</button>
                            </div>

                            <div class="btn-group float-left">
                                <button type="reset"
                                        class="btn btn-warning">{{ sc_language_render('action.reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.css"
          rel="stylesheet">
    <style>
        ul#treeview li i {
            margin-left: -19px;
        }
        ul#treeview label:not(.form-check-label):not(.custom-file-label) {
            font-weight: 400;
        }
        .display {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    @include($templatePathAdmin.'component.ckeditor_js')
    <script src="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.js"></script>
    <script>
        var json = [];
        var customerList = [];
        const select_customer = $('#select_customer');
        const btn_add_customer = $('#btn_add_customer');
        const hidden_input_customer = $('#customer_data');
        const table_body = $('#customer_table_body');
        const editInitCustomer = JSON.parse('{!! $customerList ?? '[]' !!}');
        let selectedCustomer = [];
        let currentCustomer = JSON.parse('{!! json_encode($currentCustomer ?? []) !!}');


        $(document).ready(function () {
            $.fn.hummingbird.defaults.SymbolPrefix = "far";
            $.fn.hummingbird.defaults.collapsedSymbol = "fa-plus-square";
            $.fn.hummingbird.defaults.expandedSymbol = "fa-minus-square";
            $.fn.hummingbird.defaults.checkDoubles = true;
            $.fn.hummingbird.defaults.boldParent = true;
            $.fn.hummingbird.defaults.singleGroupOpen = 2;

            // Treeview js
            $("#treeview").hummingbird();
            // $("#treeview").hummingbird("expandAll");
            $("#treeview").hummingbird("checkNode", {sel: "data-id", vals: currentCustomer});
            updateCustomerInfo();
            $("#treeview").on("click", function () {
                updateCustomerInfo();
            });

        {{--editInit();--}}
            {{--btn_add_customer.click(function () {--}}
            {{--    let add_customer_id = select_customer.val();--}}
            {{--    let selected_customer = $('#select_customer option:selected');--}}
            {{--    if (customerList.includes(add_customer_id)) {--}}
            {{--        alertMsg('error', 'Vui lòng chọn khách hàng chưa có trong danh sách trên', '{{ sc_language_render('priceboard.dupticated_customer') }}');--}}
            {{--    } else {--}}
            {{--        customerList.push(add_customer_id);--}}
            {{--        table_body.append(createRowElement(add_customer_id, selected_customer.text()));--}}
            {{--        updateInputCustomer();--}}
            {{--    }--}}
            {{--});--}}
        });

        function updateCustomerInfo() {
            let List = {"id": [], "dataid": [], "text": []};
            $("#treeview").hummingbird("getChecked", {
                list: List,
                onlyEndNodes: true,
                onlyParents: false,
                fromThis: false
            });
            selectedCustomer = List.dataid
            $('#customer_data').val(selectedCustomer.join('|'));
        }

        function createRowElement(id, name) {
            let html = '<tr><td>' + name + '</td><td><button type="button" onclick="removeRow(this)" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> </button> </td></tr>';
            console.log(html);
            let domElement = htmlToElement(html);
            return domElement;
        }

        function removeRow(element, id) {
            element.parentNode.parentNode.remove();
            customerList.splice(customerList.indexOf(id), 1);
            updateInputCustomer();
        }

        function updateInputCustomer() {
            hidden_input_customer.val(customerList.join('|'));
        }

        function editInit() {
            if (editInitCustomer.length > 0) {
                customerList = editInitCustomer.map(editInitCustomer => editInitCustomer.id);
                updateInputCustomer();
                editInitCustomer.forEach(function (item, index) {
                    table_body.append(createRowElement(item.id, item.name));
                });
            }
        }
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
    </script>
    <script>
        $(document).ready(function () {
            $("#select_product_price_id").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_priceboard.dynamic_search.product_price')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term, // search term
                            page: params.page || 1
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        return {
                            results: data.results,
                            pagination: {
                                more: (params.page * {{ config('pagination.search.default') }}) < data.count_filtered
                            }
                        };
                    },
                }
            });
            $("#select_customer").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_priceboard.dynamic_search.customer')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.results
                        };
                    },
                    cache: true
                }
            });
        });
        $('#price_board_detail').on('click', function() {
            let id = $('#select_product_price_id').val();
            let href = "{{ sc_route_admin('admin_price.detail') }}/" + id;
            window.location.href = href;
        });

        $('#select_product_price_id').on('change', function() {
            $('#display_button_detail').removeClass('display');
        })
    </script>
@endpush