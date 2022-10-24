<?php
namespace App\Http\Services\v1\Settings\User;

use Auth;
use Exception;
use App\Models\UserRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\v1\BaseServices;
use App\Http\Resources\v1\Settings\User\UserResource;

class UserServices extends BaseServices
{
    /* PRIVATE VARIABLE */
    private $model;
    private $userRolesModel;
    private $carbon;
    private $hash;
    private $str;
    private $moduleName;
    private $oldValues;
    private $newValues;

    public function __construct()
    {
        $this->model = $this->returnNewUserApp();
        $this->userRolesModel = new UserRoles();
        $this->carbon = $this->returnCarbon();
        $this->hash = $this->returnHash();
        $this->str = $this->returnStr();
        $this->moduleName = 'Users';
    }

    /* FETCH ALL USER */
    public function fetchLimit($props){
        try {
            /* GET DATA FOR PAGINATION AS A MODEL */
            $getAllData = $this->dataFilterPagination($this->model, [], null);
            $totalData = $getAllData->count();

            /* GET DATA WITH FILTER FOR PAGINATION AS A MODEL */
            $getFilterData = $this->dataFilterPagination($this->model, $props, null);
            $totalFiltered = $getFilterData->count();

            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilter($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $datas = $datas->with('roles')->get();
            $datas = UserResource::collection($datas);
            $users = [
                "total" => $totalData,
                "total_filter" => $totalFiltered,
                "per_page" => $props['take'],
                "current_page" => $props['skip'] == 0 ? 1 : ($props['skip'] + 1),
                "last_page" => ceil($totalFiltered / $props['take']),
                "from" => $totalFiltered === 0 ? 0 : ($props['skip'] != 0 ? ($props['skip'] * $props['take']) + 1 : 1),
                "to" => $totalFiltered === 0 ? 0 : ($props['skip'] * $props['take']) + $datas->count(),
                "show" => [
                    ["number" => 25, "name" => "25"], ["number" => 50, "name" => "50"], ["number" => 100, "name" => "100"]
                ],
                "data" => $datas
            ];

            return $users;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* FETCH USER BY ID */
    public function fetchById($id){
        try {
            $user = $this->model::with('roles')->find($id);
            if ($user) {
                $user = UserResource::make($user);
                return $user;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* CREATE NEW USER */
    public function createUser($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $user = $this->returnNewUserApp();
            $user->nama_pengguna    = $props['nama_pengguna'];
            $user->username         = $props['username'];
            $user->email            = $props['email'];
            $user->password         = bcrypt($props['password']);
            $user->status           = $props['status'];
            $user->save();

            /* LOOPING CHECKED ROLES */
            $roles = [];
            foreach ($props['roles'] as $role) {
                /* POPULATE USER ROLES */
                $roles[] = [
                    'user_id'       => $user->id,
                    'role_id'       => $role,
                    'created_at'    => $this->carbon::now(),
                    'updated_at'    => $this->carbon::now(),
                ];
            }
            /* INSERT USER ROLES  */
            $this->userRolesModel::insert($roles);

            /* WRITE LOG */
            $this->newValues = $this->model::find($user->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Membuat user baru [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            /* COMMIT DB TRANSACTION */
            DB::commit();

            $user = UserResource::make($user);
            return $user;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal membuat user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE USER */
    public function updateUser($props, $id){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $this->oldValues = $this->model::find($id);
            $user = $this->model::find($id);
            if ($user) {
                /* DELETE PREVIOUS ROLES */
                $this->userRolesModel::where('user_id', '=', $id)->delete();

                /* UPDATE USER */
                $user->nama_pengguna    = $props['nama_pengguna'];
                $user->username         = $props['username'];
                $user->email            = $props['email'];
                $user->status           = $props['status'];
                $user->update();

                /* LOOPING CHECKED ROLES */
                $roles = [];
                foreach ($props['roles'] as $role) {
                    /* POPULATE USER ROLES */
                    $roles[] = [
                        'user_id'       => $user->id,
                        'role_id'       => $role,
                        'created_at'    => $this->carbon::now(),
                        'updated_at'    => $this->carbon::now(),
                    ];
                }
                /* INSERT USER ROLES  */
                $this->userRolesModel::insert($roles);

                /* WRITE LOG */
                $this->newValues = $this->model::find($user->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Memperbaharui user [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                /* COMMIT DB TRANSACTION */
                DB::commit();

                $user = UserResource::make($user);
                return $user;
            } else {
                throw new Exception('Catatan tidak ditemukan!');
            }
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* UPDATE CURRENT / SIGNED USER PASSWORD */
    public function updatePassword($props){
        try {
            $authUser = $this->returnAuthUser();
            $this->oldValues = $this->model::find($authUser->id);
            $user = $this->model::find($authUser->id);

            // CHECK OLD PASSWORD WITH CURRENT PASSWORD
            if ($this->hash::check($props['current_password'], $user->password)){
                if ($this->hash::check($props['password'], $user->password)){
                    // UPDATE PASSWORD DENIED DUE TO CURRENT PASSWORD MATCH WITH OLD PASSWORD
                    throw new Exception('Password baru tidak boleh sama dengan password saat ini!');
                }else{
                    // UPDATE PASSWORD
                    $user->password   = bcrypt($props['password']);
                    $user->update();

                    /* WRITE LOG */
                    $this->newValues = $this->model::find($user->id);
                    $logParameters = [
                        'status'        => 'success',
                        'module'        => $this->moduleName,
                        'event'         => 'updated',
                        'description'   => 'Memperbaharui password [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                        'user_id'       => $this->returnAuthUser()->id ?? null,
                        'old_values'    => $this->oldValues,
                        'new_values'    => $this->newValues
                    ];
                    $this->writeActivityLog($logParameters);

                    return $user;
                }
            }else{
                throw new Exception('Password anda saat ini tidak cocok. Memperbaharui password dibatalkan!');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal memperbaharui password [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* RESET PASSWORD SELECTED USER (DEFAULT PASSWORD SAME AS USERNAME) */
    public function resetPassword($id){
        try {
            $this->oldValues = $this->model::find($id);
            $user = $this->model::find($id);

            if ($user) {
                $user->password = bcrypt($user->username);
                $user->update();

                /* WRITE LOG */
                $this->newValues = $this->model::find($user->id);
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'updated',
                    'description'   => 'Mengatur ulang password [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                return $user;
            }

            throw new Exception('Gagal mengatur ulang password!');
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'updated',
                'description'   => 'Gagal mengatur ulang password [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY USER */
    public function destroyUser($id){
        try {
            if ((int)$id !== (int)$this->returnAuthUser()->id) {
                $this->oldValues = $this->model::find($id);
                $user = $this->model::find($id);
                if ($user) {
                    $user->delete();

                    /* WRITE LOG */
                    $logParameters = [
                        'status'        => 'success',
                        'module'        => $this->moduleName,
                        'event'         => 'deleted',
                        'description'   => 'Menghapus user [ '.$this->oldValues->id.' - '. $this->oldValues->nama_pengguna.' ]',
                        'user_id'       => $this->returnAuthUser()->id ?? null,
                        'old_values'    => $this->oldValues,
                        'new_values'    => $this->newValues
                    ];
                    $this->writeActivityLog($logParameters);

                    return null;
                }

                throw new Exception('Catatan tidak ditemukan!');
            }

            throw new Exception("Permintaan pemrosesan gagal", 1);
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'deleted',
                'description'   => 'Gagal menghapus user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* DESTROY SELECTED / MULTIPLE USER */
    public function destroyMultipleUser($props){
        try {
            $this->oldValues = $this->model::whereIn('id', $props)->get();
            $users = $this->model::whereIn('id', $props);

            if ($users->count() > 0) {
                $users->delete();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'success',
                    'module'        => $this->moduleName,
                    'event'         => 'deleted',
                    'description'   => 'Menghapus user [ '.$this->oldValues.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                return null;
            }

            throw new Exception('Catatan tidak ditemukan!');
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'deleted',
                'description'   => 'Gagal menghapus user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* LOGIN FUNCTION AND GENERATE ACCESS TOKEN */
    public function authUser($props){
        try {
            /* DEFINE INPUT VARIABLE */
            $username   = $props['username'];
            $password   = $props['password'];

            /* CHECK LOGIN METHOD AND STORE FIELD METHOD TO VARIABLE */
            $usernameField = "username";
            $passwordField = "password";
            if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
                $usernameField = "email";
            }

            /* RUN AUTH ATTEMPT */
            if (Auth::attempt([$usernameField => $username, $passwordField => $password])) {
                // UPDATE LAST LOGIN TIME
                $user = $this->returnAuthUser();

                if ($user->status == 1) {
                    $update = $this->model::find($user->id);
                    $update->last_visit = $this->carbon::now();
                    $update->update();
                    $token = $user->createToken('myToken')->accessToken;

                    /* WRITE LOG */
                    $logParameters = [
                        'status'        => 'success',
                        'module'        => $this->moduleName,
                        'event'         => 'login',
                        'description'   => 'User login [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                        'user_id'       => $this->returnAuthUser()->id ?? null,
                        'old_values'    => $this->oldValues,
                        'new_values'    => $this->newValues
                    ];
                    $this->writeActivityLog($logParameters);

                    $user = UserResource::make($user);

                    $responseData = [
                        'data'  => $user,
                        'token' => $token
                    ];
                    return $responseData;
                }

                // LOGOUT USER
                Auth::logout();

                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'error',
                    'module'        => $this->moduleName,
                    'event'         => 'login',
                    'description'   => 'Login gagal karena akun tidak aktif [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                throw new Exception('Akun anda tidak aktif. Silakan hubungi administrator anda!');
            } else {
                /* WRITE LOG */
                $logParameters = [
                    'status'        => 'error',
                    'module'        => $this->moduleName,
                    'event'         => 'login',
                    'description'   => 'Username atau Password salah [ '.$usernameField.' ]',
                    'user_id'       => $this->returnAuthUser()->id ?? null,
                    'old_values'    => $this->oldValues,
                    'new_values'    => $this->newValues
                ];
                $this->writeActivityLog($logParameters);

                throw new Exception('Username atau Password salah');
            }
        } catch (Exception $ex) {
            /* WRITE LOG */
            $logParameters = [
                'status'        => 'error',
                'module'        => $this->moduleName,
                'event'         => 'login',
                'description'   => 'Gagal melakukan login [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* LOGOUT FUNCTION AND REVOKE ACCESS TOKEN */
    public function logoutUser(){
        try {
            // REVOKE TOKEN
            $user = $this->returnAuthUser();
            $user->token()->revoke();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'logout',
                'description'   => 'User logout [ '.$user->id.' - '.$user->nama_pengguna.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            return null;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* REGISTER A USER */
    public function registerUser($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            $create = $this->returnNewUserApp();
            $create->nama_pengguna  = $props['nama_pengguna'];
            $create->username       = $props['username'];
            $create->email          = $props['email'];
            $create->password       = bcrypt($props['password']);
            $create->status         = 0;
            $create->save();

            /* WRITE LOG */
            $this->newValues = $this->model::find($create->id);
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Mendaftar user [ '.$create->id.' - '.$create->nama_pengguna.' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            /* COMMIT DB TRANSACTION */
            DB::commit();

            return $create;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal mendaftar user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* FORGOT PASSWORD */
    public function forgotPassword($props){
        try {
            $notExists = $this->model::where('email', '=', $props['email'])->doesntExist();

            if ($notExists) {
                throw new Exception('Alamat email tidak ditemukan');
            }

            /* GENERATE TOKEN BY RANDOM STRING */
            $token = $this->str::random(25);

            /* CLEAN PASSWORD RESETS TABLE BASED ON EMAIL THAT WILL BE RESET */
            DB::table('password_resets')->where('email', '=', $props['email'])->delete();

            /* INSERT INTO PASSWORD RESETS TABLE */
            DB::table('password_resets')->insert([
                'email'         => $props['email'],
                'token'         => $token,
                'created_at'    => $this->carbon::now()
            ]);

            /* GET BASE URL OF FRONTEND ON ENV FILE */
            $baseUrl = $props['base_url'];

            /* SEND EMAIL LINK */
            $user = $this->model::where('email', '=', $props['email'])->first();
            $data = [
                'base_url'  => $baseUrl,
                'email'     => $props['email'],
                'token'     => $token
            ];
            Mail::send('emails.resetPassword', $data, function($message) use($props, $user) {
                $message->to($props['email'], $user->nama_pengguna);
                $message->subject('Permintaan pengaturan ulang password');
            });

            return null;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /* RESET PASSWORD BY USER WITH FORGOT PASSWORD METHOD */
    public function resetPasswordByUser($props){
        /* BEGIN DB TRANSACTION */
        DB::beginTransaction();

        try {
            /* CHECK IS TOKEN VALID OR EXISTS */
            $passwordReset = DB::table('password_resets')->where('token', '=', $props['token'])->first();
            if (!$passwordReset) {
                throw new Exception('Token tidak valid!');
            }

            /* CHECK IS EMAIL ADDRESS REGISTERED */
            $user = $this->model::where('email', '=', $passwordReset->email)->first();
            if (!$user) {
                throw new Exception('User tidak ditemukan!');
            }

            /* UPDATE USER PASSWORD WITH NEW */
            $user->password = bcrypt($props['password']);
            $user->update();

            /* REMOVE PASSWORD RESET REQUEST ON TABLE */
            DB::table('password_resets')->where('email', '=', $passwordReset->email)->where('token', '=', $props['token'])->delete();

            /* COMMIT DB TRANSACTION */
            DB::commit();

            return $user;
        } catch (Exception $ex) {
            /* ROLLBACK DB TRANSACTION */
            DB::rollback();

            /* WRITE LOG */
            $logParameters = [
                'status'        => 'success',
                'module'        => $this->moduleName,
                'event'         => 'created',
                'description'   => 'Gagal mendaftar user [ Pesan: '.$ex->getMessage().' ]',
                'user_id'       => $this->returnAuthUser()->id ?? null,
                'old_values'    => $this->oldValues,
                'new_values'    => $this->newValues
            ];
            $this->writeActivityLog($logParameters);

            throw $ex;
        }
    }

    /* FETCH ALL USER FOR OPTIONS */
    public function fetchDataOptions($props){
        try {
            /* GET DATA WITH FILTER AS A MODEL */
            $datas = $this->dataFilterPagination($this->model, $props, null);

            /* RETRIEVE ALL ROW, CONVERT TO ARRAY AND FORMAT AS RESOURCE */
            $users = $datas->select('id', 'nama_pengguna')->get();

            return $users;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
