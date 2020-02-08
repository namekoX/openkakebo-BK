<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::options("/login",function(){
    });

Route::post("/login",function(){
    $email = request()->get("email");
    $password = request()->get("password");
    $user = \App\User::where("email",$email)->first();
    if ($user && Hash::check($password, $user->password)) {
     $token = str_random(64);
     $user->token = $token;
     $user->save();
     return [
      "user" => $user
     ];
    }else{
     abort(401);
    }
   });

Route::get("/mypage",function(){
$token = request()->bearerToken();
$user = \App\User::where("token",$token)->first();
if ($token && $user) {
    return [
    "user" => $user
    ];
}else{
    abort(401);
}
});

Route::post("/logout",function(){
$token = request()->bearerToken();
$user = \App\User::where("token",$token)->first();
if ($token && $user) {
    $user->token = null;
    $user->save();
    return [];
}else{
    abort(401);
}
});