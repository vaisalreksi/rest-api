<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class VerifyJWTToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::toUser($request->input('token'));
        } catch (JWTException $e) {
            if($e instanceOf \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return \Response::json(['success'=>false,'message'=>'Token Expired'],$e->getStatusCode());
            }elseif($e instanceOf \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return \Response::json(['success'=>false,'message'=>'Token invalid'],$e->getStatusCode());
            }else{
                return \Response::json(['success'=>false,'message'=>'Token is required'],500);
            }
        }
        return $next($request);
    }
}
