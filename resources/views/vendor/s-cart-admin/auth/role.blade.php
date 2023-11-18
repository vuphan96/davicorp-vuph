@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_role.index') }}" class="btn  btn-flat btn-default"
                               title="List"><i class="fa fa-list"></i><span
                                        class="hidden-xs"> {{ sc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal"
                      id="form-main" enctype="multipart/form-data">
                    <div class="card-body">
                        <div class="fields-group">
                            <div class="form-group  row {{ $errors->has('name') ? ' text-red' : '' }}">
                                <label for="name"
                                       class="col-sm-2  control-label">{{ sc_language_render('admin.role.name') }}</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                        </div>
                                        <input type="text" id="name" name="name"
                                               value="{{ old('name',$role['name']??'')}}" class="form-control name"
                                               placeholder=""/>
                                    </div>
                                    @if ($errors->has('name'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            <div id="slug_block" class="form-group  row {{ $errors->has('slug') ? ' text-red' : '' }}">
                                <label for="slug"
                                       class="col-sm-2  control-label">{{ sc_language_render('admin.role.slug') }}</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                        </div>
                                        <input type="text" id="slug" name="slug"
                                               value="{{ old('slug',$role['slug']??'') }}" class="form-control slug"
                                               placeholder=""/>
                                    </div>
                                    @if ($errors->has('slug'))
                                        <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('slug') }}
                                            </span>
                                    @endif
                                </div>
                            </div>
                            {{-- select customer --}}
                            <div class="form-group row kind  {{ $errors->has('unit_id') ? ' text-red' : '' }}">
                                <label for="supplier_id"
                                       class="col-sm-2 col-form-label">Danh sách quyền</label>
                                <div class="col-sm-8">
                                    <input name="permission_id" id="permission_id" value="" hidden>
                                    <div class="customer-container mt-3">
                                        <div id="treeview" class="hummingbird-treeview-converter"
                                             data-boldParents="true">
                                            @foreach($listPermission as $id => $permission)
                                                <li data-id="{{ $id }}">{{ $permission }}</li>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- //select customer --}}


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
    <link href="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.css"
          rel="stylesheet">
    <style>
        ul#treeview li i {
            margin-left: -19px;
            font-size: 18px;
        }

        ul#treeview label:not(.form-check-label):not(.custom-file-label) {
            font-weight: 400;

        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/gh/hummingbird-dev/hummingbird-treeview@v3.0.4/hummingbird-treeview.min.js"></script>
    <script type="text/javascript">
        $.fn.hummingbird.defaults.SymbolPrefix = "far";
        $.fn.hummingbird.defaults.collapsedSymbol = "fa-plus-square";
        $.fn.hummingbird.defaults.expandedSymbol = "fa-minus-square";
        $.fn.hummingbird.defaults.checkDoubles = true;
        $.fn.hummingbird.defaults.boldParent = true;
        $.fn.hummingbird.defaults.singleGroupOpen = 2;
        // Define
        let treeView = $("#treeview");
        let permissionInput = $("#permission_id");
        let selectedPermission = [];
        let currentPermission = JSON.parse('{!! json_encode($currentPermission ?? []) !!}');

        $(document).ready(function () {
            $("#treeview").hummingbird();
            $("#treeview").hummingbird("checkNode", {
                sel: "data-id", vals: currentPermission.map((item) => {
                    return item.id
                })
            });
            updatePermissionInfo();
            $("#treeview").on("click", function () {
                updatePermissionInfo();
            });
        });

        function updatePermissionInfo() {
            let List = {"id": [], "dataid": [], "text": []};
            $("#treeview").hummingbird("getChecked", {
                list: List,
                onlyEndNodes: true,
                onlyParents: false,
                fromThis: false
            });
            selectedPermission = List.dataid
            permissionInput.val(selectedPermission.join(','));
        }
    </script>

@endpush
