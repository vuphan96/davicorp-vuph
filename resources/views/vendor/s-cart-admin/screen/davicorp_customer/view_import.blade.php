@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title!!}</h3>
                </div>

                <form action="{{ sc_route_admin('admin_customer.import') }}" method="post" accept-charset="UTF-8"
                      class="form-horizontal" id="import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group {{ $errors->has('file') ? ' text-red' : '' }}">
                                <div class="form-group div-info-radio-import pl-4">
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
                                <label for="image" class="col-sm-2 col-form-label">
                                </label>
                                <div class="col-sm-6 p-3">
                                    <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                                    <input name="excel_file" placeholder="Your Import"
                                           type="file" class="filepond" id="excel_file" name="excel-file">
                                </div>
                            </div>
                            <div class="fields-groups">
                                <div class="p-3">
                                <span class="text-info">
                                    <i class="fa fa-info-circle"></i> Nếu để trống mật khẩu lúc nhập, mật khẩu mặc định là <code>khachhang@davicorp</code>
                                </span>
                                    <br/>
                                    <span class="text-info">
                                    <i class="fa fa-info-circle"></i> Nên chia nhỏ các file mỗi 10.000 Dòng</code>
                                </span>
                                </div>
                            </div>
                            <div class="fields-group">
                                <div class="form-group">
                                    <div class="col-md-6 pl-3">
                                        <button class="btn btn-sm btn-primary" id="button-upload"><i
                                                    class="fa fa-save"></i> Tải lên
                                        </button>
                                        <a class="btn btn-sm btn-success"
                                           href="{{asset('example_import_excel/DanhSachKhachHangDavicorp.xlsx')}}"><i
                                                    class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary"
                                           href="{{ route('admin_customer.index') }}"><i class="fa fa-backward"></i>
                                            Quay lại</a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                @if(session()->get('error'))
                                    <span class="form-text text-red p-2">
                                                    <i class="fa fa-info-circle"></i> {!! sc_language_render('action.import_error') !!}
                                                    <div class="pl-3">
                                                        <code><b>{{ session()->get('error') }}</b></code>
                                                    </div>
                                                </span>
                                @endif
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
                                @if(session()->get('error_required'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi thiếu dữ liệu!</h4>
                                        <p>Các trường dữ liệu có dấu (*) không được bỏ trống, vui lòng điền đầy đủ thông tin.</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_required') as $line => $content)
                                                <b>Dòng {{ $line }}</b> - {{ implode(', ', $content) }}<br/>
                                            @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if(session()->get('error_dupticated'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi trùng dữ liệu!</h4>
                                        <p>Các trường dữ liệu ở dòng sau bị trùng trong tập tin hoặc đã tồn tại trước đó, vui lòng kiểm tra lại.</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_dupticated') as $line => $content)
                                                <b>Dòng {{ $line }}</b> - {{ implode(', ', $content) }}<br/>
                                            @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if(session()->get('error_validate'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi dữ liệu không hợp lệ!</h4>
                                        <p>Các trường dữ liệu ở dòng sau chưa tồn tại trong hệ thống. Vui lòng kiểm tra l</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_validate') as $line => $content)
                                                <b>Dòng {{ $line }}</b> - {{ implode(', ', $content) }}<br/>
                                            @endforeach
                                        </p>
                                    </div>
                                @endif
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
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"/>
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
          rel="stylesheet"/>
    <style>
        .filepond--credits {
            display: none;
        }
    </style>
@endpush

@push('scripts')
    @include($templatePathAdmin.'component.filepond_excel_import')
@endpush