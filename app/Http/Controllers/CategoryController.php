<?php

namespace App\Http\Controllers;

use App\Category;
use App\SubCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $kbn = request()->header("kbn");
        $user = getUser();
        if (is_null($user) || is_null($kbn)) {
            abort(401);
        }
        $categories = Category::where('user_id', $user->id)
        ->where('category_kbn',$kbn)
        ->orderBy('category_order')->get();
        $response = [];

        foreach ($categories as $category) {
            $subcategories = SubCategory::where('category_id', $category->id)
                ->orderBy('subcategory_order')->get();
            $category['subcategory'] = $subcategories;
            array_push($response, $category);
        }

        return response()->json(
            $response,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function update()
    {
        $kbn = request()->header("kbn");
        $categories = request()->all();
        $user = getUser();
        if (is_null($user) || is_null($kbn)) {
            abort(401);
        }

        foreach ($categories as $category) {
            if ($user->id !== $category['user_id']) {
                abort(400);
            }
        }

        Category::where('user_id', $user->id)
        ->where('category_kbn',$kbn)
        ->delete();

        foreach ($categories as $category) {
            $subcategoies = $category['subcategory'];
            unset($category['subcategory']);
            $category['created_at'] = now();
            $category['updated_at'] = now();
            Category::insert(
                $category
            );

            SubCategory::where('category_id', $category['id'])
            ->delete();

            foreach ($subcategoies as $subcategory) {
                if(is_null($subcategory['category_id'])){
                    $newcategory = Category::where('user_id', $user->id)->orderBy('id', 'desc')->first();
                    $subcategory['category_id'] = $newcategory->id;
                }
                $subcategory['created_at'] = now();
                $subcategory['updated_at'] = now();
                SubCategory::insert(
                    $subcategory
                );
            }
        }

        $message = array('message' => '更新しました。');

        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }
}
