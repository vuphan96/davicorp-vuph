<?php

namespace App\Admin\Api;

use App\Admin\Models\AdminCustomer;
use App\Front\Models\ShopDeviceToken;
use App\Http\Requests\Api\ApiLoginRequest;
use App\Http\Requests\Api\SchoolmasterApiLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;

class AuthController extends ApiController
{

    public function __construct()
    {
        Auth::setDefaultDriver('web');
    }

    /**
     * Create new customer
     */
    public function create(Request $request)
    {
        // Validation
        $rules = [
            'name' => 'required|string|max:255',
            'customer_code' => 'required|unique:"' . SC_DB_PREFIX . 'shop_customer"',
            'email' => 'required|string|max:150|unique:"' . SC_DB_PREFIX . 'shop_customer"',
            'password' => 'required|confirmed|string|min:6'
        ];

        $data = $request->all();
        $v = Validator::make($data, $rules);
        if ($v->fails()) {
            return $this->responseError($v->errors(), Response::HTTP_BAD_REQUEST, 'Params not valid!');
        }

        // Insert db
        DB::beginTransaction();
        try {
            $data = [
                'name' => $request->name,
                'customer_code' => $request->customer_code,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'store_id' => 1,
                'department_id' => 1,
                'tier_id' => 1,
                'status' => 1
            ];
            $user = AdminCustomer::createCustomer($data);
            DB::commit();
            return $this->responseSuccess();
        } catch (\Exception $e) {
            Log::warning(json_encode($e));
            DB::rollBack();
            return $this->responseError([], Response::HTTP_BAD_REQUEST, 'Error while create new customer!');
        }
    }

    public function login(ApiLoginRequest $request)
    {
        try {
            $credentials = request(['customer_code', 'password']);
            $credentials['status'] = 1;

            if (!$this->guard()->attempt($credentials)) {
                return $this->responseError([], Response::HTTP_UNAUTHORIZED, 'Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng kiểm tra lại!');
            }

            $user = $this->guard()->user();

            Token::where('name', 'like', 'Customer:'.$user->customer_code . ";%")->update(['revoked'=> true]);

            if ($user->status == 0) {
                $scope = ['user-guest'];
            } else {
                $scope = ['user'];
            }

            $tokenResult = $user->createToken('Customer:'.$user->customer_code.';'.now(), $scope);
            $token = $tokenResult->token;

            $token->save();

            // Save device token to send notification
            if ($request->device_token){
                $token = ShopDeviceToken::where('device_token', $request->device_token)->first();
                if ($token) {
                    $token->update(['device_token' => $request->device_token, 'customer_id' => $user->id]);
                } else {
                    $device_token = new ShopDeviceToken();
                    $device_token->customer_id = $user->id;
                    $device_token->device_token = $request->device_token;
                    $device_token->save();
                }
            }


            return $this->responseSuccess([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }

    public function schoolmasterLogin(SchoolmasterApiLoginRequest $request)
    {
        try {
            $credentials = request(['schoolmaster_code', 'schoolmaster_password']);
            $credentials['status'] = 1;

            if (!$this->guardSchoolmaster()->attempt($credentials)) {
                return $this->responseError([], Response::HTTP_UNAUTHORIZED, 'Tên đăng nhập hoặc mật khẩu không đúng. Vui lòng kiểm tra lại!');
            }

            $user = $this->guardSchoolmaster()->user();
            Token::where('name', 'like', 'Schoolmaster:'.$user->schoolmaster_code . ";%")->update(['revoked'=> true]);

            if ($user->status == 0) {
                $scope = ['user-guest'];
            } else {
                $scope = ['user'];
            }

            $tokenResult = $user->createToken('Schoolmaster:'.$user->schoolmaster_code .';'.now(), $scope);
            $token = $tokenResult->token;
            $token->save();

            return $this->responseSuccess([
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_UNAUTHORIZED, $e->getMessage());
        }
    }

    protected function guard()
    {
        return Auth::guard();
    }
    protected function guardSchoolmaster()
    {
        return Auth::guard("schoolmasters");
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            Token::where('name', 'like', 'Customer:'.$user->customer_code . ";%")->update(['revoked'=> true]);

            // Remove device token
            ShopDeviceToken::where('id', $user->id)->delete();
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }

    public function logoutSchoolmaster(Request $request)
    {
        try {
            $user = $request->user();
            Token::where('name', 'like', 'Schoolmaster:'.$user->schoolmaster_code . ";%")->update(['revoked'=> true]);
            return $this->responseSuccess();
        } catch (\Exception $e) {
            return $this->responseError([], Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}
