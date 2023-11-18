@extends($templatePathAdmin.'layout')

@section('main')
    @php
        $id = empty($id) ? 0 : $id;
    @endphp
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{!! $title_action !!}</h3>
                    @if ($layout == 'edit')
                        <div class="btn-group float-right" style="margin-right: 5px">
                            <a href="{{ $backLink }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    @endif
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main">
                    <div class="card-body">
                        <div class="form-group row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.zone.name') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-10 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name"
                                           value="{{ old() ? old('name') : $zone['name'] ?? '' }}"
                                           class="form-control name {{ $errors->has('name') ? ' is-invalid' : '' }}"
                                           required>
                                </div>

                                @if ($errors->has('name'))
                                    <span class="text-sm">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                    </span>
                                @endif

                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('description') ? ' text-red' : '' }}">
                            <label for="description"
                                   class="col-sm-2 col-form-label">{{ sc_language_render('admin.zone.code') }}
                                &nbsp;<span class="required-icon" title="{{sc_language_render('note.required-field')}}">*</span></label>
                            <div class="col-sm-10 ">
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="zone_code" name="zone_code"
                                           value="{{ old() ? old('zone_code') : $zone['zone_code'] ?? '' }}"
                                           class="form-control description {{ $errors->has('zone_code') ? ' is-invalid' : '' }}"
                                           required>
                                </div>

                                @if ($errors->has('zone_code'))
                                    <span class="text-sm text-red">
                <i class="fa fa-info-circle"></i> {{ $errors->first('zone_code') }}
              </span>
                                @endif

                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    @csrf
                    <div class="card-footer">
                        <button type="reset" class="btn btn-warning">{{ sc_language_render('action.reset') }}</button>
                        <button type="submit" data-perm="zone:create"
                                class="btn btn-primary float-right">{{ sc_language_render('action.submit') }}</button>
                    </div>
                    <!-- /.card-footer -->
                </form>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-th-list"></i> {!! $title ?? '' !!}</h3>
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
                </div>

                <div class="card-body p-0">
                    <section id="pjax-container" class="table-list">
                        <div class="box-body table-responsivep-0">

                            @if (!empty($removeList))
                                <div class="float-left p-3">
                                    <div class="menu-left">
                                        @if (!empty($removeList))
                                            <th style="text-align: center; width:1%; white-space:nowrap;">
                                                <button type="button" class="btn btn-default grid-select-all"><i
                                                            class="far fa-square"></i></button>
                                            </th>
                                        @endif
                                        <span data-perm="zone:delete" class="btn btn-danger grid-trash"
                                              title="{{ sc_language_render('action.delete') }}"><i
                                                    class="fas fa-trash-alt"></i></span>
                                    </div>

                                </div>
                            @endif

                            <table class="table table-hover box-body text-wrap table-bordered">
                                <thead>
                                <tr>
                                    <th></th>
                                    @foreach ($listTh as $key => $th)
                                        <th>{!! $th !!}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($dataTr as $keyRow => $tr)
                                    <tr class="{{ (request('id') == $keyRow) ? 'active': '' }}">
                                        @if (!empty($removeList))
                                            <td style="text-align: center; width: 1%; white-space:nowrap;">
                                                <input class="checkbox grid-row-checkbox" type="checkbox"
                                                       data-id="{{ $keyRow }}">
                                            </td>
                                        @endif
                                        @foreach ($tr as $key => $trtd)
                                            <td>{!! $trtd !!}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="block-pagination clearfix m-10">
                                <div class="ml-3 float-left">
                                    {!! $resultItems??'' !!}
                                </div>
                                <div class="pagination pagination-sm mr-3 float-right">
                                    {!! $pagination??'' !!}
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
    {!! $css ?? '' !!}
    <style>
        .table-bordered th:last-child, .table-bordered td:last-child {
            text-align: center;
            white-space: nowrap;
            width: 1%;
            padding: 8px 16px;
        }
    </style>
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ sc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script type="text/javascript">

        $('.grid-refresh').click(function () {
            $.pjax.reload({container: '#pjax-container'});
        });

        $(document).on('submit', '#button_search', function (event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide()
        })

        // tag a
        $(function () {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

        @if ($buttonSort)
        $('#button_sort').click(function (event) {
            var url = '{{ $urlSort??'' }}?sort_shipping=' + $('#shipping_sort option:selected').val();
            $.pjax({url: url, container: '#pjax-container'})
        });
        @endif

    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
        {{-- sweetalert2 --}}
        var selectedRows = function () {
            let selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });
            return selected;
        }

        $('.grid-trash').on('click', function () {
            let ids = selectedRows().join();
            deleteItem(ids);
        });

        function deleteItem(ids) {
            console.log(ids)
            if (ids === "") {
                alertMsg('error', 'Cần chọn để xoá', 'Vui lòng chọn it nhât 1 bản ghi trước khi xoá đối tượng');
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

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'delete',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', '{{ sc_language_render('action.warning') }}', data.msg);
                                    return;
                                } else {
                                    alertMsg('success', data.msg);
                                    window.location.replace('{{ sc_route_admin('admin_zone.index') }}');
                                    $.pjax.reload('#pjax-container');
                                    return;
                                }
                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ sc_language_render('action.delete_confirm_deleted_msg') }}', '{{ sc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {

                }
            })
        }
        {{--/ sweetalert2 --}}


    </script>

    {!! $js ?? '' !!}
@endpush
