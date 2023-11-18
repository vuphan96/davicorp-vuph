@extends($templatePathAdmin.'layout')

@section('main')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title !!}</h3>
                </div>

                <form action="{{ sc_route_admin('admin.import_priceboard.import') }}" method="post"
                      accept-charset="UTF-8" class="form-horizontal" id="import-excel" enctype="multipart/form-data">
                    @csrf
                    <div class="box-body">
                        <div class="fields-group">
                            <div class="form-group {{ $errors->has('file') ? ' text-red' : '' }}">
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
                                        <button class="btn btn-sm btn-primary" id="button-upload"><i
                                                    class="fa fa-save"></i> Tải lên
                                        </button>
                                        <a class="btn btn-sm btn-success"
                                           href="{{asset('example_import_excel/MauBangGiaNhapNCC.xlsx')}}"><i
                                                    class="fa fa-download"></i> Tải mẫu excel import</a>
                                        <a class="btn btn-sm btn-secondary"
                                           href="{{ sc_route_admin('admin.import_priceboard.index') }}"><i
                                                    class="fa fa-backward"></i> Quay lại</a>
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
                                @if(session()->get('error_bags'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi dữ liệu!</h4>
                                        <p>Vui lòng kiểm tra dữ liệu trong các Sheet sau!</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('error_bags') as $sheet => $sheet_error)
                                                <b>Sheet {{ $sheet }}</b>
                                        @if(!empty($sheet_error['master']))
                                            <div class="pl-3">
                                                {{ implode(', ', $sheet_error['master']) }}<br/>
                                            </div>
                                        @endif
                                        <p class="pl-3">
                                            @if(!empty($sheet_error['details']))
                                                @foreach($sheet_error['details'] as $line => $detail_errors)
                                                    <b>Dòng {{ $line }}</b> - {{ implode(', ', $detail_errors) }}<br/>
                                                @endforeach
                                            @endif
                                        </p>
                                        <hr>
                                        @endforeach
                                        </p>
                                    </div>
                                @endif

                                @if(session()->get('error_dupticated_bags'))
                                    <div class="alert alert-danger" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i> Lỗi trùng dữ liệu!</h4>
                                        <p>Vui lòng kiểm tra dữ liệu trong các Sheet sau!</p>
                                        <hr>
                                        <p class="mb-0">
                                        <p class="pl-3">
                                            @foreach(session()->get('error_dupticated_bags') as $sheet => $sheet_error)
                                                <b>Sheet {{ $sheet }}</b><br/>
                                                @if(!empty($sheet_error['details']))
                                                    @foreach($sheet_error['details'] as $line => $detail_errors)
                                                        @if(is_string($detail_errors))
                                                            <b>Dòng {{ $line }}</b> - {{ implode(', ', $detail_errors) }}<br/>
                                                        @elseif(is_array($detail_errors))
                                                            @foreach($detail_errors as $detail_error_dupticate)
                                                                {{ $detail_error_dupticate['name'] }} ({{ $detail_error_dupticate['value'] }}) - Dòng {{ implode(", ", $detail_error_dupticate['index']) }}
                                                            @endforeach
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </p>
                                        <hr>

                                        </p>
                                    </div>
                                @endif
                                @if(session()->get('data_success_import'))
                                    <div class="alert alert-success" role="alert">
                                        <h4 class="alert-heading"><i class="fa fa-info-circle"></i>Nhập file excel thành công!</h4>
                                        <p>Trong file nhập có sự thay đổi dữ liệu sau :</p>
                                        <hr>
                                        <p class="mb-0">
                                            @foreach(session()->get('data_success_import') as $sheet => $sheet_error)
                                                <b>Sheet {{ $sheet }}</b> <br>
                                                @foreach($sheet_error as $line => $detail_errors)
                                                        <p>- {{ $detail_errors }}</p>
                                                @endforeach
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
    <script type="text/javascript">
        {{--let href = '{{ sc_route_admin('admin.import_priceboard.export_notify') }}';--}}
{{--        @if (session()->get('data_success_import'))--}}
{{--            console.log(href)--}}
{{--            window.location.href = href;--}}
{{--        @endif--}}
    </script>
@endpush