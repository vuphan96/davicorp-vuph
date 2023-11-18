@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description ?? '' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr_5">
                            <a href="{{ sc_route_admin('admin_supplier.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <div class="card-body">
                        <!-- Supplier name -->
                        <div class="form-group  row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('admin.supplier.name') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name"
                                           value="{{ old() ? old('name') : $supplier['name'] ?? '' }}"
                                           class="form-control name" placeholder="" />
                                </div>
                                @if ($errors->has('name'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                    </span>
                                @endif
                                @if (session('exist'))
                                    <span class="form-text" style="color: #dc3545">
                                    <i class="fa fa-info-circle"></i> {{ session('exist') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        <!-- END customer name -->
                        {{-- supplier_code --}}
                        <div class="form-group row kind  {{ $errors->has('supplier_code') ? ' text-red' : '' }}">
                            <label for="supplier_code"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.supplierform.code') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="supplier_code" name="supplier_code"
                                           value="{{ old() ? old('supplier_code') : $supplier['supplier_code'] ?? '' }}"
                                           class="form-control input-sm supplier_code" placeholder="" />
                                </div>
                                @if ($errors->has('supplier_code'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('supplier_code') }}
                                    </span>
                                @else
                                    <span class="form-text">
                                        {{ sc_language_render('product.sku_validate') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    {{-- //supplier_code --}}
                    <!-- supplier address -->
                        <div class="form-group row {{ $errors->has('address') ? ' text-red' : '' }}">
                            <label for="address"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.supplier.address') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="address" type="text" class="form-control" name="address"
                                           value="{{ old() ? old('address') : $supplier['address'] ?? '' }}">
                                </div>
                                @if ($errors->has('address'))
                                    <span class="form-text">{{ $errors->first('address') }}</span>
                                @endif

                            </div>
                        </div>
                        <!-- end supplier address -->
                        <!-- Supplier phone -->
                        <div class="form-group row {{ $errors->has('phone') ? ' text-red' : '' }}">
                            <label for="phone"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.supplier.phone') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                           value="{{ old() ? old('phone') : $supplier['phone'] ?? '' }}" />
                                </div>
                                @if ($errors->has('phone'))
                                    <span class="form-text">{{ $errors->first('phone') }}</span>
                                @endif

                            </div>
                        </div>
                        <!-- end supplier phone -->

                        <!-- Supplier email -->
                        <div class="form-group  row {{ $errors->has('email') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('admin.supplier.email') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="email" id="email" name="email"
                                           value="{{ old() ? old('email') : $supplier['email'] ?? '' }}"
                                           class="form-control name" placeholder="" />
                                </div>
                                @if ($errors->has('email'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END customer mail -->
                        <!-- Supplier name login  supplier -->
                        <div class="form-group  row {{ $errors->has('name_login') ? ' text-red' : '' }}">
                            <label for="name_login"
                                   class="col-sm-2  col-form-label">Tên đăng nhập
                                <span class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*
                                </span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name_login" name="name_login"
                                           value="{{ old() ? old('name_login') : $supplier['name_login'] ?? '' }}"
                                           class="form-control name_login" placeholder="" />
                                </div>
                                @if ($errors->has('name_login'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('name_login') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END Supplier name login supplier -->
                        <!-- Supplier user login supplier -->
                        <div class="form-group  row {{ $errors->has('password') ? ' text-red' : '' }}">
                            <label for="password"
                                   class="col-sm-2  col-form-label">Mật khẩu
                                <span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*
                                </span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password"
                                           value="{{ old('password') ? $supplier['password'] : '' }}"
                                           class="form-control password" placeholder="" />
                                </div>
                                @if ($errors->has('password'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('password') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- END password user login supplier-->
                        <div class="form-group row {{ $errors->has('type_form_report') ? 'text-red' : '' }}">
                            <label for="type_form_report" class="col-sm-2 col-form-label">Chọn mẫu nhập hàng</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <select id="type_form_report" name="type_form_report" class="form-control name">
                                        <option value="1" {{ old('type_form_report') == 1 || ($supplier['type_form_report'] ?? '') == 1 ? 'selected' : '' }}>Mẫu hàng 1</option>
                                        <option value="2" {{ old('type_form_report') == 2 || ($supplier['type_form_report'] ?? '') == 2 ? 'selected' : '' }}>Mẫu hàng 2</option>
                                    </select>
                                </div>
                                <input type="hidden" id="selected_type_form_report" name="selected_type_form_report" value="{{ old('type_form_report') ?? ($supplier['type_form_report'] ?? '') }}">
                                @if ($errors->has('type_form_report'))
                                    <span class="form-text">
                                         <i class="fa fa-info-circle"></i> {{ $errors->first('type_form_report') }}
                                    </span>
                                @endif
                            </div>
                            <script>
                                // Get references to the select element and the hidden input
                                const selectElement = document.getElementById('type_form_report');
                                const hiddenInput = document.getElementById('selected_type_form_report');

                                // Add an event listener to the select element
                                selectElement.addEventListener('change', function () {
                                    // Update the hidden input with the selected value
                                    hiddenInput.value = selectElement.value;
                                });
                            </script>
                        </div>
                        <!-- Status -->
                        <div class="form-group  row">
                            <label for="status"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('admin.supplier.status') }}&nbsp;</label>
                            <div class="col-sm-8" style="margin-top: 7px">
                                <input class="checkbox" type="checkbox" name="status"
                                        {{ old('status', empty($supplier['status']) ? 0 : 1) ? 'checked' : '' }}>
                            </div>
                        </div>
                        <!-- End status -->
                        <i>Các trường có <span style="color: red">*</span> là bắt buộc nhập</i>
                    </div>

                    <!-- /.card-body -->

                    <div class="card-footer row">
                        @csrf
                        <div class="col-sm-2">
                        </div>

                        <div class="col-sm-8">
                            <div class="btn-group float-right">
                                <button data-perm="supplier:edit" type="submit"
                                        class="btn btn-primary">{{ sc_language_render('action.submit') }}</button>
                            </div>

                            <div class="btn-group pull-left">
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
    <style>
        .list {
            padding: 5px;
            margin: 5px;
            border-bottom: 1px solid #dcc1c1;
        }
    </style>
    <!-- Required Stylesheets -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.css"
          rel="stylesheet">
@endpush

@push('scripts')
    <!-- Required Javascript -->
    <script src="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.js">
    </script>
    <script>
        let json = [];
        let customerList = [];
        const currentCategory = JSON.parse('{!! $supplierCategories ?? '[]' !!}');
        const select_customer = $('#select_customer');
        const btn_add_customer = $('#btn_add_customer');
        const hidden_input_customer = $('#customer_data');
        const table_body = $('#customer_table_body');
        const editInitCustomer = JSON.parse('{!! $customerList ?? '[]' !!}');
        let outputList = [];
        let selectedCategory = []

        $(document).ready(function() {
            //Dynamic search
            $("#select_customer").select2({
                ajax: {
                    url: "{{ sc_route_admin('admin_priceboard.dynamic_search.customer') }}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            keyword: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response.results
                        };
                    },
                    cache: true
                }
            });
            // Init Customer chosen
            editInit();
            btn_add_customer.click(function() {
                let add_customer_id = select_customer.val();
                let selected_customer = $('#select_customer option:selected');
                if (customerList.includes(add_customer_id)) {
                    alertMsg('error', 'Vui lòng chọn khách hàng chưa có trong danh sách trên',
                        '{{ sc_language_render('priceboard.dupticated_customer') }}');
                } else {
                    customerList.push(add_customer_id);
                    table_body.append(createRowElement(add_customer_id, selected_customer.text()));
                    updateInputCustomer();
                }
            });
            // Treeview js
            $("#treeview").hummingbird();
            $("#treeview").hummingbird("expandAll");
            $("#treeview").hummingbird("checkNode", {
                sel: "data-id",
                vals: currentCategory
            });
            updateCategoryInfo();
            $("#treeview").on("click", function() {
                updateCategoryInfo();
            });

        });

        function updateCategoryInfo() {
            let List = {
                "id": [],
                "dataid": [],
                "text": []
            };
            $("#treeview").hummingbird("getChecked", {
                list: List,
                onlyEndNodes: false,
                onlyParents: false,
                fromThis: false
            });
            selectedCategory = List.dataid
            $('#category_data').val(selectedCategory.join('|'));
        }

        function createRowElement(id, name) {
            let html = '<tr><td>' + name +
                '</td><td><button type="button" onclick="removeRow(this)" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> </button> </td></tr>';
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
                editInitCustomer.forEach(function(item, index) {
                    table_body.append(createRowElement(item.id, item.name));
                });
            }
        }
    </script>
@endpush
