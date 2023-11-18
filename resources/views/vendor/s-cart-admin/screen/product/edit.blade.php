@extends($templatePathAdmin.'layout')

@section('main')
    <style>
        #start-add {
            margin: 20px;
        }
        .icon-delete {
            font-size: 28px;
            cursor: pointer;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float right  mr-5">
                            <a href="{{ sc_route_admin('admin_product.index') }}" class="btn  btn-flat btn-default"
                               title="List">
                                <i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ sc_route_admin('admin_product.edit',['id'=>$product['id']]) }}" method="post"
                      accept-charset="UTF-8"
                      class="form-horizontal" id="form-main" enctype="multipart/form-data">


                    <div class="card-body">
                        {{-- Descriptions --}}
                        @php $code = 'vi'; @endphp
                        <div class="form-group row  {{ $errors->has('descriptions.'.$code.'.name') ? ' text-red' : '' }}">
                            <label for="{{ $code }}__name"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('product.name') }}
                                &nbsp;
                                &nbsp;<span class="required-icon"
                                            title="{{sc_language_render('note.required-field')}}">*</span></label>

                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="{{ $code }}__name"
                                           name="descriptions[{{ $code }}][name]"
                                           value="{{ old('descriptions.'.$code.'.name',($product->name ?? '')) }}"
                                           class="form-control {{ $code.'__name' }}" placeholder=""
                                           required/>
                                </div>
                                @if ($errors->has('descriptions.'.$code.'.name'))
                                    <span class="form-text">
                    <i class="fa fa-info-circle"></i>
                    {{ $errors->first('descriptions.'.$code.'.name') }}
                </span>
                                @else
                                    <span class="form-text">
                        <i class="fa fa-info-circle"></i> {{ sc_language_render('admin.max_c',['max'=>200]) }}
                    </span>
                                @endif
                            </div>
                        </div>
                        <!-- Customer short_name -->
                        <div class="form-group  row {{ $errors->has('short_name') ? ' text-red' : '' }}">
                            <label for="short_name"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('product.short_name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="descriptions[{{ $code }}][short_name]"
                                           name="descriptions[{{ $code }}][short_name]"
                                           value="{{ old('descriptions.'.$code.'.short_name', $product->short_name ?? '' )}}"
                                           class="form-control short_name"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('descriptions.'.$code.'.short_name'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.short_name') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = short_name -->
                        <!-- Customer bill_name -->
                        <div class="form-group  row {{ $errors->has('bill_name') ? ' text-red' : '' }}">
                            <label for="bill_name"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('product.bill_name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="descriptions[{{ $code }}][bill_name]"
                                           name="descriptions[{{ $code }}][bill_name]"
                                           value="{{ old('descriptions.'.$code.'.bill_name', $product->bill_name ?? '' )}}"
                                           class="form-control bill_name"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('descriptions.'.$code.'.bill_name'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('descriptions.'.$code.'.bill_name') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        <!-- = bill_name -->
                        {{-- //Descriptions --}}
                    <!-- Customer order_num -->
                        <div class="form-group  row {{ $errors->has('order_num') ? ' text-red' : '' }}">
                            <label for="order_num"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('product.order_num') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="order_num" name="order_num"
                                           value="{{ old('order_num', $product['order_num'] ?? '' )}}"
                                           class="form-control order_num"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('order_num'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('order_num') }}
                                            </span>
                                @endif
                            </div>
                        </div>
                        {{-- Sku --}}
                        <div class="form-group row {{ $errors->has('sku') ? ' text-red' : '' }}">
                            <label for="sku"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('product.sku') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="sku" name="sku"
                                           value="{!! old('sku',$product->sku) !!}" class="form-control sku"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('sku'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('sku') }}
                                </span>
                                @else
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ sc_language_render('product.sku_validate') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //Sku --}}
                        {{-- Category --}}
                        <div class="form-group row {{ $errors->has('category') ? ' text-red' : '' }}">
                            <label for="category"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('product.admin.select_category') }}
                                &nbsp;<span class="required-icon"
                                            title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select class="form-control category select2"
                                            data-placeholder="{{ sc_language_render('product.admin.select_category') }}"
                                            name="category_id" required>
                                        <option value=""
                                                readonly>{{ sc_language_render('product.admin.select_category') }}</option>
                                        @foreach ($categories as $k => $v)
                                            <option value="{{ $k }}" {{ $product ? (($product->category_id == $k) ? 'selected' : '' ) : '' }}>{{$v}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('category'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('category') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //Category --}}
                        {{-- Images --}}
                        {{-- select unit --}}
                        <div class="form-group row kind   {{ $errors->has('unit_id') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('product.admin.unit') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select class="form-control input-sm unit_id select2"
                                            name="unit_id">
                                        @foreach ($units as $k => $v)
                                            <option value="{{ $k }}" {{ (old('unit_id', $product->unit_id ?? '') == $k) ? 'selected':''  }}>{{ $v->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if ($errors->has('unit_id'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('unit_id') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //select unit --}}
                        {{--                        Định mức tối thiểu--}}
                        <div class="form-group row {{ $errors->has('minimum_qty_norm') ? ' text-red' : '' }}">
                            <label for="default"
                                   class="col-sm-2 col-form-label" style="padding-top: 0">{{ sc_language_render('admin.product.minimum_qty_norm') }}
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="minimum_qty_norm" name="minimum_qty_norm"
                                           value="{{ $product->minimum_qty_norm ?? old('minimum_qty_norm') }}"
                                           class="form-control description {{ $errors->has('minimum_qty_norm') ? ' is-invalid' : '' }}"
                                           min="0" oninput="validity.valid||(value=0);" step="0.01">
                                </div>

                                @if ($errors->has('minimum_qty_norm'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('minimum_qty_norm') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        {{--                        Định mức tối thiểu--}}
                        <div class="form-group row {{ $errors->has('qr_code') ? ' text-red' : '' }}">
                            <label for="default"
                                   class="col-sm-2 col-form-label" style="padding-top: 0">Link mã QRcode
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="qrcode" title="Xem mã QR Code"><i class="fas fa-qrcode"></i></span>
                                    </div>
                                    <input type="text" id="qr_code" name="qr_code"
                                           value="{{ $product->qr_code ?? old('qr_code') }}"
                                           class="form-control description {{ $errors->has('qr_code') ? ' is-invalid' : '' }}">
                                </div>

                                @if ($errors->has('qr_code'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('qr_code') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        {{--loại mặt hàng--}}
                        <div class="form-group row {{ $errors->has('kind') ? ' text-red' : '' }}">
                            <label for="kind"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.product.type_item') }}
                            </label>
                            <div class="col-sm-10 type-unit">
                                <input name="kind" type="radio" value="0"
                                       id="dry" {{ old('kind',($product['kind']==0))?'checked':''}}> &nbsp; <label
                                        for="dry">{{ sc_language_render('admin.product.dry') }}</label> &nbsp; &nbsp;
                                <input name="kind" type="radio" value="1"
                                       id="fresh" {{ old('kind',($product['kind']==1))?'checked':''}}> &nbsp; <label
                                        for="fresh">{{ sc_language_render('admin.product.fresh') }}</label>
                            </div>
                        </div>
                        {{--// loại mặt hàng--}}

                        {{--mức độ ưu tiên--}}
                        <div class="form-group row {{ $errors->has('priority') ? ' text-red' : '' }}">
                            <label for="priority"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.product.priority') }}
                            </label>
                            <div class="col-sm-10 type-unit">
                                <input name="priority" type="radio" value="0"
                                       id="normal" {{ old('priority',($product['purchase_priority_level']==0))?'checked':''}}>
                                &nbsp; <label
                                        for="normal">{{ sc_language_render('admin.product.priority_normal') }}</label>
                                &nbsp; &nbsp;
                                <input name="priority" type="radio" value="1"
                                       id="daily" {{ old('priority',($product['purchase_priority_level']==1))?'checked':''}}>
                                &nbsp; <label
                                        for="daily">{{ sc_language_render('admin.product.priority_daily') }}</label>
                            </div>
                        </div>
                        {{--// mức độ ưu tiên--}}

                        {{--Thuế cho khách hàng bình thường--}}
                        <div class="form-group row {{ $errors->has('default') ? ' text-red' : '' }}">
                            <label for="default"
                                   class="col-sm-2 col-form-label" style="padding-top: 0">{{ sc_language_render('admin.product.tax_default') }}
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="default" name="default"
                                           value="{{ old('order_num', $product['tax_default'] ?? 0 )}}"
                                           class="form-control description {{ $errors->has('default') ? ' is-invalid' : '' }}"
                                           min="0">&nbsp;<strong style="font-size: 25px">%</strong>
                                </div>

                                @if ($errors->has('default'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('default') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        {{--//Thuế cho khách hàng bình thường--}}

                        {{--Thuế cho khách hàng công ty--}}
                        <div class="form-group row {{ $errors->has('company') ? ' text-red' : '' }}">
                            <label for="company"
                                   class="col-sm-2 col-form-label" style="padding-top: 0">{{ sc_language_render('admin.product.tax_company') }}
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="company" name="company"
                                           value="{{ old('order_num', $product['tax_company'] ?? 0 )}}"
                                           class="form-control description {{ $errors->has('company') ? ' is-invalid' : '' }}"
                                           min="0">&nbsp;<strong style="font-size: 25px">%</strong>
                                </div>

                                @if ($errors->has('company'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('company') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        {{--//Thuế cho khách hàng công ty--}}

                        {{--Thuế cho khách hàng trường học--}}
                        <div class="form-group row {{ $errors->has('school') ? ' text-red' : '' }}">
                            <label for="school"
                                   class="col-sm-2 col-form-label" style="padding-top: 0">{{ sc_language_render('admin.product.tax_school') }}
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="school" name="school"
                                           value="{{ old('order_num', $product['tax_school'] ?? 0 )}}"
                                           class="form-control description {{ $errors->has('school') ? ' is-invalid' : '' }}"
                                           min="0">&nbsp;<strong style="font-size: 25px">%</strong>
                                </div>

                                @if ($errors->has('school'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('school') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        {{--//Thuế cho khách hàng trường học--}}
                        <div class="form-group row {{ $errors->has('school') ? ' text-red' : '' }}">
                            <label class="col-sm-2 col-form-label" style="padding-top: 0">Hạn mức cảnh báo
                            </label>
                            <div class="col-sm-8 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="qty_limit" name="qty_limit"
                                           value="{{ old('qty_limit', $product->qty_limit) ?? '' }}"
                                           class="form-control {{ $errors->has('qty_limit') ? ' is-invalid' : '' }}"
                                           min="0">
                                </div>
                            </div>
                        </div>
                        {{-- Status --}}
                        <div class="form-group row ">
                            <label for="status"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('product.status') }}&nbsp;<span
                                        class="required-icon"
                                        title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <input class="checkbox" type="checkbox"
                                       name="status" {{ old('status',$product['status'])?'checked':''}}>
                            </div>
                        </div>
                        {{-- //Status --}}

                    </div>

                    <hr style="margin-bottom: 10px !important;">
                    <div class="form-group row">
                        <div class="col-sm-8">
                            <label class="col-sm-12 col-form-label">
                                Danh sách kho hàng
                            </label>
                        </div>
                        <div class="col-sm-4">
                            <label class="col-sm-12  col-form-label">
                                Tồn kho
                            </label>
                        </div>
                    </div>
                    <div class="form-group row box-select-warehouse" id="box-select-warehouse">
                        @foreach($productWareHouse as $value)
                        <div class="col-sm-12 row">
                            <div class="col-sm-8 row" style="height: 40px">
                                <div class="col-sm-1">
                                <label for="status" class="col-form-label" style="margin-left: 25px;">
                                    Kho
                                </label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control warehouse-id select2" id="select2_1_{{$value->id}}" onchange="selectWareHouse($(this));" name="warehouse_id[]" style="width: 100%">
                                        <option value="">Chọn kho</option>
                                        @foreach($wareHouse as $item)
                                            <option value="{{ $item->id }}" {{ $item->id == $value->warehouse_id ? 'selected' : '' }}>{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2" style="height: 40px">
                                <div class="input-group mb-sm-5">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" name="qty_warehouse[]" id="qty-warehouse_{{$value->warehouse_id}}" value="{{ $value->qty }}" onchange="checkValueQty($(this))" class="form-control qty-warehouse" min="0">
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <span class="icon-delete" onclick="removeItem($(this))"> <i class="fas fa-trash-alt" style="color: red;"></i></span>
                            </div>
                        </div>
                        @endforeach

                    </div>
                    <div class="form-group row" >
                        <!-- Form nhập kho hàng -->
                        <div class="ml-4">
                            <button type="button" class="btn btn-flat btn-success" id="btn-clone-warehouse" title="{{sc_language_render('action.add') }}">
                                <i class="fa fa-plus"></i>Thêm mới kho
                            </button>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer kind kind0  kind1 kind2 row" id="card-footer">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="button" data-perm="product:edit" id="btn-submit-product"
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
    <!-- Modal -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Xem mã QR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 d-flex align-items-center align-content-center justify-content-center">
                            <img src="{!! \App\Front\Models\ShopProduct::generateQr($product->qr_code ?? "")["image"] ?? '' !!}" width="140px" height="140px" alt="QR Code"/>
                        </div>
                        <div class="col-md-12 d-flex align-items-center align-content-center justify-content-center">
                            <a href="{{ $product->qr_code ?? '' }}">{{ $product->qr_code ?? "" }}</a>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    @php
        $htmlSelectProduct =' <div class="col-sm-12 row">
                                <div class="col-sm-8 row" style="height: 40px">
                                <div class="col-sm-1">
                                <label for="status" class="col-form-label" style="margin-left: 25px;">
                                    Kho
                                </label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control warehouse-id select2" id="select2_1" onchange="selectWareHouse($(this));" name="warehouse_id[]" style="width: 100%">
                                        <option value="">Chọn kho</option>';
                                        foreach ($wareHouse as $k => $item) {
                    $htmlSelectProduct .='<option value="'.$item->id.'">'.$item->name.'</option>';
                                        }
                $htmlSelectProduct .='</select>
                                    </div>
                                </div>
                                <div class="col-sm-2" style="height: 40px">
                                    <div class="input-group mb-sm-5">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                        </div>
                                        <input type="number" name="qty_warehouse[]" value="0" onchange="checkValueQty($(this))" class="form-control qty-warehouse" min="0">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                <span class="icon-delete" onclick="removeItem($(this))"> <i class="fas fa-trash-alt" style="color: red;"></i></span>
                                </div>
                                </div>';
        $htmlSelectProduct = str_replace("\n", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\t", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("\r", '', $htmlSelectProduct);
        $htmlSelectProduct = str_replace("'", '"', $htmlSelectProduct);
    @endphp
@endsection
@push("scripts")
    <script src="{{ sc_file('admin/plugin/liquidmetal.js')}}" type="text/javascript"></script>
    <script src="{{ sc_file('admin/plugin/jquery.flexselect.js')}}" type="text/javascript"></script>
    <script>
        $(document).ready(function(){
            $("#qrcode").click(function () {
                $("#qrCodeModal").modal("show");
            });
        });
        $(document).ready(function () {
            //Initialize Select2 Elements
            $('.select2').select2()
        });

        $('#btn-submit-product').click(function () {
            let submitForm = true;
            let arrWareHouse = [];
            $('.warehouse-id').each(function (index) {
                let value = $(this).val();
                if (value !== '' && value !== null) {
                    arrWareHouse.push($(this).val());
                } else {
                    submitForm = false;
                    return alertMsg('error', 'Chọn Kho trống!');
                }
            })
            let uniqueArrWareHouse = unique(arrWareHouse);
            if (uniqueArrWareHouse.length !== arrWareHouse.length) {
                submitForm = false;
                return alertMsg('error', 'Chọn kho trùng nhau!');
            }

            $('.qty-warehouse').each(function (index) {
                let value = $(this).val();
                if (value == '') {
                    submitForm = false;
                    return alertMsg('error', 'Số lượng tồn Kho trống!');
                }
            })
            if (submitForm) {
                $('#form-main').submit();
            }
        })

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

        function selectWareHouse(e) {
            let arrWareHouse = [];
            $('.warehouse-id').each(function (index) {
                let value = $(this).val();
                if (value !== '' && value !== null) {
                    arrWareHouse.push($(this).val());
                }
            })
            let uniqueArrWareHouse = unique(arrWareHouse);
            if (uniqueArrWareHouse.length !== arrWareHouse.length) {
                return alertMsg('error', 'Chọn kho trùng nhau!');
            }
        }

        $('#btn-clone-warehouse').click(function () {
            let itemProduct = '{!! $htmlSelectProduct !!}';
            $('#box-select-warehouse').append(itemProduct)
            $('.select2').select2()
            $('.title-product-exchange').each(function (index) {
                $(this).text('Thêm mới kho '+ (index+1))
            })
        })

        function checkValueQty(e) {
            let value = parseFloat(e.val());
            let qty_limit = parseFloat($('#qty_limit').val());

            if (value <= qty_limit) {
                alertJs('error', 'Cảnh báo tồn kho sản phẩm sắp hết hàng!');
            }

            if (value < 1) {
                return e.val('')
            }
        }

        function removeItem(e) {
            let i = e.parents().eq(1);
            i.remove();
        }

        /**
         * Check mảng trùng nhau
         * @param arr
         * @returns {[]}
         */
        function unique(arr) {
            let newArr = []
            for (let i = 0; i < arr.length; i++) {
                if (!newArr.includes(arr[i])) {
                    newArr.push(arr[i])
                }
            }
            return newArr
        }
    </script>
@endpush