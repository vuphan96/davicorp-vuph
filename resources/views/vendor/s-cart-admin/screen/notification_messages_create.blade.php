@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools">
                        <div class="btn-group pull-right" style="margin-right: 5px">
                            <a href="{{ route('admin_notify_automatic.index') }}" class="btn  btn-flat btn-default"
                                title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                    id="form-main" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="fields-group">
                            {{-- description --}}
                            <div class="form-group row {{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description"
                                    class="col-sm-2  control-label text-right">{{ sc_language_render('admin.news.title') }}<span
                                        style="color: red; font-size:20px">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="description"
                                        value="{{ old('description') }}">
                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                            {{ $errors->first('description') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- end description --}}
                            {{-- code --}}
                            <div class="form-group row {{ $errors->has('code') ? ' has-error' : '' }}">
                                <label for="code" class="col-sm-2  control-label text-right">Mã</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="code" value="notification" readonly>
                                    @if ($errors->has('code'))
                                        <span class="help-block">
                                            {{ $errors->first('code') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- /.code --}}
                            {{-- content --}}
                            <div class="form-group row {{ $errors->has('editor') ? ' has-error' : '' }}">
                                <label for="editor"
                                    class="col-sm-2 control-label text-right">{{ sc_language_render('contact.content') }}<span
                                        style="color: red; font-size:20px">*</span></label>
                                <div class="col-sm-8">
                                    <textarea id="content" class="editor" name="editor">
                                    {{ old('editor') }}
                                </textarea>
                                    @if ($errors->has('editor'))
                                        <span class="help-block">
                                            {{ $errors->first('editor') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- /.content --}}
                            {{-- schedule --}}
                            <div class="form-group row {{ $errors->has('schedule') ? ' has-error' : '' }}">
                                <label for="description" class="col-sm-2  control-label text-right">Thời gian thông báo
                                    (Hằng ngày)<span style="color: red; font-size:20px">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" id="schedule" name="schedule" class="form-control title"
                                        value="{{ old('schedule') }}" />
                                    @if ($errors->has('schedule'))
                                        <span class="help-block">
                                            {{ $errors->first('schedule') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- /.schedule --}}
                        </div>
                    </div>
                    {{-- Link --}}
                    <div class="card-body">
                        <div class="cart-footer row">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8">
                                <div class="btn-group float-left">
                                    <a href="{{ route('admin_notify_automatic.index') }}"
                                        class="btn btn-warning btn-flat">Quay lại</a>
                                </div>
                                <div class="btn-group float-right">
                                    <button type="submit"
                                        class="btn btn-primary">{{ sc_language_render('action.save') }}</button>
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
    <style type="text/css">
        .tooltipTrigger a p {
            display: none;
            position: absolute;
            border: 0.2px solid grey;
            padding: 0 2px;
            background: white;
        }

        .tooltipTrigger a:hover p {
            display: block;

        }

        .tooltipTrigger a:hover {
            display: block;
            cursor: pointer;
        }

        .tooltipTrigger a p {
            color: #000;
        }
    </style>
@endpush

@push('scripts')
    @include($templatePathAdmin . 'component.ckeditor_js')
    {{-- url: "{{ route('admin_notify_manual.load_customer') }}", --}}
    <script type="text/javascript">
        //For insert to template
        var variable = ['', ' {danh_xung} ', ' {khach_hang} '];

        function insert(text) {
            $('textarea.editor').ckeditor().editor.insertText(text);
        }

        function insertTemplate(text) {
            $('textarea.editor').ckeditor().editor.setData(text);
        }

        function insertLink(text) {
            $('textarea.editor').ckeditor().editor.insertText(text);
        }
        $('textarea.editor').ckeditor({
            filebrowserImageBrowseUrl: '{{ sc_route_admin('admin.home') . '/' . config('lfm.url_prefix') }}?type=product',
            filebrowserImageUploadUrl: '{{ sc_route_admin('admin.home') . '/' . config('lfm.url_prefix') }}/upload?type=product&_token={{ csrf_token() }}',
            filebrowserBrowseUrl: '{{ sc_route_admin('admin.home') . '/' . config('lfm.url_prefix') }}?type=Files',
            filebrowserUploadUrl: '{{ sc_route_admin('admin.home') . '/' . config('lfm.url_prefix') }}/upload?type=file&_token={{ csrf_token() }}',
            filebrowserWindowWidth: '900',
            filebrowserWindowHeight: '500'
        });
    </script>
@endpush
