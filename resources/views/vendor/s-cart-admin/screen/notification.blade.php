@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description ?? '' }}</h2>
                    <div class="card-tools">
                        <div class="btn-group pull-right" style="margin-right: 5px">
                            <a href="{{ route('admin_notify_history.index') }}" class="btn  btn-flat btn-default" title="List"><i
                                    class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                    id="form-main" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="fields-group">
                            {{-- region --}}
                            <div class="form-group row {{ $errors->has('region') ? ' has-error' : '' }}">
                                <label for="region"
                                    class="col-sm-2  control-label text-right">{{ sc_language_render('customer.zone') }}</label>
                                <div class="col-sm-8">
                                    <select class="form-control select2" id="region" name="region">
                                        <option value="0">Tất cả</option>
                                        <option value="cus" {{ isset($customers) ? 'selected' : '' }}>Khách hàng chỉ
                                            định</option>
                                        @foreach ($regions as $region)
                                            <option value="{{ $region->id }}"> {{ $region->name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('region'))
                                        <span class="help-block">
                                            {{ $errors->first('region') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- end region --}}
                            {{-- Customer --}}
                            <div class="form-group row {{ $errors->has('customer') ? ' has-error' : '' }}">
                                <label for="customer"
                                    class="col-sm-2  control-label text-right">{{ sc_language_render('store.admin.config_customer') }}</label>
                                <div class="col-sm-8">
                                    <select required class="form-control select2" id="customer" name="customer[]" multiple
                                        {{ isset($customers) ? '' : 'disabled' }}>
                                        @if (isset($customers))
                                            @foreach ($customers as $value)
                                                <option value="{{ $value['id'] }}" selected>{{ $value['name'] }}
                                                </option>
                                            @endforeach
                                        @else
                                            <option value="0" selected>Tất cả</option>
                                        @endif
                                    </select>
                                    @if ($errors->has('customer'))
                                        <span class="help-block">
                                            {{ $errors->first('customer') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- /.Customer --}}
                            @if (isset($invalid_cus))
                                {{-- InvalidCustomer --}}
                                <div class="form-group">
                                    <label for="customer" class="col-sm-2  control-label">Mã khách hàng không hợp lệ</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2" multiple disabled>
                                            @foreach ($invalid_cus as $value)
                                                <option value="{{ $value['ma_dai_ly'] }}" selected>
                                                    {{ $value['ma_dai_ly'] }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                {{-- /.InvalidCustomer --}}
                            @endif
                            {{-- title --}}
                            <div class="form-group row {{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="region"
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
                            @if (isset($customers))
                                @php
                                    $i = 0;
                                @endphp
                                @foreach ($customers as $customer)
                                    @foreach ($variable as $value)
                                        <input type="hidden" name="variable[{{ $i }}][{{ $value }}]"
                                            value="{{ $customer[$value] }}">
                                    @endforeach
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            @endif
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
                                        {{-- Start template notifi --}}
                                        <table class="table">
                                            @if (isset($listTemplates))
                                                <tr>
                                                    <th>Mẫu thông báo</th>
                                                </tr>
                                                @foreach ($listTemplates as $template)
                                                    <tr>
                                                        <td class="tooltipTrigger">
                                                            <a onclick="insertTemplate('{{ $template['content'] }}')">{{ $template['title'] }}
                                                                <p>Mẫu thông báo</p>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </table>
                                        {{-- END template notifi --}}
                                    </div>
                                </div>
                                {{-- end Mẫu thông báo --}}

                            </div>
                            {{-- end content --}}

                        </div>

                    </div>
                    {{-- Link --}}
                    <br>
                    <div class="card-body">
                        {{-- <div class="form-group row">
                            <label for="" class="col-sm-2  control-label text-right">Link đính kèm</label>
                            <div class="col-sm-7">
                                <select name="link" class="form-control">
                                    <option value=""></option>
                                    <option value="shop_owe">Công nợ</option>
                                    <option value="rating">Đánh giá nhân viên</option>
                                    <option value="feedback">Góp ý đến Kim Hùng</option>
                                </select>
                                <p style="font-size: 14px;margin-top:5px"> <i class="fa fa-info-circle"
                                        style="color:#2A9CD4" aria-hidden="true"></i> Đính kèm link dưới Ứng dụng điện thoại
                                    (Có thể để trống)</p>
                            </div>
                        </div> --}}
                        {{-- /.Link --}}
                        <!-- /.box-body -->

                        <div class="cart-footer row">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-7">
                                <div class="btn-group float-left">
                                    <a href="{{ route('admin_notify_history.index') }}" class="btn btn-warning btn-flat">Quay
                                        lại</a>
                                </div>
                                <div class="btn-group float-right">
                                    <button type="submit" id="check-loadings"
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
    <div id="loading" style="display: none;">
        <div id="overlay" class="overlay"><i class="fa fa-spinner fa-pulse fa-5x fa-fw "></i></div>
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
    <script type="text/javascript">
        $('#check-loadings').click(function () {
            $('#loading').show();
        })
        $("[name='top'],[name='status']").bootstrapSwitch();
    </script>
    {{-- url: "{{ route('admin_notify_manual.load_customer') }}", --}}
    <script type="text/javascript">
        $(document).ready(function() {
            $('.select2').select2()
            $('#customer').select2({
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('admin_notify_manual.load_customer') }}",
                    dataType: 'json',
                    delay: 100,
                    data: function(params) {
                        return {
                            _token: "{{ csrf_token() }}",
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    cache: true

                },
            });
        });

        $('#region').change(function() {
            var id = $(this).val();
            if (isNaN(id)) {
                $('#customer').find("option[value='0']").remove();
                $('#customer').prop('disabled', '');
            } else {
                $('#customer').find('option').remove();
                $('#customer').prop('disabled', 'disabled');
                $('#customer').append($('<option>', {
                    value: 0,
                    text: 'Tất cả',
                    selected: 'selected'
                }));
            }


        });
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
