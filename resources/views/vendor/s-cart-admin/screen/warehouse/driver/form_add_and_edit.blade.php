@extends($templatePathAdmin . 'layout')
@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description ?? '' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('driver.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $driver['id'] ?? '' }}">
                    <div class="card-body">
                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title"></h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Tên nhân viên
                                        &nbsp;<span class="required-icon"
                                                    title="{{ sc_language_render('note.required-field') }}">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="text" id="full_name"
                                                   name="full_name"
                                                   value="{{ old() ? old('full_name') : $driver['full_name'] ?? '' }}"
                                                   class="form-control {{ 'title' }}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('full_name'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('full_name') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Số điện thoại
                                        <span class="required-icon"
                                              title="{{ sc_language_render('note.required-field') }}">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="text" id="phone"
                                                   name="phone"
                                                   value="{{ old() ? old('phone') : $driver['phone'] ?? '' }}"
                                                   class="form-control {{ 'title' }}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('phone'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('phone') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Email
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="text" id="email"
                                                   name="email"
                                                   value="{{ old() ? old('email') : $driver['email'] ?? '' }}"
                                                   class="form-control {{ 'title' }}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('email'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('email') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Địa chỉ
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="text" id="address"
                                                   name="address"
                                                   value="{{ old() ? old('address') : $driver['address'] ?? '' }}"
                                                   class="form-control {{'title' }}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('address'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('address') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Tên đăng nhập
                                        <span class="required-icon"
                                              title="{{ sc_language_render('note.required-field') }}">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="text" id="login_name"
                                                   name="login_name"
                                                   value="{{ old() ? old('login_name') : $driver['login_name'] ?? '' }}"
                                                   class="form-control {{ 'title' }}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('login_name'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('login_name') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div
                                        class="form-group row  {{ $errors->has('title') ? ' text-red' : '' }}">
                                    <label for="title"
                                           class="col-sm-2 col-form-label">Mật khẩu
                                        <span class="required-icon"
                                              title="{{ sc_language_render('note.required-field') }}">*</span>
                                    </label>
                                    <div class="col-sm-8">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                            </div>
                                            <input type="password" id="password"
                                                   name="password"
                                                   value="{{ old() ? old('password') : ''}}"
                                                   class="form-control {{'title'}}" placeholder=""/>
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="form-text">
                                                        <i class="fa fa-info-circle"></i> {{ $errors->first('password') }}
                                                    </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="status"
                                           class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.status') }}</label>
                                    <div class="col-sm-8" style="margin-top: 7px">
                                        <input class="checkbox" type="checkbox" name="status" id="status"
                                                {{ old('status', empty($driver['status']) ? 0 : 1) ? 'checked' : '' }}>
                                    </div>
                                </div>
                                {{--Check nếu tồn tại giá trị driver =>edit thì mới hiển thị--}}
                                @if(isset($driver))
                                    <div class="form-group row">
                                        <label for="status" class="col-sm-2 col-form-label">Danh sách khách hàng</label>
                                        <div class="input-group col-sm-8">
                                            <div class="form-group col-sm-12">
                                                <label>Đơn hàng đợt 1:</label>
                                                <div class="input-group col-sm-12">
                                                    <div class="form-group col-sm-12">
                                                        @foreach($list_customer_type1 as $key => $customer_type1)
                                                            <label class="col-sm-10 bordered-element-1"
                                                                   data-id="{{$customer_type1['id']}}"
                                                                   data-name="{{$customer_type1['name']}}">{{$customer_type1['name']}}</label>
                                                            <span class="icon-delete col-sm-12"
                                                                  onclick="deleteCustomer1(this)"
                                                                  data-id="{{$customer_type1['id']}}"><i
                                                                        class="fas fa-trash-alt"></i></span>
                                                        @endforeach
                                                        <select id="customer_list1" onchange="checkData1($(this))"
                                                                style="width: 500px" class="form-control rounded-0"
                                                                name="customer_list1[]" multiple="multiple">
                                                            {!! $optionCustomer1!!}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-12">
                                                <label>Đơn hàng đợt 2:</label>
                                                <div class="input-group col-sm-12">
                                                    <div class="form-group col-sm-12">
                                                        @foreach($list_customer_type2 as $key => $customer_type2)
                                                            <label class="col-sm-10 bordered-element-2"
                                                                   data-id="{{$customer_type2['id']}}"
                                                                   data-name="{{$customer_type2['name']}}">{{$customer_type2['name']}}</label>
                                                            <span class="icon-delete-1 col-sm-12"
                                                                  onclick="deleteCustomer2(this)" id="deselect-value"><i
                                                                        class="fas fa-trash-alt"></i></span>
                                                        @endforeach
                                                        <select id="customer_list2" onchange="checkData2($(this))"
                                                                style="width: 500px" class="form-control rounded-0"
                                                                name="customer_list2[]" multiple="multiple">
                                                            {!! $optionCustomer2!!}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{--end check nếu tồn tại giá trị driver =>edit thì mới hiển thị--}}
                                    <div class="form-group row">
                                        <label for="status" class="col-sm-2 col-form-label">Danh sách khách hàng</label>
                                        <div class="input-group col-sm-8">
                                            <div class="form-group col-sm-12">
                                                <label>Đơn hàng đợt 1:</label>
                                                <div class="input-group col-sm-12">
                                                    <select id="customer_list1" style="width: 500px"
                                                            class="form-control rounded-0" name="customer_list1[]"
                                                            multiple="multiple">
                                                        {!! $optionCustomer !!}
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group col-sm-12">
                                                <label>Đơn hàng đợt 2:</label>
                                                <div class="input-group col-sm-12">
                                                    <select id="customer_list2" style="width: 500px"
                                                            class="form-control rounded-0" name="customer_list2[]"
                                                            multiple="multiple">
                                                        {!! $optionCustomer !!}
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- Status -->
                    <!-- End Status -->
                    <!-- /.card-body -->
                    <div class="card-footer row" id="card-footer">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="submit" data-perm=""
                                        class="btn btn-primary"
                                        id="btnSave">{{ sc_language_render('action.submit') }}</button>
                            </div>
                            <div class="btn-group float-left">
                                <button type="reset"
                                        onclick="reloadPage()"
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
    <link rel="stylesheet" type="text/css"
          href="{{ asset("admin/plugin/bootstrap-multiselect/css/bootstrap-multiselect.min.css") }}"/>\
    <style>
        .bordered-element-1 {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            margin: 1px;
            bottom: 1px;
        }

        .bordered-element-2 {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            margin: 1px;
            bottom: 1px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>
    <script src="{{ asset("admin/plugin/bootstrap-multiselect/js/bootstrap-multiselect.min.js") }}"></script>
    <script>
        function reloadPage() {
            location.reload();
        }
        $(document).ready(function () {
            $('#vi__title').on('change', function () {
                let title = $('#vi__title').val();
                $('#alias').val(getSlug(title));
            });
        $('#btnSave').on("click", function () {
            $.ajax({
                method: 'POST',
                url: '{{sc_route_admin('driver.create')}}',
                data: function (params) {
                return {
                    keyword: params.term // search term
                };
                },
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (response) {
                    if (response.success) {
                        window.location.href = "{{ route('driver.index') }}";
                    } else {
                        alertJs('error', 'Lỗi lưu dữ liệu: ' + response.message);
                    }
                },
                complete: function () {
                    $('#loading').hide();
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 5000;
            }
            $('#printDialog').on('hidden.bs.modal', function (e) {
                $('#print_ids').val('');
            })
            $('#customer_list1').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                filterPlaceholder: 'Tìm theo khách hàng',
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                selectAllText: 'Chọn tất cả!',
                maxHeight: 400,
                width: 1000,
                dropUp: true,
                includeResetOption: true,
                resetText: "Đặt lại"
            });
            $('#customer_list2').multiselect({
                enableFiltering: true,
                enableCaseInsensitiveFiltering: true,
                filterPlaceholder: 'Tìm theo khách hàng',
                includeSelectAllOption: true,
                selectAllJustVisible: true,
                selectAllText: 'Chọn tất cả!',
                maxHeight: 400,
                dropUp: true,
                includeResetOption: true,
                resetText: "Đặt lại"
            });
        });

        // select khách hàng đợt 1
        function deleteCustomer1(btn) {
            var customerId1 = $(btn).prev('label').data('id');
            $(btn).prev('label').remove();
            $(btn).remove();
            $('#customer_list1').multiselect('deselect', customerId1, true);
        }

        $(document).ready(function () {
            $('#customer_list1').multiselect();
            $('#deselect-value').on('click', function () {
                deleteCustomer1(this);
            });
        });

        function checkData1(e) {
            var selectedCustomerIds = e.val();
            $list_customer_type1 = [];
            selectedCustomerIds.forEach(function (customerId) {
                var customerName = $('#customer_list1 option[value="' + customerId + '"]').text();
                $list_customer_type1.push({id: customerId, name: customerName});
            });
            updateCustomerList1Display();
        }

        function updateCustomerList1Display() {
            $('.bordered-element-1[data-id]').remove();
            $('.icon-delete').remove();
            $list_customer_type1.forEach(function (customer) {
                var label = $('<label>')
                    .addClass('col-sm-10 bordered-element-1')
                    .attr('data-id', customer.id)
                    .attr('data-name', customer.name)
                    .text(customer.name);
                var iconDelete = $('<span>')
                    .addClass('icon-delete col-sm-4')
                    .attr('data-id', customer.id)
                    .click(function () {
                        deleteCustomer1(this);
                    })
                    .html('<i class="fas fa-trash-alt"></i>');
                $('#customer_list1').before(label, iconDelete);
            });
        }

        // select khách hàng đợt 2
        function deleteCustomer2(btn) {
            var customerId2 = $(btn).prev('label').data('id');
            $(btn).prev('label').remove();
            $(btn).remove();
            $('#customer_list2').multiselect('deselect', customerId2, true);
        }

        $(document).ready(function () {
            $('#customer_list2').multiselect();
            $('#deselect-value').on('click', function () {
                deleteCustomer1(this);
            });
        });

        function checkData2(e) {
            var selectedCustomerIds = e.val();
            $list_customer_type2 = [];
            selectedCustomerIds.forEach(function (customerId) {
                var customerName = $('#customer_list2 option[value="' + customerId + '"]').text();
                $list_customer_type2.push({id: customerId, name: customerName});
            });
            updateCustomerList2Display();
        }

        function updateCustomerList2Display() {
            $('.bordered-element-2[data-id]').remove();
            $('.icon-delete-1').remove();
            $list_customer_type2.forEach(function (customer) {
                var label = $('<label>')
                    .addClass('col-sm-10 bordered-element-2')
                    .attr('data-id', customer.id)
                    .attr('data-name', customer.name)
                    .text(customer.name);
                var iconDelete = $('<span>')
                    .addClass('icon-delete-1 col-sm-4')
                    .attr('data-id', customer.id)
                    .click(function () {
                        deleteCustomer1(this);
                    })
                    .html('<i class="fas fa-trash-alt"></i>');
                $('#customer_list2').before(label, iconDelete);
            });
        }

    </script>
@endpush
