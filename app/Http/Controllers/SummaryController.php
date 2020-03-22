<?php

namespace App\Http\Controllers;

use App\Category;
use App\KokaiConfig;
use App\Shushi;
use Illuminate\Http\Request;
use App\Koza;

class SummaryController extends Controller
{
    public function index()
    {
        $user = getUserInfo();
        if (is_null($user)) {
            abort(401);
        }
        $month = request()->input('month');

        if (is_null($month)) {
            abort(400);
        }

        $year = substr($month, 0, 4);
        $month = substr($month, 4, 2);

        $shushis = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month);

        $categories = Category::where('user_id', $user->id)
            ->orderBy('category_order')
            ->get();

        $response = [];
        $rescategory = [];
        $ids = [];

        foreach ($categories as $category) {
            $gokei = 0;
            $shushisbyCategory = Shushi::where('user_id', $user->id)
                ->whereYear('hiduke', '=', $year)
                ->whereMonth('hiduke', '=', $month)
                ->where('category_id', $category->id)
                ->get();
            foreach ($shushisbyCategory as $shushi) {
                $gokei += $shushi->kingaku;
            }
            $rescategory[$category->category_name] = array('category_kbn'=>$category->category_kbn, 'kingaku'=>$gokei);
            array_push($ids, $category->id);
        }

