@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin.import_priceboard.index') }}"
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
                    $id_priceBoard = $importPriceboard->id ?? '';
                @endphp
                <form action="{{ $url_action }}" autocomplete="off" method="post" name="form_name" accept-charset="UTF-8"
                      class="form-horizontal" id="form-main" enctype="multipart/form-data">
                    @if (isset($method))
                        @method($method)
                    @endif
                    <div id="main-add" class="card-body">
                        {{-- name --}}
                        <div class="form-group row kind  {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="sku" class="col-sm-2 col-form-label">Tên bảng giá nhập
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="name" name="name"
                                           value="{!! old('name', $importPriceboard->name ?? '') !!}"
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
                        <div class="form-group row kind  {{ $errors->has('code') ? ' text-red' : '' }}">
                            <label for="sku" class="col-sm-2 col-form-label">Mã bảng giá nhập
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="code" name="code"
                                           value="{!! old('code', $importPriceboard->code ?? '') !!}"
                                           class="form-control input-sm sku"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('code'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('code') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //code --}}
                        {{-- select priceboard --}}
                        <div class="form-group row kind   {{ $errors->has('supplier_id') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">Chọn nhà cung cấp
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <select class="form-control input-sm select2" id="select_supplier_id"
                                            name="supplier_id" required>
                                        <option value="">---</option>
                                        @if($old_supplier)
                                            <option value="{{$old_supplier->id}}"
                                                    selected>{{$old_supplier->name}}</option>
                                        @endif
                                    </select>
                                </div>
                                @if ($errors->has('supplier_id'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('supplier_id') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //select priceboard --}}
                        {{-- start date --}}
                        <div class="form-group row kind   {{ $errors->has('start_date') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">Ngày bắt đầu hiệu lực<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="start_date" name="start_date"
                                           value="{!! old('start_date', $importPriceboard->start_date ?? '') !!}"
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
                        <div class="form-group row kind   {{ $errors->has('end_date') ? ' text-red' : '' }}">
                            <label for="supplier_id"
                                   class="col-sm-2 col-form-label">Ngày kết thúc hiệu lực<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="end_date" name="end_date"
                                           value="{!! old('end_date', $importPriceboard->end_date ?? '') !!}"
                                           class="form-control input-sm sku date_time"
                                           placeholder="" required/>
                                </div>
                                @if ($errors->has('end_date'))
                                    <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('end_date') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //due date --}}
                    </div>
                    <!-- /.card-body -->


                    <div class="card-footer kind   row" id="card-footer">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="submit"
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
    </style>
@endpush

@push('scripts')
    @include($templatePathAdmin.'component.ckeditor_js')
    <script src="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.js"></script>
    <script>

    </script>
    <script>
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
        $(document).ready(function () {
            $("#select_supplier_id").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.supplier')}}",
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
        });
        $('#price_board_detail').on('click', function() {
            let id = $('#select_supplier_id').val();
            let href = "{{ sc_route_admin('admin_price.detail') }}/" + id;
            window.location.href = href;
        });

        $('#select_supplier_id').on('change', function() {
            $('#display_button_detail').removeClass('display');
        })
    </script>
@endpush