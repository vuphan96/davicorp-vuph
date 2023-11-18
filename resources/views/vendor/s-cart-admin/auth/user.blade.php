@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ sc_route_admin('admin_user.index') }}" class="btn  btn-flat btn-default"
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
                        <div class="form-group  row {{ $errors->has('name') ? ' text-red' : '' }}">
                            <label for="name"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.name') }}
                                &nbsp;<span class="required-icon"
                                            title="{{sc_language_render('note.required-field')}}">*</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="name" name="name" value="{{ old('name',$user['name']??'')}}"
                                           class="form-control name" placeholder=""/>
                                </div>
                                @if ($errors->has('name'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('name') }}
                                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group  row {{ $errors->has('username') ? ' text-red' : '' }}">
                            <label for="username"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.user_name') }}
                                <span class="required-icon"
                                      title="{{sc_language_render('note.required-field')}}">*</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="username" name="username"
                                           value="{{ old('username',$user['username']??'') }}"
                                           class="form-control username" placeholder=""/>
                                </div>
                                @if ($errors->has('username'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('username') }}
                                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group  row {{ $errors->has('email') ? ' text-red' : '' }}">
                            <label for="email"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.email') }}
                                <span class="required-icon"
                                      title="{{sc_language_render('note.required-field')}}">*</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="text" id="email" name="email"
                                           value="{{ old('email',$user['email']??'') }}" class="form-control email"
                                           placeholder=""/>
                                </div>
                                @if ($errors->has('email'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('email') }}
                                            </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group  row {{ $errors->has('avatar') ? ' text-red' : '' }}">
                            <label for="avatar"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.avatar') }}</label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="text" id="avatar" name="avatar"
                                           value="{{ old('avatar',$user['avatar']??'') }}" class="form-control avatar1"
                                           placeholder=""/>
                                    <span class="input-group-btn">
                                         <a data-input="avatar" data-preview="preview_avatar" data-type="avatar"
                                            class="btn btn-primary lfm">
                                           <i class="fa fa-image"></i> {{sc_language_render('product.admin.choose_image')}}
                                         </a>
                                       </span>
                                </div>
                                @if ($errors->has('avatar'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('avatar') }}
                                            </span>
                                @endif
                                <div id="preview_avatar" class="img_holder">
                                    @if (old('avatar',$user['avatar']??''))
                                        <img src="{{ sc_file(old('avatar',$user['avatar']??'')) }}">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group  row {{ $errors->has('password') ? ' text-red' : '' }}">
                            <label for="password"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.password') }}
                                <span class="required-icon"
                                      title="{{sc_language_render('note.required-field')}}">*</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="password" name="password"
                                           value="{{ old('password')??'' }}" class="form-control password"
                                           placeholder=""/>
                                </div>
                                @if ($errors->has('password'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('password') }}
                                            </span>
                                @else
                                    @if ($user)
                                        <span class="form-text">
                                                     {{ sc_language_render('admin.user.keep_password') }}
                                                 </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="form-group  row {{ $errors->has('password_confirmation') ? ' text-red' : '' }}">
                            <label for="password"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.password_confirmation') }}
                                <span class="required-icon"
                                      title="{{sc_language_render('note.required-field')}}">*</span>
                            </label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                                    </div>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           value="{{ old('password_confirmation')??'' }}"
                                           class="form-control password_confirmation" placeholder=""/>
                                </div>
                                @if ($errors->has('password_confirmation'))
                                    <span class="form-text">
                                                <i class="fa fa-info-circle"></i> {{ $errors->first('password_confirmation') }}
                                            </span>
                                @else
                                    @if ($user)
                                        <span class="form-text">
                                                     {{ sc_language_render('admin.user.keep_password') }}
                                                 </span>
                                    @endif
                                @endif
                            </div>
                        </div>

                        {{-- select roles --}}
                        <div class="form-group row {{ $errors->has('roles') ? ' text-red' : '' }}">
                            @php
                                $listRoles = [];
                                    $old_roles = old('roles',($user)?$user->roles->pluck('id')->toArray():'');
                                    if(is_array($old_roles)){
                                        foreach($old_roles as $value){
                                            $listRoles[] = (int)$value;
                                        }
                                    }
                            @endphp
                            <label for="roles"
                                   class="col-sm-2  control-label">{{ sc_language_render('admin.user.select_roles') }}</label>
                            <div class="col-sm-8">

                                @if (isset($user['id']) && in_array($user['id'], SC_GUARD_ADMIN))
                                    @if (count($listRoles))
                                        @foreach ($listRoles as $role)
                                            {!! '<span class="badge badge-primary">'.($roles[$role]??'').'</span>' !!}
                                        @endforeach
                                    @endif
                                @else
                                    <select class="form-control roles select2" data-placeholder="{{ sc_language_render('admin.user.select_roles') }}"
                                            style="width: 100%;" name="roles">
                                        <option value=""></option>
                                        @foreach ($roles as $k => $v)
                                            <option value="{{ $k }}" {{ (count($listRoles) && in_array($k, $listRoles))?'selected':'' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('roles'))
                                        <span class="form-text">
                                            {{ $errors->first('roles') }}
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </div>
                        @if($canEditOrder)
                            <div class="form-group  row {{ $errors->has('email') ? ' text-red' : '' }}">
                                <label for="email"
                                       class="col-sm-2  control-label">Hiệu lực sửa đơn hàng
                                </label>
                                <div class="col-sm-8">
                                    <div class="form-row align-items-center">
                                        <div class="col-6">
                                            <label class="sr-only" for="davicorp_locktime">Hiệu lực sửa đơn Davicorp</label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Davicorp</div>
                                                </div>
                                                <input type="text" class="form-control date_time_flat" id="davicorp_locktime" name="davicorp_locktime" placeholder="(Ngày/Tháng/Năm)" value="{{ old("davicorp_locktime", $dataLockTime ? $dataLockTime->davicorp_due_time : "") }}">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <label class="sr-only" for="davicook_locktime"></label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Davicook</div>
                                                </div>
                                                <input type="text" class="form-control date_time_flat" id="davicook_locktime" name="davicook_locktime" placeholder="(Ngày/Tháng/Năm)" value="{{ old("davicook_locktime", $dataLockTime ? $dataLockTime->davicook_due_time : "") }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{-- //select roles --}}
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <script type="text/javascript">
        $(document).ready(() => {
            $(".date_time_flat").flatpickr({
                altInput: true,
                altFormat: "d/m/Y",
                locale: "vn",
                allowInput: true
            });
        });
    </script>
@endpush
