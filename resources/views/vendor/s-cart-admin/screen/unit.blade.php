@extends($templatePathAdmin . 'layout')

@section('main')
    <div class="row">

        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title_action !!}</h3>
                    @if ($layout == 'edit')
                        <div class="btn-group float-right" style="margin-right: 5px">
                            <a href="{{ sc_route_admin('admin_unit.index') }}" class="btn  btn-flat btn-default"
                                title="List"><i class="fa fa-list"></i><span class="hidden-xs">
                                    {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    @endif
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                {{-- form --}}

                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                    id="form-main">
{{--                    @if (isset($method))--}}
{{--                        @method('PUT')--}}
{{--                    @endif--}}
                    <div class="card-body">
                        @if (isset($length['id']))
                            <input type="hidden" name="id" value="{{ $length['id'] }}">
                        @endif

                        <div class="form-group row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name"
                                class="col-sm-2 col-form-label">{{ sc_language_render('admin.length.name') }} <span
                                    style="color: red; font-size:20px">*</span></label>
                            <div class="col-sm-10 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name"
                                        value="{{ old() ? old('name') : $length['name'] ?? '' }}"
                                        class="form-control name {{ $errors->has('name') ? ' is-invalid' : '' }}">
                                </div>

                                @if ($errors->has('name'))
                                    <span class="text-sm" style="color: red">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                    </span>
                                @endif
                                @if (session('warrning'))
                                    <span class="text-sm" style="color: red">
                                        <i class="fa fa-info-circle"></i> {{ session('warrning') }}
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('description') ? ' text-red' : '' }}">
                            <label for="description"
                                class="col-sm-2 col-form-label">{{ sc_language_render('admin.length.description') }}
                            </label>
                            <div class="col-sm-10 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="description" name="description"
                                        value="{{ old() ? old('description') : $length['description'] ?? '' }}"
                                        class="form-control description {{ $errors->has('description') ? ' is-invalid' : '' }}">
                                </div>

                                @if ($errors->has('description'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('description') }}
                                    </span>
                                @endif

                            </div>
                        </div>
                        @php
                            //dd($length);
                        @endphp
                        <div class="form-group row {{ $errors->has('description') ? ' text-red' : '' }}">
                            <label for="description"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('action.type_unit') }}
                            </label>
                            <div class="col-sm-10 type-unit">
                                <input {{ $length ? (($length['type'] == 0) ? 'checked' : '') : 'checked' }} name="type" type="radio" value="0" id="float"> &nbsp; <label for="float">{{ sc_language_render('action.type_unit_decimal') }}</label> &nbsp; &nbsp;
                                <input {{ $length ? (($length['type'] != 0) ? 'checked' : '') : '' }} name="type" type="radio" value="1" id="integer"> &nbsp; <label for="integer">{{ sc_language_render('action.type_unit_integer') }}</label>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    @csrf
                    <div class="card-footer">
                        <button type="reset" class="btn btn-warning">{{ sc_language_render('action.reset') }}</button>
                        <button type="submit" data-perm="{{isset($data_perm_submit)?$data_perm_submit:''}}"
                            class="btn btn-primary float-right">{{ sc_language_render('action.submit') }}</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>


        <div class="col-md-7">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> {!! $title ?? '' !!}</h3>
                    <div class="card-header with-border">
                        <div class="card-tools">
                            @if (!empty($topMenuRight) && count($topMenuRight))
                                @foreach ($topMenuRight as $item)
                                    <div class="menu-right">
                                        @php
                                            $arrCheck = explode('view::', $item);
                                        @endphp
                                        @if (count($arrCheck) == 2)
                                            @if (view()->exists($arrCheck[1]))
                                                @include($arrCheck[1])
                                            @endif
                                        @else
                                            {!! trim($item) !!}
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <!-- /.box-tools -->
                    </div>

                    <div class="card-header with-border">
                        <div class="card-tools">
                            @if (!empty($menuRight) && count($menuRight))
                                @foreach ($menuRight as $item)
                                    <div class="menu-right">
                                        @php
                                            $arrCheck = explode('view::', $item);
                                        @endphp
                                        @if (count($arrCheck) == 2)
                                            @if (view()->exists($arrCheck[1]))
                                                @include($arrCheck[1])
                                            @endif
                                        @else
                                            {!! trim($item) !!}
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>


                        <div class="float-left">
                            @if (!empty($removeList))
                                <div class="menu-left" style="margin-left: -20px;">
                                    <button type="button" class="btn btn-default grid-select-all"><i
                                            class="far fa-square"></i></button>
                                </div>
                                <div class="menu-left">
                                    <span data-perm="unit:delete" class="btn btn-flat btn-danger grid-trash"
                                        title="{{ sc_language_render('action.delete') }}"><i
                                            class="fas fa-trash-alt"></i></span>
                                </div>
                            @endif

                        </div>

                    </div>
                </div>

                <div class="card-body p-0">
                    <section id="pjax-container" class="table-list">
                        <div class="box-body table-responsivep-0">
                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                    <tr>
                                        @if (!empty($removeList))
                                            <th style="width: 5%"></th>
                                        @endif
                                        @foreach ($listTh as $key => $th)
                                            <th style=" {!! $cssTh[$key] ?? '' !!} ">{!! $th !!}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dataTr as $keyRow => $tr)
                                        <tr>
                                            @if (!empty($removeList))
                                                <td style="padding-left: 12px; text-align: center">
                                                    <input class="checkbox grid-row-checkbox" type="checkbox"
                                                        data-id="{{ $keyRow }}">
                                                </td>
                                            @endif
                                            @foreach ($tr as $key => $trtd)
                                                <td style="{!! $cssTd[$key] ?? '' !!}">{!! $trtd !!}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="block-pagination clearfix m-10">
                                <div class="ml-3 float-left">
                                    {!! $resultItems ?? '' !!}
                                </div>
                                <div class="pagination pagination-sm mr-3 float-right">
                                    {!! $pagination ?? '' !!}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>



            </div>
        </div>

    </div>
    </div>
