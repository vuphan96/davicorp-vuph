@extends($templatePathAdmin.'layout_portable')

@section('main')
@include($templatePathAdmin.'component.css_login')
    <div class="container-login100">
      <div class="wrap-login100 main-login">
        <form action="{{ sc_route_admin('admin.login') }}" method="post">
          <div class="login-title-des col-md-12 p-b-41">
            <a><b>{{sc_language_render('admin.login')}}</b></a>
          </div>
          <div class="col-md-12 form-group has-feedback {!! !$errors->has('username') ?: 'text-red' !!}">
            <div class="wrap-input100 validate-input form-group ">
              <input class="input100 form-control" type="text" placeholder="{{ sc_language_render('admin.user.username') }}"
                name="username" value="{{ old('username') }}" required>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-user"></i>
              </span>
            </div>
            @if($errors->has('username'))
              @foreach($errors->get('username') as $message)
                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
              @endforeach
            @endif
          </div>
          <div class="col-md-12 form-group has-feedback {!! !$errors->has('password') ?: 'text-red' !!}">
            <div class="wrap-input100 validate-input form-group ">
              <input class="input100 form-control" type="password" placeholder="{{ sc_language_render('admin.user.password') }}"
                name="password" required>
              <span class="focus-input100"></span>
              <span class="symbol-input100">
                <i class="fa fa-lock"></i>
              </span>
            </div>
            @if($errors->has('password'))
              @foreach($errors->get('password') as $message)
                <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
              @endforeach
            @endif
          </div>
          <div class="col-md-12">
            <div class="container-login-btn">
              <button class="login-btn" type="submit">
                {{ sc_language_render('admin.user.login') }}
              </button>
            </div>
            {{-- <div class="text-center">
              <a href="" class="forgot">
                <i class="fa fa-caret-right"></i> <b>Forgot Password</b>
              </a>
            </div> --}}

            <div class="checkbox input text-center remember">
              <label>
                <input class="checkbox" type="checkbox" name="remember" value="1"
                  {{ (old('remember')) ? 'checked' : '' }}>
                <b>{{ sc_language_render('admin.user.remember_me') }}</b>
              </label>
            </div>

          </div>
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
      </div>
    </div>
<!-- Modal -->
<div class="modal fade" id="loginAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Cảnh báo đăng nhập</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tài khoản của bạn đã được đăng nhập từ thiết bị khác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
    @endsection


    @push('styles')
    <style type="text/css">
      .container-login100 {
        background-image: url({!! sc_file('images/bg-system.jpg') !!});
      }
    </style>
    @endpush

    @push('scripts')
    <script>
      $(function () {
        $('.checkbox').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' /* optional */
        });
      });
          @if(Session::has('login-alert') && (Session::get('login-alert') == 'force-logout'))
            $('#loginAlertModal').modal('show')
          @endif
    </script>
    @endpush