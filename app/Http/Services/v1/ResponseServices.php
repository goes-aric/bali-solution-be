<?php
namespace App\Http\Services\v1;

class ResponseServices
{
    public function returnResponse($status = 'success', $statusCode = 200, $message, $data = null){
        $response  = [
            'status'        => $status,
            'status_code'   => $statusCode,
            'message'       => $message,
            'data'          => $data
        ];
        return $response;
    }

    public function returnExceptionResponse($code = 400, $message, $exception){
        $exResponse = [];
        switch (true) {
            case \Config::get('app.debug') === true:
                $exResponse['status_code']  = $exception->getCode();
                $exResponse['message']      = $exception->getMessage();
                $exResponse['line']         = $exception->getLine();
                $exResponse['file']         = $exception->getFile();
                $exResponse['trace']        = $exception->getTrace();
                break;

            default:
                $exResponse['message'] = $message;
                break;
        }

        $response  = [
            'status'        => 'error',
            'status_code'   => $exResponse['status_code'] ?? $code,
            'message'       => $exResponse['message'],
            'data'          => $exResponse
        ];
        return $response;
    }

    public function authSuccessResponse($code = 200, $message, $data = null, $token = null){
        $response  = [
            'status'        => 'success',
            'status_code'   => $code,
            'access_token'  => $token,
            'message'       => $message,
            'data'          => $data
        ];
        return $response;
    }
}
