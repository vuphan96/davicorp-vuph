@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title!!}</h3>
                </div>

                <form action="{{ route("admin.einvoice.import") }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group {{ $errors->has('file') ? ' text-red' : '' }}">
                                <label for="image" class="col-sm-2 col-form-label">
                                </label>
                                <div class="col-sm-6 p-3">
                                    <input type="text" name="_token" value="{{ csrf_token() }}" hidden>
                                    <input  name="excel_file" placeholder="Your Import"
                                            type="file" class="filepond" id="excel_file" name="excel-file">
                                </div>
                            </div>
                            <div class="fields-groups">
                                <div class="p-3">
                                    <br/>
                                    <span class="text-info">
                                    <i class="fa fa-info-circle"></i> Quý khách nên nhập tối đa 10 Sheet mỗi File để tránh quá tải hệ thống</code>
                                        <br/>
                                </span>
                                    <span class="text-info">
                                    <i class="fa fa-info-circle"></i> Để hạn chế lỗi, quý khách vui lòng nhập dạng ngày giờ theo định dạng chuẩn của Excel</code>
                                </span>
                                </div>
                            </div>
                            <div class="fields-group">
                                <div class="form-group">
                                    <div class="col-md-6 pl-3">
                                        <button class="btn btn-sm btn-primary" id="button-upload"><i class="fa fa-save"></i> Tải lên</button>
                                        <a class="btn btn-sm btn-success" href="{{asset('example_import_excel/MauNhapHoaDonDienTuExcel.xlsx')}}"><i class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary" href="{{ route('admin.einvoice.index') }}"><i class="fa fa-backward"></i> Quay lại</a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-2">
                                @if(session()->get('error'))
                                    <span class="form-text text-red p-3">
                                                    <i class="fa fa-info-circle"></i> {!! sc_language_render('action.import_error') !!}
                                                    <div class="pl-3">
                                                        <code><b>{{ session()->get('error') }}</b></code>
                                                    </div>
                                                </span>
                                @endif
                                @if(session()->get('error_validate'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi dữ liệu!</h4>
                                        <p>Vui lòng kiểm tra dữ liệu trong các Sheet sau!</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_validate')["data"] as $sheet => $sheet_error)
                                                @php $sheet_error = $sheet_error["error_msg"];@endphp
                                                @if(!empty($sheet_error))
                                                    <b>Sheet {{ $sheet }}</b>
                                        <div class="pl-3">
                                            @isset($sheet_error["detail"])
                                                @foreach($sheet_error["detail"] as $keyRow => $rowdata)
                                                    <div>Dòng {{ $keyRow }}: {{ implode(', ', $rowdata) }}</div>
                                                @endforeach
                                            @endisset
                                            @isset($sheet_error["master"])
                                                @foreach($sheet_error["master"] as $keyRowMaster => $rowdataMaster)
                                                    <div>Dòng {{ $keyRowMaster }}: {{ $rowdataMaster }}</div>
                                                @endforeach
                                            @endisset
                                            <br/>
                                        </div>
                                        @endif
                                        {{--                                                <p class="pl-3">--}}
                                        {{--                                                        @if(!empty($sheet_error['details']))--}}
                                        {{--                                                            @foreach($sheet_error['details'] as $line => $detail_errors)--}}
                                        {{--                                                                <b>Dòng {{ $line }}</b> - {{ implode(', ', $detail_errors) }}<br/>--}}
                                        {{--                                                            @endforeach--}}
                                        {{--                                                        @endif--}}
                                        {{--                                                </p>--}}
                                        <hr>
                                        @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if(session()->get('error_dupticate'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi dữ liệu!</h4>
                                        <p>Trùng dữ liệu. Vui lòng kiểm tra dữ liệu ở các sheet sau!</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_dupticate') as $sheet => $sheet_error)
                                                <b>Sheet {{ $sheet }}</b>
                                        <div class="pl-3">
                                            {{ implode(', ', $sheet_error) }}<br/>
                                        </div>
                                        <hr>
                                        @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if(session()->get('error_data'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi dữ liệu!</h4>
                                        <p>Dữ liệu không hợp lệ. Vui lòng kiểm tra dữ liệu ở các sheet sau!</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_data') as $sheet => $sheet_error)
                                                <b>Sheet {{ $sheet }}</b>
                                        <div class="pl-3">
                                            {{ implode(', ', $sheet_error) }}<br/>
                                        </div>
                                        <hr>
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