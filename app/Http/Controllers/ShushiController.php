<?php

namespace App\Http\Controllers;

use App\Koza;
use App\Shushi;
use Illuminate\Http\Request;

class ShushiController extends Controller
{
    public function index()
    {
        $user = getUser();
        if (is_null($user)) {
            abort(401);
        }
        $limit = request()->input('limit');
        $offset = request()->input('offset');
        $category = request()->input('category');
        $month = request()->input('month');

        if (is_null($limit) || is_null($offset)) {
            abort(400);
        }

        $shushis = Shushi::where('user_id', $user->id);
        if (isset($category)){
            $shushis = $shushis->where('category_id', $category);
        }
        if (isset($month)){
            $year = substr ($month , 0, 4);
            $month = substr ($month , 4, 2);
            $shushis = $shushis->whereYear('hiduke', '=', $year);
            $shushis = $shushis->whereMonth('hiduke', '=', $month);
        }
        $shushis = $shushis->offset($offset)
        ->limit($limit)
        ->orderby('hiduke', 'desc')
        ->get();
        $shushisCount = Shushi::where('user_id', $user->id)->count();
        $response = [];
        $rireki = [];

        foreach($shushis as $shushi)
        {
            $koza = $shushi->koza;
            $beforeKoza = $shushi->beforeKoza;
            $category = $shushi->category;
            $shushi->subCategory;
            array_push($rireki, $shushi);
        }

        $response['rireki'] = $rireki;
        $response['pagerTotalCount'] = $shushisCount;
        return response()->json(
            $response,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function store()
    {
        $req = request()->all();
        $user = getUser();
        $shushi = new Shushi;
        $kbn = request()->input('shushi_kbn');
        $kingaku = request()->input('kingaku', 0);
        $koza_id = request()->input('koza_id');
        $before_koza_id = request()->input('before_koza_id');
        $koza = Koza::find($koza_id);
        if (isset($before_koza_id)){
            $before_koza = Koza::find($before_koza_id);
        }

        if (is_null($user)) {
            abort(401);
        }

        if ($user->id != $req['user_id'] || is_null($kbn) || is_null($koza) || $koza->user_id != $req['user_id']) {
            abort(400);
        }

        if($kbn == '0'){
            // 収入
            $koza->zandaka += $kingaku;
        } elseif($kbn == '1'){
            // 支出
            $koza->zandaka -= $kingaku;
        } else{
            // 振替
            if (is_null($before_koza) || $before_koza->user_id != $req['user_id']) {
                abort(400);
            }
            $koza->zandaka += $kingaku;
            $before_koza->zandaka -= $kingaku;
            $before_koza->save();
        }
        
        $shushi->fill($req)->save();
        $koza->save();
        $message = array('message' => '登録しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update()
    {
        $req = request()->all();
        $user = getUser();
        $id = request()->input('id');
        $kbn = request()->input('shushi_kbn');
        $kingaku = request()->input('kingaku', 0);
        $koza_id = request()->input('koza_id');
        $before_koza_id = request()->input('before_koza_id');
        $koza = Koza::find($koza_id);
        if (isset($before_koza_id)){
            $before_koza = Koza::find($before_koza_id);
        }

        if (is_null($user)) {
            abort(401);
        }
        $shushi = Shushi::where('user_id', $user->id)->where('id', $id)->first();

        if ($user->id != $req['user_id'] || is_null($kbn) || is_null($koza) 
            || $koza->user_id != $req['user_id'] || is_null($shushi)) {
            abort(400);
        }

        if($kbn == '0'){
            // 収入
            $koza->zandaka += ($kingaku - $shushi->kingaku);
        } elseif($kbn == '1'){
            // 支出
            $koza->zandaka -= ($kingaku - $shushi->kingaku);
        } else{
            // 振替
            if (is_null($before_koza) || $before_koza->user_id != $req['user_id']) {
                abort(400);
            }
            $koza->zandaka += ($kingaku - $shushi->kingaku);
            $before_koza->zandaka -= ($kingaku - $shushi->kingaku);
            $before_koza->save();
        }
        
        $shushi->fill($req)->save();
        $koza->save();
        $message = array('message' => '更新しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function delete()
    {
        $req = request()->all();
        $user = getUser();
        $id = request()->input('id');
        $kbn = request()->input('shushi_kbn');
        $koza_id = request()->input('koza_id');
        $before_koza_id = request()->input('before_koza_id');
        $koza = Koza::find($koza_id);
        if (isset($before_koza_id)){
            $before_koza = Koza::find($before_koza_id);
        }

        if (is_null($user)) {
            abort(401);
        }
        $shushi = Shushi::where('user_id', $user->id)->where('id', $id)->first();

        if ($user->id != $req['user_id'] || is_null($kbn) || is_null($koza) 
            || $koza->user_id != $req['user_id'] || is_null($shushi)) {
            abort(400);
        }

        if($kbn == '0'){
            // 収入
            $koza->zandaka -= $shushi->kingaku;
        } elseif($kbn == '1'){
            // 支出
            $koza->zandaka += $shushi->kingaku;
        } else{
            // 振替
            if (is_null($before_koza) || $before_koza->user_id != $req['user_id']) {
                abort(400);
            }
            $koza->zandaka -= $shushi->kingaku;
            $before_koza->zandaka += $shushi->kingaku;
            $before_koza->save();
        }
        
        $shushi->delete();
        $koza->save();
        $message = array('message' => '削除しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
