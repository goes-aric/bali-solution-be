<?php
namespace App\Http\Controllers\v1\Settings\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\v1\BaseController;
use App\Http\Services\v1\Settings\User\UserServices;

class UserController extends BaseController
{
    private $userServices;

    public function __construct(UserServices $userServices)
    {
        $this->userServices = $userServices;
    }

    public function index(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $users = $this->userServices->fetchLimit($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar user', $users);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function store(Request $request)
    {
        try {
            $rules = [
                'nama_pengguna'			=> 'required|string|max:191',
                'username'		        => 'required|string|max:191|alpha_dash|unique:users',
                'email'			        => 'required|string|email|max:191|unique:users',
                'password'              => [
                    'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ],
                'password_confirmation'	=> [
                    'required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ],
                'status'                => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $user = $this->userServices->createUser($request);
            return $this->returnResponse('success', self::HTTP_OK, 'User berhasil dibuat', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function show($id)
    {
        try {
            $user = $this->userServices->fetchById($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Detail user', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $rules = [
                'nama_pengguna' => 'required|string|max:191',
                'username'      => 'required|string|max:191|alpha_dash|unique:users,username,'.$id.'',
                'email'         => 'required|email|max:191|unique:users,email,'.$id.'',
                'status'        => 'required',
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $user = $this->userServices->updateUser($request, $id);
            return $this->returnResponse('success', self::HTTP_OK, 'User berhasil diperbaharui', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $rules = [
                'current_password'      => 'required|string|min:5',
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

            $user = $this->userServices->updatePassword($request);
            return $this->returnResponse('success', self::HTTP_OK, 'Password telah diperbaharui', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function resetPassword($id)
    {
        try {
            $user = $this->userServices->resetPassword($id);
            return $this->returnResponse('success', self::HTTP_OK, 'User berhasil diatur ulang', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroy($id)
    {
        try {
            $user = $this->userServices->destroyUser($id);
            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function destroyMultiple(Request $request)
    {
        try {
            $props = $request->data;
            $users = $this->userServices->destroyMultipleUser($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Catatan berhasil dihapus!', $users);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function forgotPassword(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|string|email|max:255'
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $props = [
                'base_url'  => config('app.base_url'),
                'email'     => $request['email']
            ];
            $user = $this->userServices->forgotPassword($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Permintaan telah dikirimkan ke alamat email', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function resetPasswordByUser(Request $request)
    {
        try {
            $rules = [
                'password'		        => [
                    'required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ],
                'password_confirmation'	=> [
                    'required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()
                ],
                'token'                 => 'required'
            ];
            $validator = $this->returnValidator($request->all(), $rules);
            if ($validator->fails()) {
                return $this->returnResponse('error', self::HTTP_UNPROCESSABLE_ENTITY, $validator->errors());
            }

            $user = $this->userServices->resetPasswordByUser($request);
            return $this->returnResponse('success', self::HTTP_OK, 'Password telah diatur ulang!', $user);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }

    public function fetchDataOptions(Request $request)
    {
        try {
            $props = $this->getBaseQueryParams($request, []);
            $users = $this->userServices->fetchDataOptions($props);

            return $this->returnResponse('success', self::HTTP_OK, 'Daftar user', $users);
        } catch (Exception $ex) {
            return $this->returnExceptionResponse('error', self::HTTP_BAD_REQUEST, $ex);
        }
    }
}
