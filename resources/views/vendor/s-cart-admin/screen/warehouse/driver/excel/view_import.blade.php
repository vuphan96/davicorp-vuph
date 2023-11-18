@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title !!}</h3>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('driver.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ sc_route_admin('driver.import') }}" method="post" accept-charset="UTF-8"
                      class="form-horizontal" id="import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group {{ $errors->has('file') ? ' text-red' : '' }}">
                                <label for="image" class="col-sm-2 col-form-label">
                                </label>
                                <div class="col-sm-6 p-3">
                                    <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                                    <input name="excel_file" placeholder="Your Import" type="file" class="filepond"
                                           id="excel_file" name="excel-file">
                                    <div>
                                        @if(session()->get('error_validate_import'))
                                            <span class="form-text text-red pl-2">
                                                <div class="pl-3">
                                                    <p class="mb-0" style="font-size: 16px">
                                                        @foreach(session()->get('error_validate_import') as $content)
                                                            <code>- {{ $content }} </code><br>
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </span>
                                        @endif
                                        <span class="form-text">
                                            <i class="fa fa-info-circle"></i> {!! sc_language_render('admin.category.import-note') !!}
                                        </span>
                                        <span class="form-text">
                                            <i class="fa fa-info-circle"></i> {!! sc_language_render('admin.supplier.import.limit') !!}
                                        </span>

                                    </div>
                                </div>
                            </div>
                            <div class="fields-group">
                                <div class="form-group">
                                    <div class="col-md-6 pl-3">
                                        <button class="btn btn-sm btn-primary" id="button-upload"><i class="fa fa-save"></i>
                                            Tải lên</button>
                                        <a class="btn btn-sm btn-success"
                                           href="{{ asset('example_import_excel/NhanVienGiaoHang.xlsx') }}"><i
                                                    class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary" href="{{ route('driver.index') }}"><i
                                                    class="fa fa-backward"></i> Quay lại</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>


                    <!-- /.box-footer -->
                </form>
            </div>
        </div>
    </div>


@endsection

@push('styles')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />
    <style>
        .filepond--credits {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    @include($templatePathAdmin.'component.filepond_excel_import')
@endpush