@endsection
@push('styles')
    <style>
        .type-unit {
            padding-top: 8px;
        }
    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}

    <script type="text/javascript">
        $('.grid-refresh').click(function() {
            $.pjax.reload({
                container: '#pjax-container'
            });
        });

        $(document).on('submit', '#button_search', function(event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function() {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function() {
            $('#loading').hide()
        })

        // tag a
        $(function() {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function() {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        @if ($buttonSort)
            $('#button_sort').click(function(event) {
                var url = '{{ $urlSort ?? '' }}?sort_shipping=' + $('#shipping_sort option:selected').val();
                $.pjax({
                    url: url,
                    container: '#pjax-container'
                })
            });
        @endif
    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
        {{-- sweetalert2 --}}
        var selectedRows = function() {
            var selected = [];
            $('.grid-row-checkbox:checked').each(function() {
                selected.push($(this).data('id'));
            });

            return selected;
        }

        $('.grid-trash').on('click', function() {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        function deleteItem(ids) {
            if (ids == "") {
                alertMsg('error', 'Cần chọn mục để xoá', 'Vui lòng chọn it nhât 1 bản ghi trước khi xoá đối tượng');
                return;
            }

            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ sc_language_render('action.delete_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ sc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ sc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function() {
                    return new Promise(function(resolve) {
                        $.ajax({
                            method: 'delete',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function(data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg,
                                        '{{ sc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    window.location.replace(
                                        '{{ sc_route_admin('admin_unit.index') }}'
                                        );
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}',
                        '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    result.dismiss === Swal.DismissReason.cancel
                ) {}
            })
        }
        {{-- / sweetalert2 --}}
    </script>

    {!! $js ?? '' !!}
@endpush
