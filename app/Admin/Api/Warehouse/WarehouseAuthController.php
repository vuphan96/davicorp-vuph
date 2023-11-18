<?php

namespace App\Admin\Api\Warehouse;

use App\Admin\Api\ApiController;
use App\Admin\Models\AdminCustomer;
use App\Admin\Models\AdminDriver;
use App\Front\Models\ShopDeviceToken;
use App\Front\Models\ShopSupplier;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Http\Requests\Api\SchoolmasterApiLoginRequest;
use App\Http\Requests\Api\WarehouseApiLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use function Maatwebsite\Excel\Cache\delete;

class WarehouseAuthController extends ApiController
{

    public function __construct()
    {
        Auth::setDefaultDriver('web');
    }

    public function postLogin(WarehouseApiLoginRequest $request)
    {
        try {

            $payload = $request->validated();
            $credentials = ['username' => $payload['username'], 'password' => $payload['password']];


            if (!$this->guard()->attempt($credentials)) {
                return $this->responseError([], Response::HTTP_UNAUTHORIZED, 'Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng kiểm tra lại!');
            }
            $user = $this->guard()->user();

//            Token::where('name', 'like', 'Warehouse:'.$user->customer_code . ";%")->update(['revoked'=> true]);

            $scope = ['warehouse'];
            $tokenResult = $user->createToken('Warehouse:' . $user->id .';'.now(), $scope);
            $token = $tokenResult->token;

            $token->save();

            // Save device token to send notification
            if ($payload['device_token']){
                $token = ShopDeviceToken::where('device_token', $payload['device_token'])->first();
                if ($token) {
                    $token->update(['device_token' => $payload['device_token'], 'customer_id' => $user->id]);
                } else {
                    $device_token = new ShopDeviceToken();
                    $device_token->customer_id = $user->id;
                    $device_token->device_token = $payload['device_token'];
                    $device_token->save();
                }
            }

            return $this->responseSuccess([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'role' => $payload['role'],
                'user' => $user,
                'device_token'=> $payload['device_token'] ?? '',
            ]);
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        try {
            $device_token = $request->device_token;
            ShopDeviceToken::where('device_token', $device_token)->delete();
            $user = Auth::guard('admin-api')->user();
            $user->token()->revoke();
            return $this->responseSuccess('logged out');
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }



// login and logout supplier
    public function loginSupplierApp(WarehouseApiLoginRequest $request){
        try {
            $payload = $request->validated();

            $user = (new ShopSupplier())->findForPassport($payload['username']);
            if(!$user){
                throw new \Exception('Tên đăng nhập không đúng, vui lòng kiểm tra lại');
            }
            $isValid = $user->validateForPassportPasswordGrant($payload['password']);
            if(!$isValid){
                throw new \Exception('Mật khẩu không chính xác, vui lòng kiểm tra lại');
            }

            $scope = ['supplier'];
            $tokenResult = $user->createToken('Supplier:' . $user->id .';'.now(), $scope);
            $token = $tokenResult->token;

            $token->save();

            // Save device token to send notification
            if ($payload['device_token']){
                $token = ShopDeviceToken::where('device_token', $payload['device_token'])->first();
                if ($token) {
                    $token->update(['device_token' => $payload['device_token'], 'customer_id' => $user->id]);
                } else {
                    $device_token = new ShopDeviceToken();
                    $device_token->customer_id = $user->id;
                    $device_token->device_token = $payload['device_token'];
                    $device_token->save();
                }
            }

            return $this->responseSuccess([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'role' => $payload['role'],
                'user' => $user,
                'device_token'=> $payload['device_token'] ?? '',
            ]);

        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
    public function logoutSupplier(Request $request)
    {
        try {
            $device_token = $request->device_token;
            ShopDeviceToken::where('device_token', $device_token)->delete();
            $user = Auth::guard('supplier')->user();
            $user->token()->revoke();
            return $this->responseSuccess('Đăng xuất thành công');
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }



// login and logout driver
    public function loginDriverApp(WarehouseApiLoginRequest $request){
        try {
            $payload = $request->validated();

            $user = (new AdminDriver())->findForPassport($payload['username']);
            if(!$user){
                throw new \Exception('Tên đăng nhập không đúng, vui lòng kiểm tra lại');
            }
            $isValid = $user->validateForPassportPasswordGrant($payload['password']);
            if(!$isValid){
                throw new \Exception('Mật khẩu không chính xác, vui lòng kiểm tra lại');
            }

            $scope = ['driver'];
            $tokenResult = $user->createToken('Driver:' . $user->id .';'.now(), $scope);
            $token = $tokenResult->token;

            $token->save();

            // Save device token to send notification
            if ($payload['device_token']){
                $token = ShopDeviceToken::where('device_token', $payload['device_token'])->first();
                if ($token) {
                    $token->update(['device_token' => $payload['device_token'], 'customer_id' => $user->id]);
                } else {
                    $device_token = new ShopDeviceToken();
                    $device_token->customer_id = $user->id;
                    $device_token->device_token = $payload['device_token'];
                    $device_token->save();
                }
            }

            return $this->responseSuccess([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString(),
                'role' => $payload['role'],
                'user' => $user,
                'device_token'=> $payload['device_token'] ?? '',
            ]);

        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
    public function logoutDriver(Request $request)
    {
        try {
            $device_token = $request->device_token;
            ShopDeviceToken::where('device_token', $device_token)->delete();
            $user = Auth::guard('driver')->user();
            $user->token()->revoke();
            return $this->responseSuccess('Đăng xuất thành công');
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }


    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function detect(Request $request){
        try {
            $username = $request['username'];

            if(AdminUser::where('username', $username)->first()){
                return $this->responseSuccess(['role' => 'warehouse']);
            }
            if(ShopSupplier::where('name_login', $username)->first()){
                return $this->responseSuccess(['role' => 'supplier']);
            }
            if(AdminDriver::where('login_name', $username)->first()){
                return $this->responseSuccess(['role' => 'driver']);
            }
        } catch (\Throwable $throwable){
            return $this->responseError($throwable->getMessage());
        }
    }
}
