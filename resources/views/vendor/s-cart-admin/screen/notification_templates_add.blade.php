@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="fields-group">
                            {{-- title --}}
                            <div class="form-group row {{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title"
                                    class="col-sm-2  control-label text-right">{{ sc_language_render('admin.news.title') }}<span
                                        style="color: red; font-size:20px">*</span></label>
                                <div class="col-sm-7">
                                    <input type="text" id="title" name="title" class="form-control title" />
                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                            {{ $errors->first('title') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- /.Title --}}
                            {{-- content --}}
                            <div class="form-group row {{ $errors->has('editor') ? ' has-error' : '' }}"
                                style="margin-bottom: -100px;">
                                <label for="editor"
                                    class="col-sm-2 control-label text-right">{{ sc_language_render('contact.content') }}<span
                                        style="color: red; font-size:20px">*</span></label>
                                <div class="col-sm-7">
                                    <textarea id="content" class="editor" name="editor">
                                    {!! old('editor') !!}
                                </textarea>
                                    @if ($errors->has('editor'))
                                        <span class="help-block">
                                            {{ $errors->first('editor') }}
                                        </span>
                                    @endif
                                </div>
                                {{-- Mẫu thông báo --}}
                                <div class="col-sm-3">
                                    <div id="style-7" class="card-body table-responsive no-padding"
                                        style="height:500px; overflow: hidden;overflow-y: auto;">
                                        <table class="table">
                                            <tr>
                                                <th>Danh sách biến</th>
                                            </tr>
                                            <tr>
                                                <td class="tooltipTrigger">
                                                    <a onclick="insert('{khach_hang}')">{khach_hang}
                                                        <p>Họ tên khách hàng, ví dụ: Trà Phước Hưng</p>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        {{-- END danh_xung --}}
                                    </div>
                                </div>
                                {{-- end Mẫu thông báo --}}

                            </div>
                            {{-- end content --}}

                        </div>

                    </div>
                    <br>
                    <div class="card-body">
                        <div class="cart-footer row">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-7">
                                <div class="btn-group float-left">
                                    <a href="{{ route('admin_notify_history.index') }}" class="btn btn-warning btn-flat">Quay
                                        lại</a>
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
    <div class="row">

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
