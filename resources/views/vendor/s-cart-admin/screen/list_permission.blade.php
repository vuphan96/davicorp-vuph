@extends($templatePathAdmin.'layout')

@section('main')
    @php
        $id = empty($id) ? 0 : $id;
    @endphp

    <div class="row">

        <div class="col-md-6">

            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">
                        <h3 class="card-title">
                            {{--                            <a class="btn btn-warning btn-flat menu-sort-save" title="Save"><i--}}
                            {{--                                        class="fa fa-save"></i><span class="hidden-xs">&nbsp;Save</span></a>--}}
                        </h3>
                    </h3>
                </div>

                <div class="card-body p-0">
                    <div class="box-body table-responsive">
                        <div class="dd" id="menu-sort">
                            <ol class="dd-list">
                                {{-- Level 0 --}}
                                @if ($perms && $perms[0])
                                    @foreach ($perms[0] as $level0)
                                        @if ($level0->type ==1)
                                            <li class="dd-item " data-id="{{ $level0->id }}">
                                                <div class="dd-handle header-fix  {{ ($level0->id == $id)? 'active-item' : '' }}">
                                                    {!! $level0->name !!}
                                                    <span class="float-right dd-nodrag">
                                                        <a href="{{ sc_route_admin('admin_permission.edit',['id'=>$level0->id]) }}"><i
                                                                  class="fa fa-edit"></i></a>
                          &nbsp;
                                                        <a data-id="{{ $level0->id }}" class="remove_menu"><i class="fa fa-trash"></i></a>
                                                    </span>
                                                </div>
                                                {{-- Level 1 --}}
                                                @if (isset($perms[$level0->id]) && count($perms[$level0->id]))
                                                    <ol class="dd-list">
                                                        @foreach ($perms[$level0->id] as $level1)
                                                            <li class="dd-item" data-id="{{ $level1->id }}">
                                                                <div class="dd-handle {{ ($level1->id == $id)? 'active-item' : '' }}">
                                                                    {!! $level1->name !!}
                                                                    <span class="float-right dd-nodrag">
                                                                          <a href="{{ sc_route_admin('admin_permission.edit',['id'=>$level1->id]) }}"><i
                                                                                      class="fa fa-edit fa-edit"></i></a>
                                                                          &nbsp;
                                                                          <a data-id="{{ $level1->id }}" class="remove_menu"><i class="fa fa-trash fa-edit"></i></a>
                                                                      </span>
                                                                </div>
                                                            </li>
                                                        @endforeach
                                                    </ol>
                                                @endif
                                                {{-- end level 1 --}}
                                            </li>
                                        @else
                                            <li class="dd-item " data-id="{{ $level0->id }}">
                                                <div class="dd-handle {{ ($level0->id == $id)? 'active-item' : '' }}">
                                                    {!! $level0->name !!}
                                                    <span class="float-right dd-nodrag">
                                                      <a href="{{ sc_route_admin('admin_permission.edit',['id'=>$level0->id]) }}"><i
                                                                  class="fa fa-edit fa-edit"></i></a>
                                                      &nbsp;
                                                      <a data-id="{{ $level0->id }}" class="remove_menu"><i class="fa fa-trash fa-edit"></i></a>
                                                  </span>
                                                </div>

                                            </li>
                                        @endif

                                    @endforeach
                                    {{-- end level 0 --}}
                                @endif
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">

            <div class="card">
                <div class="card-header with-border">
                    <h3 class="card-title">{!! $title_form !!}</h3>
                    @if ($layout == 'edit')
                        <div class="card-tools">
                            <div class="btn-group float-right" style="margin-right: 5px">
                                <a href="{{ sc_route_admin('admin_permission.index') }}"
                                   class="btn  btn-flat btn-default"
                                   title="List"><i class="fa fa-list"></i><span
                                            class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                            </div>
                        </div>
                    @endif
                </div>

                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">

                    <div class="card-body">
                        <div class="fields-group">
                            <!--  Loại -->
                            <div class="form-group row  {{ $errors->has('type') ? ' text-red' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Loại quyền</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input name="type" id="type_0" type="radio" class="custom-control-input"
                                                   value="0"
                                                   {{ (old('parent_id', $permission['type'] ?? '') != 1) ? 'checked' : '' }} required>
                                            <label for="type_0" class="custom-control-label">Quyền</label>
                                        </div>
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input name="type" id="type_1" type="radio" class="custom-control-input"
                                                   value="1"
                                                   {{ (old('parent_id', $permission['type'] ?? '') == 1) ? 'checked' : '' }} required>
                                            <label for="type_1" class="custom-control-label">Nhóm quyền</label>
                                        </div>
                                    </div>
                                    @if ($errors->has('type'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <!-- // Loại -->
                            <!--  Nhóm quyền -->
                            <div id="role_group"
                                 class="form-group row  {{ $errors->has('parent_id') ? ' text-red' : ''}} {{ ($permission['type'] ?? '') ? 'd-none' : '' }}">
                                <label for="name" class="col-sm-2  control-label">Nhóm quyền</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <select name="parent_id" style="width: 100%" class="select2 form-control">
                                            <option value="" readonly>Chọn nhóm quyền</option>
                                            @foreach($permissionGroup as $permissionItem)
                                                <option {{ (old('parent_id', ($permission['parent_id'] ?? '')) == $permissionItem->id) ? 'selected' : '' }} value="{{ $permissionItem->id }}"> {{ $permissionItem->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if ($errors->has('parent_id'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <!-- // Nhóm quyền -->
                            <!-- Chọn nhóm quyền -->
                            <div class="form-group row  {{ $errors->has('name') ? ' text-red' : '' }}">
                                <label for="name"
                                       class="col-sm-2  control-label">{{ sc_language_render('admin.permission.name') }}</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                        </div>
                                        <input type="text" id="name" name="name"
                                               value="{{ old('name',$permission['name']??'')}}"
                                               class="form-control name" placeholder=""/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <!-- Slug -->
                            <div id="slug_block" class="form-group  row {{ $errors->has('slug') ? ' text-red' : '' }}">
                                <label for="slug"
                                       class="col-sm-2  control-label">{{ sc_language_render('admin.role.slug') }}</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                        </div>
                                        <input type="text" id="slug" name="slug"
                                               value="{{ old('slug',$permission['slug']??'') }}"
                                               class="form-control slug"
                                               placeholder=""/>
                                    </div>
                                    @if ($errors->has('slug'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('slug') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <!-- // Slug -->
                            {{-- select http_method --}}
                            <div id="uri_block"
                                 class="form-group row {{ $errors->has('http_uri') ? ' text-red' : '' }} {{ ($permission->type ?? '') ? 'd-none' : '' }}">
                                @php
                                    $old_http_uri = old('http_uri',isset($permission->http_uri)?explode(',', $permission->http_uri):[]);
                                @endphp
                                <label for="http_uri"
                                       class="col-sm-2  control-label">{{ sc_language_render('admin.permission.select_http_uri') }}</label>
                                <div class="col-sm-8">
                                    <select class="form-control input-sm http_uri select2" multiple="multiple"
                                            data-placeholder="{{ sc_language_render('admin.permission.method_placeholder') }}"
                                            style="width: 100%;" name="http_uri[]">
                                        <option value=""></option>
                                        @foreach ($routeAdmin as  $route)
                                            <option value="{{ $route['uri'] }}" {{ in_array($route['uri'], $old_http_uri)?'selected':'' }} >{{ $route['name']?$route['method'].'::'.$route['name']:$route['uri'] }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('http_uri'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('http_uri') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select http_uri --}}


                        </div>
                    </div>

                    <!-- /.card-body -->

                    <div class="card-footer row">
                        @csrf
                        <div class="col-md-2">
                        </div>

                        <div class="col-md-8">
                            <div class="btn-group float-right">
                                <button type="submit"
                                        class="btn btn-primary">{{ sc_language_render('action.submit') }}</button>
                            </div>

                            <div class="btn-group float-left">
                                <button type="reset"
                                        class="btn btn-warning">{{ sc_language_render('action.reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-footer -->

                </form>

            </div>
        </div>
    </div>

@endsection


@push('styles')
    <!-- Ediable -->
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/nestable/jquery.nestable.min.css')}}">
    <link rel="stylesheet" href="{{ sc_file('admin/plugin/iconpicker/fontawesome-iconpicker.min.css')}}">
@endpush

@push('scripts')
    <!-- Ediable -->

    <script src="{{ sc_file('admin/plugin/nestable/jquery.nestable.min.js')}}"></script>
    <script src="{{ sc_file('admin/plugin/iconpicker/fontawesome-iconpicker.min.js')}}"></script>

    <script type="text/javascript">
        $('.remove_menu').click(function (event) {
            var id = $(this).data('id');
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            })

            swalWithBootstrapButtons.fire({
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
                            method: 'post',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: id,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', 'Cancelled', data.msg);
                                    return;
                                } else {
                                    alertMsg('success', 'Success');
                                    window.location.replace('{{ sc_route_admin('admin_permission.index') }}');
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
                    // swalWithBootstrapButtons.fire(
                    //   'Cancelled',
                    //   'Your imaginary file is safe :)',
                    //   'error'
                    // )
                }
            })

        });


        $(document).ready(function () {
            $('.active-item').parents('li').removeClass('dd-collapsed');
            //icon picker
            $('.icon').iconpicker({placement: 'bottomLeft', animation: false});

            $("input[name='type']").change(function () {
                if ($("input[name='type']:checked").val() == 0) {
                    if ($("#role_group").hasClass('d-none')) {
                        $("#role_group").removeClass('d-none');
                        $("#uri_block").removeClass('d-none');
                        $("#slug_block").removeClass('d-none');
                    }
                } else {
                    if (!$("#role_group").hasClass('d-none')) {
                        $("#role_group").addClass('d-none');
                        $("#uri_block").addClass('d-none');
                        $("#slug_block").addClass('d-none');
                    }
                }
            });
        });

    </script>
@endpush