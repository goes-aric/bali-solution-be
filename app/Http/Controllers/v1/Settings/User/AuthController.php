<?php
namespace App\Http\Controllers\v1\Settings\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\User\UserServices;

class AuthController extends BaseController
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

    public function login(Request $request){
        try {
            $rules = [
                'username'  => 'required',
                'password'  => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $user = $this->userServices->authUser($request);
            return $this->authSuccessResponse(self::HTTP_OK, 'Login berhasil', $user['data'], $user['token']);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function logout(){
        try {
            $user = $this->userServices->logoutUser();
            return $this->returnResponse('success', self::HTTP_OK, 'Anda telah logout!', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function register(Request $request){
        try {
            $rules = [
                'nama_pengguna'			=> 'required|string|max:255',
                'username'		        => 'required|string|max:255|alpha_dash|unique:users',
                'email'			        => 'required|string|email|max:255|unique:users',
                'password'		        => [
                    'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ],
                'password_confirmation'	=> [
                    'required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ]
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $user = $this->userServices->registerUser($request);
            return $this->returnResponse('success', self::HTTP_CREATED, 'berhasil', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_INTERNAL_SERVER_ERROR, $ex);
        }
    }
}
