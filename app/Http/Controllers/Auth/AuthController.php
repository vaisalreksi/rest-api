<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Models\Setup\Users;
use Carbon\Carbon;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $credential = $request->only('username','password');
        $token = JWTAuth::attempt($credential);
        try {
            $cekUser = Users::where('username',$input['username'])->first();
            if(empty($cekUser)){
                return \Response::json(['message'=>'User tidak ditemukan','success'=>false],404);
            }

            if(Hash::check($input['password'], $cekUser->password)) {
                if($cekUser->status == 0){
                  $lastlogin = Users::find($cekUser->id);
                  $lastlogin->last_login = Carbon::now();
                  $lastlogin->save();
                    if(!$token = JWTAuth::attempt($credential)){
                        return \Response::json(['message'=>'kata sandi tidak sesuai','success'=>false],404);
                    }
                }else{
                    return \Response::json(['message'=>'User tidak aktif','success'=>false],404);
                }
            }else{
                return \Response::json(['message'=>'kata sandi tidak sesuai','success'=>false],404);
            }
        } catch (JWTException $e) {
            return \Response::json(['message'=>'Terjadi kesalahan','success'=>false],500);
        }
        return \Response::json(['token'=>$token,'success'=>true],200);
        // return \Response::json(compact('token'));
    }

    public function refresh(Request $request)
    {
        $input = $request->all();

        try{
            $token = JWTAuth::refresh($input['token']);
            $id = JWTAuth::toUser($token);
            return \Response::json([
                'success' => true,
                'token' => $token,
                'id' => $id->id,
                'lang' => $id->language_id,
                'name' => $id->name
            ],200);

        }catch(JWTException $e){
            return \Response::json([
                'success' => false,
                'message' => 'Token Error',
            ],401);
        }
    }

    public function logout(Request $request)
    {
        $input = $request->all();

        try{
            $user = JWTAuth::toUser($input['token']);
            JWTAuth::setToken($input['token'])->invalidate();
            return \Response::json([
                'success' => true,
                'message' => 'Logout successful!',
            ],200);

        }catch(JWTException $e){
            return \Response::json([
                'success' => false,
                'message' => 'An error occurred while performing an action!',
            ],401);
        }
    }
}
