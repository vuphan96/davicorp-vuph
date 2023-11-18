@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-sm-{{ $is_edit ? 6 : 12 }}">
            <div class="card">
                <div class="card-header d-flex flex-row align-items-center">
                    <h2 class="card-title">Thông tin khách hàng</h2>
                    <div class="card-tools ml-auto p-2">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_customer.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <!-- /.card-body -->
                    <div class="card-body">

                        <input type="hidden" name="id" value="{{$customer['id'] ?? ''}}">

                        <!-- Customer name -->
                        <div class="form-group  row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name" class="col-sm-3  col-form-label">{{ sc_language_render('customer.name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name"
                                           value="{{ old('name', $customer['name'] ?? '' )}}" class="form-control name"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('name'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = customer name -->
                        <!-- Customer short_name -->
                        <div class="form-group  row {{ $errors->has('short_name') ? ' text-red' : '' }}">
                            <label for="short_name" class="col-sm-3  col-form-label">{{ sc_language_render('customer.short_name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="short_name" name="short_name"
                                           value="{{ old('short_name', $customer['short_name'] ?? '' )}}" class="form-control short_name"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('short_name'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('short_name') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = short_name -->
                        <!-- customer code -->
                        <div class="form-group  row {{ $errors->has('customer_code') ? ' text-red' : '' }}">
                            <label for="customer_code"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.admin.customer_code') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="customer_code" name="customer_code"
                                           value="{{ old('customer_code', $customer['customer_code'] ?? '' )}}"
                                           class="form-control name" placeholder="" required/>
                                </div>
                                @if ($errors->has('customer_code'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('customer_code') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = customer code -->
                        <!-- schoolmaster code -->
                        <div class="form-group  row {{ $errors->has('schoolmaster_code') ? ' text-red' : '' }}">
                            <label for="schoolmaster_code"
                                   class="col-sm-3  col-form-label">Tên đăng nhập TK hiệu trưởng
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="schoolmaster_code" name="schoolmaster_code"
                                           value="{{ old('schoolmaster_code', $customer['schoolmaster_code'] ?? '' )}}"
                                           class="form-control name" placeholder="" required/>
                                </div>
                                @if ($errors->has('schoolmaster_code'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('schoolmaster_code') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = schoolmaster code -->
                        <!-- schoolmaster_password -->
                        <div class="form-group  row {{ $errors->has('schoolmaster_password') ? ' text-red' : '' }} changepass">
                            <label for="schoolmaster_password"
                                   class="col-sm-3  col-form-label">Mật khẩu TK hiệu trưởng {!! $is_edit ? '' : '<span class="required-icon" title="' . sc_language_render('note.required-field') .'">*</span>'!!}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="schoolmaster_password" name="schoolmaster_password" value=""
                                           class="form-control schoolmaster_password" placeholder="" {{$is_edit ? '' : 'required'}}/>
                                </div>
                                <span class="form-text" {{$is_edit ? '' : 'hidden'}}>
                                   <i class="fas fa-info-circle"></i>&nbsp;{!! sc_language_render('customer.admin.keep_password') !!}
                                                 </span>
                                @if ($errors->has('schoolmaster_password'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('schoolmaster_password') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- schoolmaster_password -->
                        <!-- Customer mail -->
                        <div class="form-group  row {{ $errors->has('email') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.email') }}
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $customer['email'] ?? '') ?? '' }}"
                                           class="form-control name" placeholder=""/>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('email') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- END customer mail -->
                        <!-- customer phone -->
                        <div class="form-group row {{ $errors->has('phone') ? ' text-red' : '' }}">
                            <label for="phone"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.phone') }}
                                &nbsp</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="phone" type="text" class="form-control" name="phone"
                                           value="{{ (old('phone', $customer['phone'] ?? ''))}}">
                                </div>
                                @if($errors->has('phone'))
                                    <span class="form-text">{{ $errors->first('phone') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- end customer phone -->
                        <!-- department -->
                        <div class="form-group row {{ $errors->has('department_id') ? ' text-red' : '' }}">
                            <label for="department_id"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.department') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="department_id" class="select2 select_department">
                                        <option value=""
                                                readonly>{{ sc_language_render('product.admin.department_label') }}</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id ?? ''}}" {{ $customer ? (($department->id == $customer->department_id) ? 'selected' : '') : ''}}>{{ $department->name ?? sc_language_render('common.no_data') }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($errors->has('department_id'))
                                    <span class="form-text">{{ $errors->first('department_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- = department -->
                        <!-- tier -->
                        <div class="form-group row {{ $errors->has('tier_id') ? ' text-red' : '' }}">
                            <label for="tier_id"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.tier') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="tier_id" class="select2" required>
                                        <option readonly
                                                value="">{{ sc_language_render('product.admin.tier_select_label') }}</option>
                                        @foreach($tiers as $tier)
                                            <option value="{{ $tier->id ?? '' }}" @if($customer)
                                                {{ (($customer->getTierId()) == $tier->id) ? 'selected' : ''}}
                                                    @endif>{{ $tier->name ?? 'Lỗi dữ liệu hạng' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($errors->has('tier_id'))
                                    <span class="form-text">{{ $errors->first('tier_id') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- = tier -->
                        <!-- tax code -->
                        <div class="form-group row {{ $errors->has('tax_code') ? ' text-red' : '' }}">
                            <label for="tax_code"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.tax_code') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="tax_code" type="text" class="form-control" name="tax_code"
                                           value="{{ (old('tax_code', $customer['tax_code'] ?? ''))}}">
                                </div>
                                @if($errors->has('tax_code'))
                                    <span class="form-text">{{ $errors->first('tax_code') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- = tax_code -->
                        <!-- Customer no -->
                        <div class="form-group  row {{ $errors->has('order_num') ? ' text-red' : '' }}">
                            <label for="no" class="col-sm-3  col-form-label">{{ sc_language_render('customer.no') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="order_num" name="order_num"
                                           value="{{ old('order_num', $customer['order_num'] ?? '' )}}" class="form-control no"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('order_num'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('order_num') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = Customer no -->
                        <!-- Customer route -->
                        <div class="form-group  row {{ $errors->has('route') ? ' text-red' : '' }}">
                            <label for="route" class="col-sm-3  col-form-label">{{ sc_language_render('customer.route') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="route" name="route"
                                           value="{{ old('route', $customer['route'] ?? '' )}}" class="form-control route"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('route'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('route') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = Customer route -->
                        <!-- customer address -->
                        <div class="form-group row {{ $errors->has('address') ? ' text-red' : '' }}">
                            <label for="address"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.address') }}</label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input id="address" type="text" class="form-control" name="address"
                                           value="{{ (old('address', $customer['address'] ?? ''))}}">
                                </div>
                                @if($errors->has('address'))
                                    <span class="form-text">{{ $errors->first('address') }}</span>
                                @endif

                            </div>
                        </div>
                        <!-- = customer address -->
                        <!-- zone -->
                        <div class="form-group row {{ $errors->has('zone_id') ? ' text-red' : '' }}">
                            <label for="zone_id"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.zone') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="zone_id" class="select2 select_zone" required>
                                        <option value=""
                                                readonly="">{{ sc_language_render('product.admin.zone_label') }}</option>
                                        @isset($customer->zone_id)
                                            <option value="{{ $customer->zone_id ?? ''}}"
                                                    selected>{{ $customer->zone->name ?? 'Khu vực bị xoá vui lòng chọn khu vực khác' }}</option>
                                        @endisset
                                    </select>
                                </div>
                                @if($errors->has('zone_id'))
                                    <span class="form-text">{{ $errors->first('zone_id') }}</span>
                                @endif

                            </div>
                        </div>
                        <!-- = zone -->
                        <!-- password -->
                        <div class="form-group  row {{ $errors->has('password') ? ' text-red' : '' }} changepass">
                            <label for="password"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.password') }} {!! $is_edit ? '' : '<span class="required-icon" title="' . sc_language_render('note.required-field') .'">*</span>'!!}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password" value=""
                                           class="form-control password" placeholder="" {{$is_edit ? '' : 'required'}}/>
                                </div>
                                <span class="form-text" {{$is_edit ? '' : 'hidden'}}>
                                   <i class="fas fa-info-circle"></i>&nbsp;{!! sc_language_render('customer.admin.keep_password') !!}
                                                 </span>
                                @if ($errors->has('password'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('password') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- password -->
                        <!-- Retype password -->
                        <div class="form-group  row {{ $errors->has('password_confirmation') ? ' text-red' : '' }}" {{$is_edit ? 'hidden' : ''}}>
                            <label for="password_confirmation"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.password_confirm') }}
                                &nbsp;{!! $is_edit ? '' : '<span class="required-icon" title="' . sc_language_render('note.required-field') .'">*</span>' !!}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           value="" class="form-control password_confirmation"
                                           placeholder="" {{$is_edit ? '' : 'required'}} />
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <span class="form-text">
                                                    <i class="fa fa-info-circle"></i> {{ $errors->first('password_confirmation') }}
                                                </span>
                                @endif
                            </div>
                        </div>
                        <!-- = Retype password -->
                        <!-- type customer -->
                        <div class="form-group row {{ $errors->has('kind') ? ' text-red' : '' }}">
                            <label for="kind"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('admin.customer.type_customer') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select name="kind" class="select2">
                                        <option value="0" {{ isset($customer->kind) ? (($customer->kind == 0) ? 'selected' : '') : '' }}>{{ sc_language_render('admin.customer.normal') }}</option>
                                        <option value="1" {{ isset($customer->kind) ? (($customer->kind == 1) ? 'selected' : '') : '' }}>{{ sc_language_render('admin.customer.company') }}</option>
                                        <option value="2" {{ isset($customer->kind) ? (($customer->kind == 2) ? 'selected' : '') : '' }}>{{ sc_language_render('admin.customer.school') }}</option>
                                        <option value="3" {{ isset($customer->kind) ? (($customer->kind == 3) ? 'selected' : '') : '' }}>Khác</option>
                                    </select>
                                </div>
                                @if($errors->has('kind'))
                                    <span class="form-text">{{ $errors->first('kind') }}</span>
                                @endif
                            </div>
                        </div>
                        <!-- = type customer -->
                        <!-- teacher code -->
                        <div class="form-group row {{ $errors->has('teacher_code') ? ' text-red' : '' }}">
                            <label for="teacher_code"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.admin.teacher_code') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="teacher_code" name="teacher_code"
                                           value="{{ old('teacher_code', $customer['teacher_code'] ?? '' )}}"
                                           class="form-control name" placeholder=""/>
                                </div>
                                @if ($errors->has('teacher_code'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('teacher_code') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- == teacher code -->

                        <!-- student code -->
                        <div class="form-group  row {{ $errors->has('student_code') ? ' text-red' : '' }}">
                            <label for="student_code"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.admin.student_code') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="student_code" name="student_code"
                                           value="{{ old('student_code', $customer['student_code'] ?? '' )}}"
                                           class="form-control name" placeholder=""/>
                                </div>
                                @if ($errors->has('student_code'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('student_code') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- == student code -->
                        <!-- Status -->
                        <div class="form-group  row">
                            <label for="status"
                                   class="col-sm-3 col-form-label">{{ sc_language_render('customer.status') }}</label>
                            <div class="col-sm-8">
                                <input class="checkbox" type="checkbox"
                                       name="status" {{ old('status',(empty($customer['status'])?0:1))?'checked':''}}>

                            </div>
                        </div>
                        <!-- = Status -->

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer row" style="background-color: transparent">
                        @csrf
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button data-perm="{{isset($data_perm_submit)?$data_perm_submit:''}}" type="submit"
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
        @if($is_edit)
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header d-flex flex-row align-items-center">
                        <div class="card-tools float-right w-100">
                            <div class="card-title">
                                <h5>Danh sách sản phẩm</h5>
                                <form class="btn-group float-right">
                                    <div class="form-row">
                                        <div class="col-4 mb-1">
                                            <select name="supplier_search" data-attribute="supplier"
                                                    class="form-control select2 select_supplier" id="select_supplier">
                                                <option value="">Chọn nhà cung cấp</option>
                                                <option value="{{ $currentSearchSupplier->id ?? '' }}"
                                                        selected>{{ $currentSearchSupplier->name ?? 'Tất cả nhà cung cấp' }}</option>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <input name="product_search" type="text" class="form-control" placeholder="Tên sản phẩm" value="{{ request('product_search') }}">
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-outline-primary" title="Tìm theo nội dung bộ lọc"><i class="fa fa-filter"></i></button>
                                            <button type="button" onclick="clearFilter()" class="btn btn-outline-danger" title="Xoá bộ lọc"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('admin_customer.product_edit', ['id' => $customer->id]) }}"
                                  method="post"
                                  class="col-md-12" id="product-form">
                                @csrf
                                <div class="mb-3">
                                    <button  data-perm="customer:edit" class="btn btn-sm btn-success"
                                             onclick="document.getElementById('product-form').submit()"><i class="fa fa-save"></i>&nbsp;Lưu
                                    </button>
                                    <button  data-perm="customer:edit" type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#product-modal">
                                        <i class="fa fa-plus"></i> Thêm sản phẩm
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                        <tr>
                                            <th style="width: 42%">Tên sản phẩm</th>
                                            <th style="width: 42%">Nhà cung cấp</th>
                                            <th style="width: 16px; text-align: center">Thao tác</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($customerProducts as $item)
                                            <tr>
                                                <td>
                                                    <select name="products[{{ $item->id }}]" data-attribute="product"
                                                            class="form-control select2 select_product">
                                                        <option value="" readonly>Chọn sản phẩm</option>
                                                        <option value="{{ $item->product_id }}"
                                                                selected>{{ $item->product->name ?? '---' }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="suppliers[{{ $item->id }}]" data-attribute="supplier"
                                                            class="form-control select2 select_supplier">
                                                        <option value="" readonly>Chọn nhà cung cấp</option>
                                                        <option value="{{ $item->supplier_id }}"
                                                                selected>{{ $item->supplier->name ?? '---' }}</option>
                                                    </select>
                                                </td>
                                                <td style="text-align: center"><a  data-perm="customer:edit" href="{{ route('admin_customer.product_remove', ['id' => $item->id])}}" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i> </a> </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                        @if(!empty(session()->get('product_error')))
                            <span class="form-text text-red mb-3">
                        <i class="fa fa-asterisk"></i> {!! session()->get('product_error') !!}
                    </span>
                        @endif
                        {{ $customerProducts->appends(request()->input())->links() }}
                        @if($customerProducts->total() > 0)
                            <span class="pagination-sm">Hiển thị {{ $customerProducts->firstItem() }} đến {{ $customerProducts->lastItem() }} trong tổng số {{ $customerProducts->total() }} sản phẩm</span>
                        @else
                            <span class="pagination-sm">Không có kết quả</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
    <!-- Modal -->
    @if($is_edit)
        <div class="modal fade" id="product-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <form class="modal-content" method="post" action="{{ route('admin_customer.product_add', ['id' => $customer->id ?? '']) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thêm sản phẩm</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="product_id" class="col-4 col-form-label">Sản phẩm</label>
                            <div class="col-8">
                                <div class="input-group">
                                    <select name="product_id" data-attribute="product"
                                            class="form-control select2 select_product" required>
                                        <option value="">Chọn sản phẩm</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="supplier_id" class="col-4 col-form-label">Nhà cung cấp</label>
                            <div class="col-8">
                                <div class="input-group">
                                    <select name="supplier_id"
                                            class="form-control select2 select_supplier" required>
                                        <option value="">Chọn nhà cung cấp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tạo mới</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection

@push('styles')
    <style>
        .list {
            padding: 1rem;
            margin: 5px;
            border: 1px solid #dcc1c1;
            border-radius: 8px;
            max-height: 350px;
            overflow-x: hidden;
            overflow-y: auto;
        }

        .input-group > .select2-container--bootstrap, .input-group > .select2-container--default {
            width: auto;
            flex: 1 1 auto;
        }

        .input-group > .select2-container--bootstrap .select2-selection--single,
        .input-group > .select2-container--default .select2-selection--single,
        .input-group > .select2-container--bootstrap .select2-selection--multiple,
        .input-group > .select2-container--default .select2-selection--multiple {
            height: 100%;
            line-height: inherit;
            border-radius: 4px 0 0 4px;
        }

        .input-group > .select2-container--bootstrap .select2-selection--single .select2-selection__rendered,
        .input-group > .select2-container--default .select2-selection--single .select2-selection__rendered,
        .input-group > .select2-container--bootstrap .select2-selection--multiple .select2-selection__rendered,
        .input-group > .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            height: inherit;
            display: -webkit-box !important;
            display: -ms-flexbox !important;
            display: flex !important;
            -webkit-box-align: center !important;
            -ms-flex-align: center !important;
            align-items: center !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function initSelect2() {
            //Dynamic search
            $(".select_product").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.product')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            page: params.page || 1
                        }
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
                    cache: true
                }
            });

            $("#select_supplier").select2({
                allowClear: true,
                placeholder: "Tìm theo nhà cung cấp"
            })

            //Dynamic search
            $(".select_supplier").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.supplier')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            page: params.page || 1
                        }
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
                    cache: true
                }
            });

            $(".select_zone").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.zone')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            page: params.page || 1
                        }
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
                    cache: true
                }
            });
        }

        //Dynamic search
        $(document).ready(function () {
            initSelect2();
        });
        function clearFilter(){
            $("#select_supplier").val('').trigger('change');
            $("input[name='product_search']").val('').trigger('change');
        }
    </script>
@endpush
