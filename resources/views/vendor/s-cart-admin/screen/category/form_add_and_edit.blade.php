@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description ?? '' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_category.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{{ $category['id'] ?? '' }}">
                    <div class="card-body">
                        @foreach ($languages as $code => $language)
                            @if (!(array_key_exists('vi', $languages->toArray()) && count($languages->toArray()) == 1))
                                <div class="card">
                                    <div class="card-header with-border">
                                        <h3 class="card-title">{{ $language->name }} {!! sc_image_render($language->icon, '20px', '20px', $language->name) !!}</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        @endif

                                        <div
                                                class="form-group row  {{ $errors->has('descriptions.' . $code . '.title') ? ' text-red' : '' }}">
                                            <label for="{{ $code }}__title"
                                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.title') }}
                                                &nbsp;<span class="required-icon"
                                                            title="{{ sc_language_render('note.required-field') }}">*</span></label>
                                            <div class="col-sm-8">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                                    </div>
                                                    <input type="text" id="{{ $code }}__title"
                                                           name="title_category"
                                                           value="{{ old() ? old('title_category') : $category['name'] ?? '' }}"
                                                           class="form-control {{ $code . '__title' }}" placeholder=""/>
                                                </div>
                                                @if ($errors->has('descriptions.' . $code . '.title'))
                                                    <span class="form-text">
                                            <i class="fa fa-info-circle"></i>
                                            {{ $errors->first('descriptions.' . $code . '.title') }}
                                        </span>
                                                @else
                                                    @if (session('warrning'))
                                                        <span class="form-text" style="color: #dc3545">
                                                <i class="fa fa-info-circle"></i>
                                                {{ session('warrning') }}
                                            </span>
                                                    @else
                                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i>
                                                {{ sc_language_render('admin.max_c', ['max' => 200]) }}
                                            </span>
                                                    @endif
                                                @endif

                                            </div>
                                        </div>
                                        @if (!(array_key_exists('vi', $languages->toArray()) && count($languages->toArray()) == 1))
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- sku --}}
                        <div class="form-group row kind  {{ $errors->has('sku') ? ' text-red' : '' }}">
                            <label for="sku"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.code') }}<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}">&nbsp; *</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" style="width: 100px;" id="sku" name="sku"
                                           value="{{ old() ? old('sku') : $category['sku'] ?? '' }}"
                                           class="form-control input-sm sku"
                                           placeholder=""/>
                                </div>
                                @if ($errors->has('sku'))
                                    <span class="form-text">
                            <i class="fa fa-info-circle"></i> {{ $errors->first('sku') }}
                        </span>
                                @else
                                    <span class="form-text">
                            {{ sc_language_render('product.sku_validate') }}
                        </span>
                                @endif
                            </div>
                        </div>
                        {{-- //sku --}}
                        {{-- sku --}}
                        <div class="form-group row kind  {{ $errors->has('sort') ? ' text-red' : '' }}">
                            <label for="sku"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.sort') }}<span
                                        class="required-icon"
                                        title="{{ sc_language_render('note.required-field') }}"> &nbsp; *</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="number" style="width: 100px;" id="sort" name="sort"
                                           value="{{ old() ? old('sort') : $category['sort'] ?? 0 }}"
                                           class="form-control input-sm sku"
                                           placeholder=""/>
                                </div>
                                @if ($errors->has('sort'))
                                    <span class="form-text">
                            <i class="fa fa-info-circle"></i> {{ $errors->first('sort') }}
                        </span>
                                @endif
                            </div>
                        </div>
                        {{-- //sku --}}

                        <div class="form-group row  {{ $errors->has('image') ? ' text-red' : '' }}">
                            <label for="image"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.image') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="image" name="image"
                                           value="{{ old('image', $category['image'] ?? '') }}"
                                           class="form-control input image"
                                           placeholder=""/>
                                    <div class="input-group-append">
                                        <a data-input="image" data-preview="preview_image" data-type="category"
                                           class="btn btn-primary lfm">
                                            <i class="fa fa-image"></i> {{ sc_language_render('product.admin.choose_image') }}
                                        </a>
                                    </div>
                                </div>
                                @if ($errors->has('image'))
                                    <span class="form-text">
                            <i class="fa fa-info-circle"></i> {{ $errors->first('image') }}
                        </span>
                                @endif
                                <div id="preview_image" class="img_holder">
                                    @if (old('image', $category['image'] ?? ''))
                                        <img src="{{ sc_file(old('image', $category['image'] ?? '')) }}">
                                    @endif

                                </div>
                            </div>
                        </div>
                        <!-- Status -->
                        <div class="form-group  row">
                            <label for="status"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.category.status') }}</label>
                            <div class="col-sm-8">
                                <input class="checkbox" type="checkbox" name="status"
                                        {{ old('status', empty($category['status']) ? 0 : 1) ? 'checked' : '' }}>

                            </div>
                        </div>
                        <!-- End Status -->
                    </div>


                    <!-- /.card-body -->

                    <div class="card-footer row" id="card-footer">
                        @csrf
                        <div class="col-md-2">
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
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#vi__title').on('change', function () {
                let title = $('#vi__title').val();
                $('#alias').val(getSlug(title));
            });
        });
    </script>
@endpush
