@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title ?? '' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr_5">
                            <a href="{{ sc_route_admin('admin.davicook_dish.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                @php
                    $id = $dish['id'] ?? '' ;
                @endphp
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    @if (isset($method))
                        @method('PUT')
                    @endif
                    <div class="card-body">
                        <!-- dish name -->
                        <div class="form-group  row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('admin.dish_form.name') }}
                                &nbsp;<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name"
                                           value="{{ old() ? old('name') : $dish['name'] ?? '' }}"
                                           class="form-control name" placeholder=""/>
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
                        <!-- end dish name -->
                        <br>
                        <!-- dish code -->
                        <div class="form-group row kind  {{ $errors->has('code') ? ' text-red' : '' }}">
                            <label for="code"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.dish_form.code') }}
                                &nbsp;<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="code" name="code"
                                           value="{{ old() ? old('code') : $dish['code'] ?? '' }}"
                                           class="form-control input-sm code" placeholder=""/>
                                </div>
                                @if ($errors->has('code'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('code') }}
                                    </span>
                                @else
                                    <span class="form-text">
                                        {!! sc_language_render('product.sku_validate') !!}
                                    </span>
                                @endif
                            </div>
                        </div>
                    {{-- end code --}}
                    <!-- Status -->
                        <div class="form-group  row">
                            <label for="status"
                                   class="col-sm-2  col-form-label">{{ sc_language_render('admin.dish_form.status') }}
                                &nbsp;</label>
                            <div class="col-sm-8" style="margin-top: 7px">
                                <input class="checkbox" type="checkbox" name="status" {{ $id ? '' : 'checked' }}
                                        {{ old('status', empty($dish['status']) ? 0 : 1) ? 'checked' : '' }}>
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
                                <button type="submit" data-perm= '{{ isset($add_dish) ? "davicook_dish:create" : "davicook_dish:edit" }}'
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

@endpush

@push('scripts')

@endpush
