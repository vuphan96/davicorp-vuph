@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-sm-{{ $is_edit ? 6 : 12 }}">
            <div class="card">
                <div class="card-header d-flex flex-row align-items-center">
                    <h2 class="card-title">Thông tin khách hàng</h2>
                    <div class="card-tools ml-auto p-2">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin.davicook_customer.index') }}"
                               class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                @if(isset($method))
                    @method('PUT')
                @endif
                @csrf
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
                                           placeholder=""/>
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
                            <label for="short_name"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.short_name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="short_name" name="short_name"
                                           value="{{ old('short_name', $customer['short_name'] ?? '' )}}"
                                           class="form-control short_name"
                                           placeholder=""/>
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
                                           class="form-control name" placeholder=""/>
                                </div>
                                @if ($errors->has('customer_code'))
                                    <span class="form-text">
            <i class="fa fa-info-circle"></i> {{ $errors->first('customer_code') }}
        </span>
                                @endif
                            </div>
                        </div>
                        <!-- = customer code -->
                        <!-- serving price -->
                        <div class="form-group  row {{ $errors->has('serving_price') ? ' text-red' : '' }}">
                            <label for="serving_price"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.admin.serving_price') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" step="0.01" id="serving_price" name="serving_price"
                                           value="{{ old('serving_price', $customer['serving_price'] ?? '' )}}"
                                           class="form-control name" placeholder="" min="0" required/>
                                </div>
                                @if ($errors->has('serving_price'))
                                    <span class="form-text">
            <i class="fa fa-info-circle"></i> {{ $errors->first('serving_price') }}
        </span>
                                @endif
                            </div>
                        </div>
                        <!-- = serving price -->
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
                                           value="{{ old('order_num', $customer['order_num'] ?? '' )}}"
                                           class="form-control no"
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
                            <label for="route"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.route') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="route" name="route"
                                           value="{{ old('route', $customer['route'] ?? '' )}}"
                                           class="form-control route"
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
                                            {{--                                            {{ dd($customer->zone->name) }}--}}
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
                        <!-- Status -->
                        <div class="form-group  row">
                            <label for="status"
                                   class="col-sm-3  col-form-label">{{ sc_language_render('customer.status') }}</label>
                            <div class="col-sm-8">
                                <input class="checkbox" type="checkbox"
                                       name="status" {{ old('status',(empty($customer['status'])?0:1))?'checked':''}}>

                            </div>
                        </div>
                        <!-- = Status -->

                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer row" style="background-color: transparent">
                        {{--                        @csrf--}}
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="submit"  data-perm="{{isset($data_perm_submit)?$data_perm_submit:''}}"
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
                                            <input name="product_search" type="text" class="form-control"
                                                   placeholder="Tên sản phẩm" value="{{ request('product_search') }}">
                                        </div>
                                        <div class="col-4">
                                            <button class="btn btn-outline-primary" title="Tìm theo nội dung bộ lọc"><i
                                                        class="fa fa-filter"></i></button>
                                            <button type="button" onclick="clearFilter()" class="btn btn-outline-danger"
                                                    title="Xoá bộ lọc"><i class="fa fa-times-circle"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <form action="{{ route('admin.davicook_customer.update_product_supplier', ['id' => $customer->id]) }}"
                                  method="post"
                                  class="col-md-12" id="product-form">
                                @csrf
                                <div class="mb-3">
                                    <button class="btn btn-sm btn-success" data-perm="davicook_customer:edit"
                                            onclick="document.getElementById('product-form').submit()"><i
                                                class="fa fa-save"></i>&nbsp;Lưu
                                    </button>
                                    <button data-perm="davicook_customer:edit" type="button"
                                            class="btn btn-sm btn-success" data-toggle="modal"
                                            data-target="#product-modal">
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
                                                        <option value="{{ $item->product_id ?? '' }}"
                                                                selected>{{ $item->product ? $item->product->getName() : 'Sản phẩm đã bị xóa' }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="suppliers[{{ $item->id }}]" data-attribute="supplier"
                                                            class="form-control select2 select_supplier">
                                                        <option value="" readonly>Chọn nhà cung cấp</option>
                                                        <option value="{{ $item->supplier_id }}"
                                                                selected>{{ $item->supplier ? $item->supplier->name : 'Nhà cung cấp đã bị xóa' }}</option>
                                                    </select>
                                                </td>
                                                <td style="text-align: center"><a data-perm="davicook_customer:edit"
                                                                                  href="{{ route('admin.davicook_customer.remove_product', ['id' => $item->id])}}"
                                                                                  class="btn btn-sm btn-outline-danger"><i
                                                                class="fa fa-trash"></i> </a></td>
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
    {{--    list định lượng và món ăn--}}
    @if($is_edit)
        <div class="row dish-list">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-th-list"></i> {!! $title_dish ?? '' !!}</h3>
                        <div class="card-header with-border">
                            <div class="card-tools">
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
                                    <div class="menu-left" style="margin-left: -20px;">
                                        <button type="button" class="btn btn-default grid-select-all"><i
                                                    class="far fa-square"></i></button>
                                    </div>
                                    <div class="menu-left">
<span class="btn btn-flat btn-danger grid-trash" data-perm="davicook_customer:edit"
      title="{{ sc_language_render('action.delete') }}"><i
            class="fas fa-trash-alt"></i></span>
                                    </div>
                                @endif

                            </div>

                        </div>
                    </div>

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
                                    <tbody>
                                    @foreach ($dataTr as $keyRow => $tr)
                                        <tr>
                                            @if (!empty($removeList))
                                                <td style="padding-left: 12px; text-align: center">
                                                    <input class="checkbox grid-row-checkbox" type="checkbox"
                                                           data-id="{{ $keyRow }}">
                                                </td>
                                            @endif
                                            @foreach ($tr as $key => $trtd)
                                                <td style="{!! $cssTd[$key] ?? '' !!}">{!! $trtd !!}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                <div class="block-pagination clearfix m-10">
                                    <div class="ml-3 float-left">
                                        {!! $resultItems ?? '' !!}
                                    </div>
                                    <div class="pagination pagination-sm mr-3 float-right">
                                        {!! $pagination ?? '' !!}
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>


                </div>
            </div>
        </div>
        {{--  ===  list định lượng và món ăn--}}


        <!-- Modal -->
        <div class="modal fade" id="product-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">

            {{-- Thêm nhà cung cấp --}}
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <form class="modal-content" method="post"
                      action="{{ route('admin.davicook_customer.add_product_supplier', ['id' => $customer->id ?? '']) }}">
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
        {{-- /Thêm nhà cung cấp --}}


        {{-- Thêm món ăn --}}
        <div class="modal fade" id="dish-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1100px;">
                <form class="modal-content form-add-element-dish" method="post"
                      action="javascript:void(0)"
                      multiple="" style="height:500px;">
                    @csrf
                    <input type="hidden" value="{{  $customer['id'] ?? '' }}" name="customer_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Thêm món ăn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll; max-height:400px;">
                        <div class="form-group row">
                            <div style="min-width: 10px"></div>
                            <label for="product_id" class="ingredient_dish_name col-form-label">Chọn món ăn:</label>
                            <div class="col-3">
                                <div class="input-group" id="js-select-dish">
                                    <select name="dish_id" data-attribute="product"
                                            class="form-control select2 select_dish" required>
                                        <option value="">Chọn món ăn</option>
                                    </select>
                                </div>
                                @if ($errors->has('dish_id'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('dish_id') }}
                                    </span>
                                @endif
                            </div>
                            <div class="col-3">
                                <div class="input-group">
                                    <select name="is_export_menu"
                                            class="form-control " required style="height: 44px; border-radius: 0px; max-width: 200px">
                                        <option value="1">Xuất định lượng</option>
                                        <option value="0">Không xuất định lượng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3" style="margin-left: 30px;">
                                <div class="btn-group">
                                    <label for="product_id" class="col-form-label">Tổng cost món ăn:</label>
                                    <h6 class="sum-total-cost" data-sum_total_cost_id="0" style="font-weight: bold; margin: 10px 10px;">
                                        {{ sc_currency_render($sum_total_cost ?? 0, 'vnd') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 10px"></div>
                            <label for="product_id" class="ingredient_dish_name ingredient_cooked_dish_name col-form-label">Định lượng chín:</label>
                            <div class="col-5">
                                <div class="input-group" id="js-select-dish">
                                    <input type="text" class="qty-cooked-dish" id="qty_cooked_dish_create" name="cooked_dish_create" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 10px"></div>
                            <label for="product_id" class="ingredient_name col-form-label">Chọn nguyên liệu:</label>
                        </div>
                        <div class="form-group row">
                            <table class="col-12 add-ingredient-dish" data-table_id="0" id="add_ingredient">
                                <thead>
                                <tr>
                                    <th style="min-width: 20px;"></th>
                                    <th class="ingredient_name">Nguyên liệu</th>
                                    <th class="ingredient_type">Loại nguyên liệu</th>
                                    <th class="ingredient_quantity">Định lượng sống</th>
                                    <th class="ingredient_unit">Đơn vị</th>
                                    <th class="ingredient_import_price">Giá nhập</th>
                                    <th class="ingredient_total_cost">Tổng tiền</th>
                                    <th class="action">Xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="add-ingredient">
                                    <td style="min-width: 20px;"></td>
                                    <td class="ingredient_name">
                                        <div class="input-group">
                                            <select name="product_id[]"
                                                    class="form-control select2 select_product_customer js-select-product"
                                                    required>
                                                <option value="" readonly="">Chọn Nguyên liệu</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="ingredient_type">
                                        <div class="input-group">
                                            <select name="is_spice[]" class="form-control" required>
                                                <option value="0" readonly="">Nguyên liệu chính</option>
                                                <option value="1" readonly="">Gia vị</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="ingredient_quantity">
                                        <div class="input-group">
                                            <input onkeyup="update_total_default();"
                                                   type="number" step="0.0000001" name="quantitative[]"
                                                   class="form-control quantitative" id="select-quantitative" min="0" required>
                                        </div>
                                    </td>
                                    <td class="ingredient_unit">
                                        <div class="input-group unit-ingredient">
                                            <input readonly class="form-control" id="select-unit">
                                        </div>
                                    </td>
                                    <td class="ingredient_import_price">
                                        <div class="input-group import-price-ingredient">
                                            <input readonly onkeyup="update_total_default();"
                                                   name="import_price[]"
                                                   class="form-control import_price" id="select-import-price">
                                        </div>
                                    </td>
                                    <td class="ingredient_total_cost">
                                        <div class="input-group">
                                            <div class="input-group">
                                                <input readonly class="form-control total_cost total_cost_0" name="total_cost[]" value="0">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="action">
                                        <div class="input-group unit-ingredient">
                                            <a href="#" title="Xóa"><i class="fa fa-trash delete-ingredient"></i></a>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 20px;"></div>
                            <div class="ingredient_name">
                                <button type="button" data-modal_id="0" class="btn  btn-success  btn-flat add_ingredirent js-createRowIngredient">
                                    <i class="fa fa-plus"></i> Thêm nguyên liệu
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-create-dish">Tạo mới</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- \Thêm món ăn --}}


        {{-- Chỉnh sửa món ăn --}}
        <div class="modal fade" id="edit-dish-modal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1100px;">
                <form class="modal-content form-edit-element-dish" method="post"
                      action="javascript:void(0)"
                      multiple="" style="height:500px;">
                    @csrf
                    <input type="hidden" value="{{  $customer['id'] ?? '' }}" name="customer_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Sửa món ăn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll; max-height:400px;">
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <label for="product_id" class="col-2 col-form-label ingredient_dish_name">Chọn món ăn:</label>
                            <div class="col-3">
                                <div class="input-group js-choose-food">
                                    <select data-attribute="product"
                                            class="form-control select2 select_dish" disabled>
                                        <option value=""></option>
                                    </select>
                                    <input type="hidden" value="" class="input-menu" name="menu_id">
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group" id="add_is_export_menu_detail">
                                    <select id="select_type_export_menu_edit" name="is_export_menu"
                                            class="form-control" required style="height: 44px; border-radius: 0px; max-width: 200px">
                                        <option value="1">Xuất định lượng</option>
                                        <option value="0">Không xuất định lượng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3" style="margin-left: 30px;">
                                <div class="btn-group float-right">
                                    <label for="product_id" class="col-form-label">Tổng cost món ăn:</label>
                                    <h6 class="sum-total-cost" data-sum_total_cost_id="" style="font-weight: bold; margin: 10px 10px">
                                        {{ sc_currency_render($sum_total_cost ?? 0, 'vnd') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 10px"></div>
                            <label for="product_id" class="ingredient_dish_name ingredient_cooked_dish_name col-form-label">Định lượng chín:</label>
                            <div class="col-5">
                                <div class="input-group">
                                    <input type="text" class="qty-cooked-dish" id="qty_cooked_dish_edit" name="qty_cooked_dish" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <label for="product_id" class="col-2 col-form-label">Chọn nguyên liệu:</label>
                        </div>
                        <div class="form-group row">
                            <table class="col-12 edit-ingredient-dish" data-table_id="" id="add_ingredient">
                                <thead>
                                <tr>
                                    <th style="min-width: 20px"></th>
                                    <th class="ingredient_name">Nguyên liệu</th>
                                    <th class="ingredient_type">Loại nguyên liệu</th>
                                    <th class="ingredient_quantity">Định lượng sống</th>
                                    <th class="ingredient_unit">Đơn vị</th>
                                    <th class="ingredient_import_price">Giá nhập</th>
                                    <th class="ingredient_total_cost">Tổng tiền</th>
                                    <th class="action">Xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="add-ingredient">
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <div class="ingredient_name">
                                <button type="button" data-modal_id="" class="btn  btn-success  btn-flat add_ingredirent js-createRowIngredient">
                                    <i class="fa fa-plus"></i> Thêm nguyên liệu
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-save-dish">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- /Chỉnh sửa món ăn --}}


        {{-- Sao chép món ăn --}}
        <div class="modal fade" id="clone-dish-modal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1100px;">
                <form class="modal-content form-clone-element-dish" method="post"
                      action="javascript:void(0)"
                      multiple="" style="height:500px;">
                    @csrf
                    <input type="hidden" value="{{  $customer['id'] ?? '' }}" name="customer_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Sao chép món ăn</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll; max-height:400px;">
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <label for="product_id" class="col-2 col-form-label ingredient_dish_name">Chọn món ăn:</label>
                            <div class="col-3">
                                <div class="input-group">
                                    <select name="dish_id" data-attribute="product"
                                            class="form-control select2 select_dish">
                                        <option value="">Chọn món ăn mới</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="input-group" id="add_is_export_menu_clone">
                                    <select id="select_type_export_menu_clone" name="is_export_menu"
                                            class="form-control" required style="height: 44px; border-radius: 0px; max-width: 200px">
                                        <option value="1">Xuất định lượng</option>
                                        <option value="0">Không xuất định lượng</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-3" style="margin-left: 30px;">
                                <div class="btn-group float-right">
                                    <label for="product_id" class="col-form-label">Tổng cost món ăn:</label>
                                    <h6 class="sum-total-cost" data-sum_total_cost_id="" style="font-weight: bold; margin: 10px 10px">
                                        {{ sc_currency_render($sum_total_cost ?? 0, 'vnd') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 10px"></div>
                            <label for="product_id" class="ingredient_dish_name ingredient_cooked_dish_name col-form-label">Định lượng chín:</label>
                            <div class="col-5">
                                <div class="input-group" id="js-select-dish">
                                    <input type="text" class="qty-cooked-dish" id="qty_cooked_dish_clone" name="qty_cooked_dish" value="">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <label for="product_id" class="col-2 col-form-label">Chọn nguyên liệu:</label>
                        </div>
                        <div class="form-group row">
                            <table class="col-12 clone-ingredient-dish" data-table_id="" id="add_ingredient">
                                <thead>
                                <tr>
                                    <th style="min-width: 20px"></th>
                                    <th class="ingredient_name">Nguyên liệu</th>
                                    <th class="ingredient_type">Loại nguyên liệu</th>
                                    <th class="ingredient_quantity">Định lượng sống</th>
                                    <th class="ingredient_unit">Đơn vị</th>
                                    <th class="ingredient_import_price">Giá nhập</th>
                                    <th class="ingredient_total_cost">Tổng tiền</th>
                                    <th class="action">Xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="add-ingredient"></tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="form-group row">
                            <div style="min-width: 20px"></div>
                            <div class="ingredient_name">
                                <button type="button" data-modal_id="" class="btn  btn-success  btn-flat add_ingredirent js-createRowIngredient">
                                    <i class="fa fa-plus"></i> Thêm nguyên liệu
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary btn-save-dish">Tạo mới</button>
                    </div>
                </form>
            </div>
        </div>
        {{-- /Sao chép món ăn --}}
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
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none !important;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield !important;
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
        .unit-ingredient {
            /* padding-left: 10px; */
        }
        .unit-ingredient p {
            font-size: 1rem;
            font-weight: 800;
            margin-bottom: 0px !important;
        }
        .unit-ingredient i {
            margin-top: 7px;
            margin-left: 5px;
            font-size: 24px;
            color: red;
        }
        .ingredient_name {
            min-width: 230px;
            max-width: 230px;
            width: 230px !important;
            padding-left: 10px;
        }
        .ingredient_dish_name {
            min-width: 120px;
            max-width: 120px;
            padding-left: 10px;
        }
        .ingredient_cooked_dish_name {
            min-width: 140px;
            max-width: 140px;
            padding-left: 10px;
        }
        .qty-cooked-dish {
            width: 100%;
        }
        .ingredient_name .select2 {
            width: 220px;
        }
        .ingredient_type {
            min-width: 160px;
            padding-left: 10px;
        }
        .ingredient_quantity {
            min-width: 130px;
            padding-left: 10px;
        }
        .ingredient_qty_cooked {
            min-width: 130px;
            padding-left: 10px;
        }
        .ingredient_unit {
            min-width: 90px;
            min-width: 90px;
            padding-left: 10px;
            padding-right: 10px;
        }
        .ingredient_import_price {
            min-width: 120px;
            margin-left: 10px;
        }
        .ingredient_total_cost {
            min-width: 120px;
            padding-left: 10px;
        }
        .action {
            min-width: 55px;
            max-width: 55px;
            padding-left: 10px;
        }
    </style>
@endpush

@push('scripts')

    <!-- Handle navigation with key arrow -->
    <script type="text/javascript">

        $('.form-add-element-dish').bind('keydown', function(e) {
            if (e.which === 38 || e.which === 40 || e.which === 13) {
                e.preventDefault();
            }
        });

        $('.form-edit-element-dish').bind('keydown', function(e) {
            if (e.which === 38 || e.which === 40 || e.which === 13) {
                e.preventDefault();
            }
        });

        $('.form-clone-element-dish').bind('keydown', function(e) {
            if (e.which === 38 || e.which === 40 || e.which === 13) {
                e.preventDefault();
            }
        });

        $('table.add-ingredient-dish').keydown(function(e) {
            var id_modal = $(this).attr('data-table_id');
            var active = $('input:focus,select:focus',$(this));
            var next = null;
            var focusableQuery = 'input:visible,select:visible,textarea:visible';
            var position = parseInt( active.closest('td').index()) + 1;
            var tr_position = parseInt( active.closest('tr').index());
            var tr_length = $('table.add-ingredient-dish tr').length - 2;

            switch(e.keyCode) {
                case 38: // Up
                    next = active
                        .closest('tr')
                        .prev()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 40: // Down
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 13: // Enter
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    if (tr_position == tr_length) {
                        $('.js-createRowIngredient').filter('[data-modal_id=' + id_modal + ']').trigger('click');
                    }
                    break;
            }
            if(next && next.length) {
                next.focus();
            }
        });

        $('table.edit-ingredient-dish').keydown(function(e) {
            var id_modal = $(this).attr('data-table_id');
            var active = $('input:focus,select:focus',$(this));
            var next = null;
            var focusableQuery = 'input:visible,select:visible,textarea:visible';
            var position = parseInt( active.closest('td').index()) + 1;
            var tr_position = parseInt( active.closest('tr').index());
            var tr_length = $('table.edit-ingredient-dish tr').length - 2;

            switch(e.keyCode) {
                case 38: // Up
                    next = active
                        .closest('tr')
                        .prev()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 40: // Down
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 13: // Enter
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    if (tr_position == tr_length) {
                        $('.js-createRowIngredient').filter('[data-modal_id=' + id_modal + ']').trigger('click');
                    }
                    break;
            }
            if(next && next.length) {
                next.focus();
            }
        });

        $('table.clone-ingredient-dish').keydown(function(e) {
            var id_modal = $(this).attr('data-table_id');
            var active = $('input:focus,select:focus',$(this));
            var next = null;
            var focusableQuery = 'input:visible,select:visible,textarea:visible';
            var position = parseInt( active.closest('td').index()) + 1;
            var tr_position = parseInt( active.closest('tr').index());
            var tr_length = $('table.clone-ingredient-dish tr').length - 2;

            switch(e.keyCode) {
                case 38: // Up
                    next = active
                        .closest('tr')
                        .prev()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 40: // Down
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    break;
                case 13: // Enter
                    next = active
                        .closest('tr')
                        .next()
                        .find('td:nth-child(' + position + ')')
                        .find(focusableQuery);
                    if (tr_position == tr_length) {
                        $('.js-createRowIngredient').filter('[data-modal_id=' + id_modal + ']').trigger('click');
                    }
                    break;
            }
            if(next && next.length) {
                next.focus();
            }
        });
    </script>
    <!-- /Handle navigation with key arrow -->

    {{-- Handle scroll to bottom when search dish list --}}
    <script>

        $("#button_search").submit(function() {
            sessionStorage.setItem("check", 1);
        });
        if (sessionStorage.getItem("check")) {
            $("body,html").animate({
                scrollTop: $(".dish-list").offset().top
            }, 50);
            sessionStorage.removeItem("check");
        }
    </script>
    {{-- /Handle scroll to bottom when search dish list --}}

    <script>
        let count = 0;
        let id_customer = "{{ $customer['id'] ?? ''}}";

        function initSelect2() {
            //Dynamic search product
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
            //Dynamic search product customer
            $(".select_product_customer").select2({
                {{--let id_customer = "{{ $customer['id'] }}";--}}
                ajax: {
                    url: "{{sc_route_admin('admin_search.product_customer')}}?id=" + id_customer,
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
                        // console.log(data.unit);
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

            //Dynamic search dish
            $(".select_dish").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.dish')}}",
                    type: "get",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            page: params.page || 1,
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

        // Dynamic search
        $(document).ready(function () {
            initSelect2();
        });

        function clearFilter() {
            $("#select_supplier").val('').trigger('change');
            $("input[name='product_search']").val('').trigger('change');
        }

        // Get value when select product
        $('.js-select-product').on('select2:select', function (e) {
            var name_unit = e.params.data.unit;
            var unit_type = e.params.data.unit_type;
            var import_price = e.params.data.import_price;
            $('.unit-ingredient').find('#select-unit').val(name_unit);
            $('.import-price-ingredient').find('#select-import-price').val(format_money(import_price)).trigger('keyup');
        });

        // Update total, total cost
        function update_total_default() {
            var qty = $('#select-quantitative').eq(0).val();
            var import_price = $('#select-import-price').eq(0).val();
            $('.total_cost').eq(0).val(format_money(Math.round(qty * convert_money_to_numb(import_price))));
            update_sum_total_cost(0);
        }

        function update_total(id, count) {
            var qty = $('.quantitative_' + id + '_' + count).val();
            var import_price = $('.import_price_' + id + '_' + count).val();
            $('.total_cost_' + id + '_' + count).eq(0).val(format_money(Math.round(qty * convert_money_to_numb(import_price))));
            update_sum_total_cost(id);
        }

        function update_sum_total_cost(id) {
            var sum_d = 0;
            var sum = 0;
            $('.total_cost_' + id).each(function(){
                sum_d += convert_money_to_numb(this.value);
            });
            var formated = sum_d.toLocaleString('en-US') + '₫';
            $("[data-sum_total_cost_id=" + id + "]").html(formated);
        }

        // Delete ingredient dish
        $('.delete-ingredient').on('click', function () {
            $('.add-ingredient').remove();
            update_sum_total_cost(0);
        })

        // Add tr add ingredient
        $('.js-createRowIngredient').on('click', function () {
            var id_modal = $(this).attr('data-modal_id');
            var html = '';
            count += 1;
            html += '<td style="min-width: 20px"></td>\n' +
                '<td class="ingredient_name">\n' +
                '<div class="input-group">\n' +
                '<select data-id="' + count + '" name="product_id[]" class="form-control select2 select_product_customer js-select-product" required>\n' +
                '<option value="" readonly>Chọn Nguyên liệu</option>\n' +
                '</select>\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="ingredient_type">\n' +
                '<div class="input-group">\n' +
                '<select name="is_spice[]" class="form-control" required>\n' +
                '<option value="0" readonly="">Nguyên liệu chính</option>\n' +
                '<option value="1" readonly="">Gia vị</option>\n' +
                '</select>\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="ingredient_quantity">\n' +
                '<div class="input-group">\n' +
                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" type="number" step="0.0000001" name="quantitative[]" id="select-quantitative-' + count + '" class="form-control quantitative quantitative_' + id_modal + '_' + count + '" value="" min="0" required>\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="ingredient_unit">\n' +
                '<div class="input-group unit-ingredient">\n' +
                '<input readonly class="form-control" id="select-unit-' + count + '">\n' +
                '<input type="hidden" value="" name="unit_id[]">\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="ingredient_import_price">\n' +
                '<div class="input-group import-price-ingredient">\n' +
                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" readonly name="import_price[]" id="select-import-price-' + count + '" class="form-control import_price import_price_' + id_modal + '_' + count + '" value="">\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="ingredient_total_cost">\n' +
                '<div class="input-group">\n' +
                '<input readonly name="total_cost[]" class="form-control total_cost_' + id_modal + ' total_cost_' + id_modal + '_' + count + '" value="0">\n' +
                '</div>\n' +
                '</td>\n' +
                '<td class="action">\n' +
                '<div class="input-group unit-ingredient">\n' +
                '<a href="#" title="Xóa"><i class="fa fa-trash" onclick="deleteIngredient(' + id_modal + ', ' + count + ')"></i></a>\n' +
                '</div>\n' +
                '</td>';

            $('[data-table_id=' + id_modal + '] > tbody:last-child').append('<tr class="add-ingredient-' + count + '">' + html + '</tr>');
            $(".select_product_customer").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.product_customer')}}?id=" + id_customer,
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

            $('.quantitative').focus();

            $('.js-select-product').on('select2:select', function (e) {
                let id = $(this).data('id');
                let name_unit = e.params.data.unit;
                let import_price = e.params.data.import_price;
                let unit_p = '#select-unit-' + id;
                let import_price_p = '#select-import-price-' + id;
                $('.unit-ingredient').find(unit_p).val(name_unit);
                $('.import-price-ingredient').find(import_price_p).val(format_money(import_price)).trigger('keyup');
            });
        });

        function deleteIngredient(id_modal, id) {
            $('.add-ingredient-' + id).remove();
            update_sum_total_cost(id_modal);
        }

        $('.button-edit-dish-modal').on('click', function () {
            let id_menu = $(this).data('id');
            let id_modal = $(this).data('modal_id').replace(/\s/g,'');
            let is_export_menu = parseInt($(this).data('is_export_menu'));
            $('.form-edit-element-dish .js-createRowIngredient').attr('data-modal_id', id_modal);
            $('.form-edit-element-dish .sum-total-cost').attr('data-sum_total_cost_id', id_modal);
            $('.form-edit-element-dish #add_ingredient').attr('data-table_id', id_modal);
            $('#edit-dish-modal #add_ingredient > tbody:last-child').html('');
            $('.js-choose-food').find('.select2-selection__rendered').text($(this).data('title'));
            $('.input-menu').val($(this).data('id'));
            $('#qty_cooked_dish_edit').val($(this).data('qty_cooked_dish'));
            $('#select_type_export_menu_edit').find('option').each(function(i,e){
                if ( $(this).val() == is_export_menu ) {
                    $(this).attr('selected', true);
                }
            });
            $.ajax({
                url: '{{ sc_route_admin('admin.davicook_customer.get_list_ingredient_dish') }}?=' + id_menu,
                method: "GET",
                data: {id: id_menu, _token: "{{csrf_token()}}"},
                dataType: "json",
                success: function (data) {
                    if (data) {

                        // Show total cost dish
                        let obj_length = (Object.keys(data).length);
                        let sum_total_cost = data[obj_length-1].sum_total_cost;
                        let formated_sum_total_cost = sum_total_cost.toLocaleString('en-US') + '₫';
                        $("[data-sum_total_cost_id=" + id_modal + "]").html(formated_sum_total_cost);

                        let detailHtml = [];
                        for (let datum of data) {
                            count += 1;
                            let html = '';
                            let is_slice = datum.is_spice == 1 ? 'selected' : '';
                            let is_not_slice = datum.is_spice == 0 ? 'selected' : '';
                            html += '<td style="min-width: 20px"></td>\n' +
                                '<td class="ingredient_name">\n' +
                                '<div class="input-group">\n' +
                                '<select data-number="' + count + '" name="product_id[]"' +
                                'class="form-control select2 select_product_customer js-edit-product" required>\n' +
                                '<option value="' + datum.product_id + '" readonly="">' + datum.product_name + '</option>\n' +
                                '</select>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_type">\n' +
                                '<div class="input-group">\n' +
                                '<select name="is_spice[]" class="form-control" required>\n' +
                                '<option value="0" '+ is_not_slice +' >Nguyên liệu chính</option>\n' +
                                '<option value="1" '+ is_slice +'>Gia vị</option>\n' +
                                '</select>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_quantity">\n' +
                                '<div class="input-group">\n' +
                                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" type="number" step="0.0000001" name="quantitative[]" id="select-quantitative-' + count + '" class="form-control quantitative quantitative_' + id_modal + '_' + count + '" value="' + datum.qty + '" min="0" required>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_unit">\n' +
                                '<div class="input-group unit-ingredient">\n' +
                                '<input readonly class="form-control" value="' + datum.unit + '" id="select-unit-' + count + '">\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_import_price">\n' +
                                '<div class="input-group import-price-ingredient">\n' +
                                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" readonly name="import_price[]" id="select-import-price-' + count + '" class="form-control import_price import_price_' + id_modal + '_' + count + '" value="' + format_money(datum.import_price) + '">\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_total_cost">\n' +
                                '<div class="input-group">\n' +
                                '<input readonly name="total_cost[]" class="form-control total_cost_' + id_modal + ' total_cost_' + id_modal + '_' + count + '" value="' + format_money(Math.round(datum.import_price * datum.qty)) + '">\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="action">\n' +
                                '<div class="input-group unit-ingredient">\n' +
                                '<a href="#" title="Xóa" onclick="deleteIngredient(' + id_modal + ' ,' + count + ')"><i class="fa fa-trash delete-ingredient"></i></a>\n' +
                                '</div>\n' +
                                '</td>\n';
                            html = '<tr class="add-ingredient-' + count + '">' + html + '</tr>';
                            detailHtml.push(html);
                        }

                        $('#edit-dish-modal #add_ingredient > tbody:last-child').html(detailHtml.join('\r\n'));
                        $(".select_product_customer").select2({
                            ajax: {
                                url: "{{sc_route_admin('admin_search.product_customer')}}?id=" + id_customer,
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
                    $('.js-edit-product').on('select2:select', function (e) {
                        let number = $(this).data('number');
                        let name_unit = e.params.data.unit;
                        let import_price = e.params.data.import_price;
                        let unit_p = '#select-unit-' + number;
                        let import_price_p = '#select-import-price-' + number;

                        $(unit_p).val(name_unit);
                        $(import_price_p).val(format_money(import_price)).trigger('keyup');
                        update_sum_total_cost(id_modal);
                    });

                }
            });
        });

        /**
         * Nhân bản món ăn. Show chi tiết từng nguyên liệu.
         */
        $('.button-clone-dish-modal').on('click', function () {
            let id_menu = $(this).data('id');
            let id_modal = $(this).data('modal_id').replace(/\s/g,'');

            $('.form-clone-element-dish .js-createRowIngredient').attr('data-modal_id', id_modal);
            $('.form-clone-element-dish .sum-total-cost').attr('data-sum_total_cost_id', id_modal);
            $('.form-clone-element-dish #add_ingredient').attr('data-table_id', id_modal);
            $('#clone-dish-modal #add_ingredient > tbody:last-child').html('');
            $('.input-menu').val($(this).data('id'));
            $('#qty_cooked_dish_clone').val($(this).data('qty_cooked_dish'));

            $.ajax({
                url: '{{ sc_route_admin('admin.davicook_customer.get_list_ingredient_dish') }}?=' + id_menu,
                method: "GET",
                data: {id: id_menu, _token: "{{csrf_token()}}"},
                dataType: "json",
                success: function (data) {
                    if (data) {

                        // Show total cost dish
                        let obj_length = (Object.keys(data).length);
                        let sum_total_cost = data[obj_length-1].sum_total_cost;
                        let formated_sum_total_cost = sum_total_cost.toLocaleString('en-US') + '₫';
                        $("[data-sum_total_cost_id=" + id_modal + "]").html(formated_sum_total_cost);

                        let detailHtml = [];
                        for (let datum of data) {
                            count += 1;
                            let is_slice = datum.is_spice == 1 ? 'selected' : '';
                            let is_not_slice = datum.is_spice == 0 ? 'selected' : '';
                            let html = '';
                            html += '<td style="min-width: 20px"></td>\n' +
                                '<td class="ingredient_name">\n' +
                                '<div class="input-group">\n' +
                                '<select data-number="' + count + '" name="product_id[]"' +
                                'class="form-control select2 select_product_customer js-edit-product" required>\n' +
                                '<option value="' + datum.product_id + '" readonly="">' + datum.product_name + '</option>\n' +
                                '</select>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_type">\n' +
                                '<div class="input-group">\n' +
                                '<select name="is_spice[]" class="form-control" required>\n' +
                                '<option value="0" '+ is_not_slice +' >Nguyên liệu chính</option>\n' +
                                '<option value="1" '+ is_slice +'>Gia vị</option>\n' +
                                '</select>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_quantity">\n' +
                                '<div class="input-group">\n' +
                                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" type="number" step="0.0000001" name="quantitative[]" id="select-quantitative-' + count + '" class="form-control quantitative quantitative_' + id_modal + '_' + count + '" value="' + datum.qty + '" min="0" required>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_unit">\n' +
                                '<div class="input-group unit-ingredient">\n' +
                                '<input readonly class="form-control" value="' + datum.unit + '" id="select-unit-' + count + '">\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_import_price">\n' +
                                '<div class="input-group import-price-ingredient">\n' +
                                '<input onKeyup="update_total(' + id_modal + ', ' + count + ');" readonly name="import_price[]" id="select-import-price-' + count + '" class="form-control import_price import_price_' + id_modal + '_' + count + '" value="' + format_money(datum.import_price) + '" required>\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="ingredient_total_cost">\n' +
                                '<div class="input-group">\n' +
                                '<input readonly name="total_cost[]" class="form-control total_cost_' + id_modal + ' total_cost_' + id_modal + '_' + count + '" value="' + format_money(Math.round(datum.import_price * datum.qty)) + '">\n' +
                                '</div>\n' +
                                '</td>\n' +
                                '<td class="action">\n' +
                                '<div class="input-group unit-ingredient">\n' +
                                '<a href="#" title="Xóa" onclick="deleteIngredient(' + id_modal + ', ' + count + ')"><i class="fa fa-trash delete-ingredient"></i></a>\n' +
                                '</div>\n' +
                                '</td>\n';
                            html = '<tr class="add-ingredient-' + count + '">' + html + '</tr>';
                            detailHtml.push(html);
                        }

                        $('#clone-dish-modal #add_ingredient > tbody:last-child').html(detailHtml.join('\r\n'));
                        $(".select_product_customer").select2({
                            ajax: {
                                url: "{{sc_route_admin('admin_search.product_customer')}}?id=" + id_customer,
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
                    $('.js-edit-product').on('select2:select', function (e) {
                        let number = $(this).data('number');
                        let name_unit = e.params.data.unit;
                        let import_price = e.params.data.import_price;
                        let unit_p = '#select-unit-' + number;
                        let import_price_p = '#select-import-price-' + number;

                        $(unit_p).val(name_unit);
                        $('.import-price-ingredient').find(import_price_p).val(format_money(import_price)).trigger('keyup');
                        update_sum_total_cost(id_modal);
                    });
                }
            });
        });

        // delete dish customer
        var selectedRows = function () {
            var selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });

            return selected;
        }
        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            // console.log(ids);
            deleteItem(ids);
        });

        function deleteItem(ids) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn mục để xoá', 'Vui lòng chọn it nhât 1 bản ghi trước khi xoá đối tượng');
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
                                    alertMsg('error', data.msg,
                                        '{{ sc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    location.reload();
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}',
                        '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                }
            })
        }

        $('.form-add-element-dish').on('submit', function (e) {
            $.ajax({
                type: 'post',
                url: "{{ sc_route_admin('admin.davicook_customer.add_ingredient_dish') }}",
                data: $('.form-add-element-dish').serialize(),
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', data.msg,
                            '{{ sc_language_render('action.warning') }}');
                        e.preventDefault();
                        return;
                    } else {
                        alertMsg('success', data.msg);
                        location.reload();
                    }
                }
            });
        });

        $('.form-edit-element-dish').on('submit', function (e) {
            $.ajax({
                type: 'post',
                url: "{{ sc_route_admin('admin.davicook_customer.update_list_ingredient_dish') }}",
                data: $('.form-edit-element-dish').serialize(),
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', data.msg,
                            '{{ sc_language_render('action.warning') }}');
                        e.preventDefault();
                        return;
                    } else {
                        alertMsg('success', data.msg);
                        location.reload();
                    }
                }
            });
        });

        $('.form-clone-element-dish').on('submit', function (e) {
            $.ajax({
                type: 'post',
                url: "{{ sc_route_admin('admin.davicook_customer.clone_ingredient_dish') }}",
                data: $('.form-clone-element-dish').serialize(),
                success: function (data) {
                    if (data.error == 1) {
                        alertMsg('error', data.msg,
                            '{{ sc_language_render('action.warning') }}');
                        e.preventDefault();
                        return;
                    } else {
                        alertMsg('success', data.msg);
                        location.reload();
                    }
                }
            });
        });
    </script>
@endpush
