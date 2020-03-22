<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\KokaiConfig;

class KokaiConfigController extends Controller
{
    public function index()
    {
        $user = getUserInfo();
        if (is_null($user)) {
            abort(401);
        }
        $config = KokaiConfig::where('user_id', $user->id)->first();

        return response()->json(
            $config,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update()
    {
        $req = request()->all();
        $id = request()->input('id');
        $user = getUserInfo();
        if (is_null($user)) {
            abort(401);
        }

        $config = KokaiConfig::where('user_id', $user->id)->where('id', $id)->first();

        if (is_null($config)) {
            abort(400);
        }

        $config->fill($req)->save();

        $message = array('message' => '更新しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
