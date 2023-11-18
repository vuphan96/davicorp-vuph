@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-tools">
                        @if($notify->is_import_price == 1)
                        <div class="btn-group pull-right" style="margin-right: 10px">
                            <a href="{{ sc_route_admin('admin_notify_history.export_import_price', ['id' => $notify->id]) }}" target="_blank" class="btn btn btn-primary"><i
                                        class="fa fa-file-export"></i>&nbsp;Xuất Excel
                            </a>
                        </div>
                        @endif
                        <div class="btn-group pull-right" style="margin-right: 5px">
                            <a href="{{ route('admin_notify_history.index') }}" class="btn  btn-flat btn-default" title="List"><i
                                        class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <form action="" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                      enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="fields-group">
                            {{-- title --}}
                            <div class="form-group row">
                                <label for="title"
                                       class="col-sm-2  control-label text-right">{{ sc_language_render('admin.news.title') }}</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{!! $notify->title !!}" disabled>
                                </div>
                            </div>
                            {{-- end title --}}
                            {{-- content --}}
                            <div class="form-group row ">
                                <label for="customer"
                                       class="col-sm-2  control-label text-right">{{ sc_language_render('contact.content') }}</label>
                                <div class="col-sm-8">
                                    @if($notify->edit_type == 5)
                                        @php
                                            $content = json_decode($notify->content);
                                        @endphp
                                        @foreach($content as $key => $item)
                                            <b>Sheet {{ $key }}</b> <br>
                                            @foreach($item as $line => $detail)
                                                <p>&nbsp;&nbsp;  - {{ $detail }}</p>
                                            @endforeach
                                        @endforeach
                                    @else
                                        <p>{!! $notify->content !!}</p>
                                    @endif

                                </div>
                            </div>
                            {{-- end content --}}
                            {{-- date send --}}
                            <div class="form-group row">
                                <label for="date" class="col-sm-2  control-label text-right">Ngày gửi</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="date" name="date"
                                           value="{{ $notify->created_at }}" disabled>
                                </div>
                            </div>
                            {{-- end date send --}}
                            {{-- link --}}
                            @if (!empty($notify->link))
                                <div class="form-group row">
                                    <label for="links" class="col-sm-2  control-label text-right">Link đính kèm</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="links" name="links"
                                               value="{!! $notify->link !!}" disabled>
                                    </div>
                                </div>
                            @endif
                            {{-- end link --}}
                        </div>
                    </div>
                    {{-- Link --}}
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
