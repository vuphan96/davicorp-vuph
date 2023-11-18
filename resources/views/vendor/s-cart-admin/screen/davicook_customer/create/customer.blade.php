@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-sm-{{ $is_edit ? 6 : 12 }}">
            <div class="card">
                <div class="card-header d-flex flex-row align-items-center">
                    <h2 class="card-title">Thêm mới khách hàng</h2>
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
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="submit" data-perm="{{isset($data_perm_submit)?$data_perm_submit:''}}"
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
    <style>
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

        .ingredient_name .select2 {
            width: 220px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        let count = 0;
        let id_customer = "{{ $customer['id'] ?? ''}}";

        function initSelect2() {
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

    </script>
@endpush
