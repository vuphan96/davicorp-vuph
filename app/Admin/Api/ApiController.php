<?php

namespace App\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    public function __construct()
    {
        $this->templatePathAdmin = config('admin.path_view');
    }

    public function responseError($errors = [], $statusCode = Response::HTTP_BAD_REQUEST, $msg = 'Failed')
    {
        $data = [
            'error' => true,
            'message' => $msg,
            'data' => $errors,
        ];
        return response()->json( $data, $statusCode);
    }

    public function responseSuccess($data = '', $statusCode = Response::HTTP_OK, $msg = 'Successful')
    {
        $data = [
            'error' => false,
            'message' => $msg,
            'data' => $data,
        ];
        return response()->json( $data, $statusCode);
    }
}
