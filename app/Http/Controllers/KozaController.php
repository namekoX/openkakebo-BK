<?php

namespace App\Http\Controllers;

use App\Koza;
use Illuminate\Http\Request;

class KozaController extends Controller
{
    public function index()
    {
        $user = getUserInfo();
        if (is_null($user)) {
            abort(401);
        }
        $kozas = Koza::where('user_id', $user->id)->get();

        return response()->json(
            $kozas,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update()
    {
        $kozas = request()->all();
        $user = getUserInfo();
        if (is_null($user)) {
            abort(401);
        }

        foreach ($kozas as $koza) {
            if ($user->id !== $koza['user_id']) {
                abort(400);
            }
        }

        Koza::where('user_id', $user->id)
        ->delete();

        foreach ($kozas as $koza) {
            $koza['created_at'] = now();
            $koza['updated_at'] = now();
            Koza::insert(
                $koza
            );
        }

        $message = array('message' => '更新しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
