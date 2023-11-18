@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title !!}</h3>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_supplier.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ sc_route_admin('admin_supplier.import') }}" method="post" accept-charset="UTF-8"
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
                                        @if (session()->get('dupticate'))
                                            <span class="form-text text-red">
                                                <i class="fa fa-info-circle"></i> {!! sc_language_render('admin.category.import_error_dupticate') !!}
                                                <div class="pl-3">
                                                    @foreach (session()->get('dupticate') as $row_index => $warning)
                                                        <code><b>Dòng {{ $row_index }} -
                                                                {{ $warning }}</b></code><br />
                                                    @endforeach
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
                                           href="{{ asset('example_import_excel/NhaCungCap.xlsx') }}"><i
                                                    class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary" href="{{ route('admin_supplier.index') }}"><i
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
