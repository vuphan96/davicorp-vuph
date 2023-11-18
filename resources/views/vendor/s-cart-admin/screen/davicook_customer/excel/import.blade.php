@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title!!}</h3>
                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin.davicook_customer.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>

                <form action="{{ sc_route_admin('admin.davicook_customer.import_list_customer') }}" method="post" accept-charset="UTF-8"
                      class="form-horizontal" id="import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group">
                                <label for="image" class="col-sm-2 col-form-label">
                                </label>
                                <div class="col-sm-6 p-3">
                                    <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                                    <div class="form-group div-info-radio-import">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input name="type_import_customer" id="info_0" type="radio" class="custom-control-input"
                                                   value="1" checked>
                                            <label for="info_0" class="custom-control-label">Ghi đè thông tin</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input name="type_import_customer" id="info_1" type="radio" class="custom-control-input"
                                                   value="2" >
                                            <label for="info_1" class="custom-control-label">Không ghi đè</label>
                                        </div>
                                    </div>
                                    <input name="excel_file" placeholder="Your Import" type="file" class="filepond"
                                           id="excel_file" name="excel-file">
                                    <div>
                                        @if (session()->get('massageUndefined'))
                                            <span class="form-text text-red" style="font-size: 13px">
                                                <i class="fa fa-info-circle"></i> Lỗi dữ liệu:
                                                <div class="pl-3">
                                                        <code><b style="font-size: 12px">Lỗi file excel không đúng mẫu. Vui lòng liên hệ bộ phận kỹ thuật !</b></code><br />
                                                        <code><b class="d-none">{{ session('massageUndefined') }}</b></code><br />
                                                </div>
                                            </span>
                                        @endif
                                        @if (session()->get('massageErrors'))
                                            <span class="form-text text-red" style="font-size: 13px">
                                                <i class="fa fa-info-circle"></i> Lỗi dữ liệu:
                                                <div class="pl-3">
                                                    @foreach (session('massageErrors') as $row_index => $info)
                                                        <code><b style="font-size: 12px">
                                                                {{ $info }}</b></code><br />
                                                    @endforeach
                                                </div>
                                            </span>
                                        @endif
                                        <span class="form-text">
                                            <i class="fa fa-info-circle"></i> {!! sc_language_render('admin.category.import-note') !!}

                                        </span>
                                        <span class="form-text">
                                            <i class="fa fa-info-circle"></i> {!! sc_language_render('admin.category.import.limit') !!}
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
                                           href="{{ asset('example_import_excel/davicook_customer_update_22_3_2023.xlsx') }}"><i
                                                    class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary"
                                           href="{{ route('admin.davicook_customer.index') }}"><i class="fa fa-backward"></i>
                                            Quay lại</a>
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
