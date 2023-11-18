@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row bg-white">
        <div class="col-md-12">
            <form action="{{ $url_action_edit }}" autocomplete="off" method="post" name="form_name" accept-charset="UTF-8"
                  class="form-horizontal card" id="form-main" enctype="multipart/form-data">
                <div class="card-header with-border">
                    <h2 class="card-title">Chỉnh sửa bảng giá nhập</h2>
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
                               class="col-sm-2 col-form-label">Ngày bắt đầu hiệu lực&nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label></label>
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
                               class="col-sm-2 col-form-label">Ngày kết thúc hiệu lực&nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label></label>
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
                            <button data-perm="import_priceboard:edit" type="submit"
                                    class="btn btn-primary">{{ sc_language_render('action.submit') }}</button>
                        </div>
                    </div>
                </div>

                <!-- /.card-footer -->
            </form>
        </div>
    </div>

    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Thêm sản phẩm</h3>

                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action_add_product }}" method="post"
                    accept-charset="UTF-8" class="form-horizontal" id="form_product_price">
                    @csrf
                    <input type="hidden" name="priceboard_id" value="{{ $importPriceboard->id ?? '' }}">
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('idProduct') ? ' text-red' : '' }}">
                            <label for="product_id" class="col-sm-3 col-form-label">Chọn sản phẩm</label>
                            <div class="col-sm-9">
                                <div class="input-group mb-3">
                                    <select class="form-control input-sm select2" style="width: 100%;" name="product_id" id="product_id" required>
                                        <option value="">Chọn sản phẩm</option>
                                    </select>
                                </div>

                                @if ($errors->has('product_id'))
                                    <span class="text-sm text-red">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('product_id') }}
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('price1') ? ' text-red' : '' }}">
                            <label for="price"
                                class="col-sm-3 col-form-label">Giá nhập</label>
                            <div class="col-sm-9 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" id="price" name="price" value="{{ old('price') }}"
                                        class="form-control name {{ $errors->has('price') ? ' is-invalid' : '' }}" min="0">
                                </div>

                                @if ($errors->has('price'))
                                    <span class="text-sm text-red">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('price') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer">
                        <button data-perm="import_priceboard:edit"  type="submit" id="submit_list_price_product" class="btn btn-success float-right"><i
                                class="fa fa-plus"></i> {{ sc_language_render('product.admin.add_new') }}</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>

        <div class="col-md-6">

            <div class="card">
                <div class="card-header">
                    <div class="col-sm-5 float-left">
                        <h3 class="card-title"><i class="fas fa-th-list"></i> {!! $title_description ?? '' !!}</h3>
                    </div>

                    <div class=" float-right">
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
                    </div>

                </div>

                <div class="card-body p-0">
                    <section id="pjax-container" class="table-list">
                        <div class="box-body table-responsivep-0">
                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                <tr>
                                    @if (!empty($removeList))
                                        <th></th>
                                    @endif
                                    @foreach ($listTh as $key => $th)
                                        <th>{!! $th !!}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($dataTr as $keyRow => $tr)
                                    <tr class="{{ request('id') == $keyRow ? 'active' : '' }}">
                                        @if (!empty($removeList))
                                            <td>
                                                <input class="checkbox" type="checkbox" class="grid-row-checkbox"
                                                       data-id="{{ $keyRow }}">
                                            </td>
                                        @endif
                                        @foreach ($tr as $key => $trtd)
                                            <td>{!! $trtd !!}</td>
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
    </div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ sc_file('admin/plugin/bootstrap-editable.css')}}">
@endpush

@push('scripts')
    {{-- //Pjax --}}
    {{-- <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script> --}}
    <script type="text/javascript">
        $('.grid-refresh').click(function() {
            $.pjax.reload({
                container: '#pjax-container'
            });
        });

        $(document).on('submit', '#button_search', function(event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function() {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function() {
            $('#loading').hide()
        })

        // tag a
        $(function() {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function() {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        @if ($buttonSort)
            $('#button_sort').click(function(event) {
                var url = '{{ $urlSort ?? '' }}?sort_shipping=' + $('#shipping_sort option:selected').val();
                $.pjax({
                    url: url,
                    container: '#pjax-container'
                })
            });
        @endif
    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
        {{-- sweetalert2 --}}
        var selectedRows = function() {
            var selected = [];
            $('.grid-row-checkbox:checked').each(function() {
                selected.push($(this).data('id'));
            });

            return selected;
        }

        $('.grid-trash').on('click', function() {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        function deleteItem(ids) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: "{{ sc_language_render('action.delete_confirm') }}",
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function() {
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'post',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg,
                                        '{{ sc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    {{--window.location.replace(--}}
                                    {{--    '{{ sc_route_admin('admin_price.edit', ['id' => $productprice['id']]) }}'--}}
                                    {{--    );--}}
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
        {{-- / sweetalert2 --}}

        // submit form thêm bảng giá
        $("#submit_list_price_product").click(function() {
            $("#form_product_price").submit();
        });
        $("#table-price-product").click(function() {
            $("#form-main").submit();
        });
    </script>
    <!-- Ediable -->
    <script src="{{ sc_file('admin/plugin/bootstrap-editable.min.js') }}"></script>
    <script type="text/javascript">
        $(".date_time").datepicker({ dateFormat: "{{ config('admin.datepicker_format') }}" });
        // Editable
        $(document).ready(function() {
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
            $("#product_id").select2({
                ajax: {
                    url: "{{sc_route_admin('admin_search.product')}}",
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
            $.fn.editable.defaults.params = function(params) {
                params._token = "{{ csrf_token() }}";
                params.lang = "{{ 'dump' }}";
                return params;
            };

            $('.editable-required').editable({
                validate: function(value) {
                    if (value == '') {
                        return '{{ sc_language_render('admin.not_empty') }}';
                    }
                    if(/\D/.test(value)) {
                        return '{{  sc_language_render('product.price.no.negative') }}';
                    }
                },
                success: function(data) {
                    if (data.error == 0) {
                        alertJs('success', '{{ sc_language_render('admin.msg_change_success') }}');
                    } else {
                        alertJs('error', data.msg);
                    }
                },
                display: function(value, response) {
                    let a = Number(value);
                    let x = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(a);
                    $(this).text( x);
                },
            });

        });
    </script>
@endpush