        // 未分類を積み上げ
        $gokei = 0;
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '0')
            ->whereNotIn('category_id', $ids)
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '0')
            ->whereNull('category_id')
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }
        $rescategory['未分類の収入'] = array('category_kbn'=>'0', 'kingaku'=>$gokei);

        // 未分類を積み上げ
        $gokei = 0;
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '1')
            ->whereNotIn('category_id', $ids)
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '1')
            ->whereNull('category_id')
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }
        $rescategory['未分類の支出'] = array('category_kbn'=>'1', 'kingaku'=>$gokei);

        // 合計
        $gokei = 0;
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '0')
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }
        $response['shunyu'] = $gokei;

        $gokei = 0;
        $shushisbyCategory = Shushi::where('user_id', $user->id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month)
            ->where('shushi_kbn', '1')
            ->get();
        foreach ($shushisbyCategory as $shushi) {
            $gokei += $shushi->kingaku;
        }

        $kozas = Koza::where('user_id', $user->id)->get();

        $response['shishutu'] = $gokei;
        $response['category'] = $rescategory;
        $response['koza'] = $kozas;

        return response()->json(
            $response,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    function public () {
        $month = request()->input('month');
        $id = request()->input('id');

        if (is_null($id)) {
            abort(400);
        }

        $config = KokaiConfig::where('user_id', $id)->first();

        if (is_null($config)) {
            abort(400);
        }
        if ($config->is_open == '0') {
            // ページ非公開
            abort(403);
        }

        $togetu = false;
        if (is_null($month)) {
            if ($config->is_togetu == '0') {
                $month = date("Ym", strtotime("-1 month"));
                $togetu = true;
            } else {
                $month = date("Ym");
            }
        }
        if ($config->is_togetu == '0' && $month == date("Ym")) {
            //当月非公開
            abort(403);
        }
        $response = [];
        $response['month'] = $month;
        $year = substr($month, 0, 4);
        $month = substr($month, 4, 2);

        $shushis = Shushi::where('user_id', $id)
            ->whereYear('hiduke', '=', $year)
            ->whereMonth('hiduke', '=', $month);

        $categories = Category::where('user_id', $id)
            ->orderBy('category_order')
            ->get();

        $rescategory = [];
        $ids = [];

        if ($config->is_shunyu_category == '1' || $config->is_shishutu_category == '1') {
            
            foreach ($categories as $category) {
                if (($config->is_shunyu_category == '1' && $category->category_kbn == '0') 
                || ($config->is_shishutu_category == '1' &&$category->category_kbn == '1')) {
                    $gokei = 0;
                    $shushisbyCategory = Shushi::where('user_id', $id)
                    ->whereYear('hiduke', '=', $year)
                    ->whereMonth('hiduke', '=', $month)
                    ->where('category_id', $category->id)
                    ->get();
                    foreach ($shushisbyCategory as $shushi) {
                        $gokei += $shushi->kingaku;
                    }
                    $rescategory[$category->category_name] = array('category_kbn'=>$category->category_kbn, 'kingaku'=>$gokei);
                    array_push($ids, $category->id);
                }
            }

            // 未分類を積み上げ
            if ($config->is_shunyu_category == '1'){
                $gokei = 0;
                $shushisbyCategory = Shushi::where('user_id', $id)
                    ->whereYear('hiduke', '=', $year)
                    ->whereMonth('hiduke', '=', $month)
                    ->where('shushi_kbn', '0')
                    ->whereNotIn('category_id', $ids)
                    ->get();
                foreach ($shushisbyCategory as $shushi) {
                    $gokei += $shushi->kingaku;
                }
                $shushisbyCategory = Shushi::where('user_id', $id)
                    ->whereYear('hiduke', '=', $year)
                    ->whereMonth('hiduke', '=', $month)
                    ->where('shushi_kbn', '0')
                    ->whereNull('category_id')
                    ->get();
                foreach ($shushisbyCategory as $shushi) {
                    $gokei += $shushi->kingaku;
                }
                $rescategory['未分類の収入'] = array('category_kbn'=>'0', 'kingaku'=>$gokei);
            }

            // 未分類を積み上げ
            if ($config->is_shishutu_category == '1'){
                $gokei = 0;
                $shushisbyCategory = Shushi::where('user_id', $id)
                    ->whereYear('hiduke', '=', $year)
                    ->whereMonth('hiduke', '=', $month)
                    ->where('shushi_kbn', '1')
                    ->whereNotIn('category_id', $ids)
                    ->get();
                foreach ($shushisbyCategory as $shushi) {
                    $gokei += $shushi->kingaku;
                }
                $shushisbyCategory = Shushi::where('user_id', $id)
                    ->whereYear('hiduke', '=', $year)
                    ->whereMonth('hiduke', '=', $month)
                    ->where('shushi_kbn', '1')
                    ->whereNull('category_id')
                    ->get();
                foreach ($shushisbyCategory as $shushi) {
                    $gokei += $shushi->kingaku;
                }
                $rescategory['未分類の支出'] = array('category_kbn'=>'1', 'kingaku'=>$gokei);
            }
        }
        // 合計
        if ($config->is_shunyu == '1'){
            $gokei = 0;
            $shushisbyCategory = Shushi::where('user_id', $id)
                ->whereYear('hiduke', '=', $year)
                ->whereMonth('hiduke', '=', $month)
                ->where('shushi_kbn', '0')
                ->get();
            foreach ($shushisbyCategory as $shushi) {
                $gokei += $shushi->kingaku;
            }
            $response['shunyu'] = $gokei;
        }

        if ($config->is_shishutu == '1'){
            $gokei = 0;
            $shushisbyCategory = Shushi::where('user_id', $id)
                ->whereYear('hiduke', '=', $year)
                ->whereMonth('hiduke', '=', $month)
                ->where('shushi_kbn', '1')
                ->get();
            foreach ($shushisbyCategory as $shushi) {
                $gokei += $shushi->kingaku;
            }
            $response['shishutu'] = $gokei;
        }

        $response['category'] = $rescategory;
        $response['togetu'] = $togetu;
        $response['is_shishutu'] = ($config->is_shishutu == '1');
        $response['is_shunyu'] = ($config->is_shunyu == '1');
        $response['is_shishutu_category'] = ($config->is_shishutu_category == '1');
        $response['is_shunyu_category'] = ($config->is_shunyu_category == '1');

        return response()->json(
            $response,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
