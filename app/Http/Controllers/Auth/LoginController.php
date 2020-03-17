<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Hash;
use App\User;
use Socialite;
use App\Koza;
use App\Category;
use App\SubCategory;
use App\KokaiConfig;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $gUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $gUser->email)->first();
        $token = $this->createToken($user);
        return redirect()->away(env('CLIENT_URL') . '/sociallogin/' . $token, 301, );
    }

    public function redirectToYahoo()
    {
        return Socialite::driver('yahoo')->redirect();
    }

    public function handleYahooCallback()
    {
        $yUser = Socialite::driver('yahoo')->stateless()->user();
        $user = User::where('email', $yUser->email)->first();
        $token = $this->createToken($user);
        return redirect()->away(env('CLIENT_URL') . '/sociallogin/' . $token, 301, );
    }

    public function login()
    {
        $email = request()->get('email');
        $password = request()->get('password');
        $user = \App\User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = str_random(64);
            $user->token = $token;
            $user->save();
            $isSocial = ($user->password === '' ? true : false);
            $user['isSocial'] = $isSocial;
            return [
                'user' => $user,
            ];
        } else {
            abort(401);
        }
    }

    public function getUser()
    {
        $user = getUser();
        if ($user) {
            $isSocial = ($user->password === '' ? true : false);
            $user['isSocial'] = $isSocial;
            return $user;
        } else {
            abort(401);
        }
    }

    public function password()
    {
        $user = getUser();
        if (is_null($user)) {
            abort(401);
        }
        $password = request()->get('after');
        $before_password = request()->get('before');
        if (is_null($password) || is_null($before_password) || !Hash::check($before_password, $user->password)) {
            abort(400);
        }
        $user->password =  Hash::make($password);
        $user->save();
        $message = array('message' => '更新しました。');
    
        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function mail()
    {
        $user = getUser();
        if (is_null($user)) {
            abort(401);
        }
        $mail = request()->get('after');
        $before_mail = request()->get('before');
        if (is_null($mail) || $before_mail != $user->email) {
            abort(400);
        }
        $user->email =  $mail;
        $user->email_verified_at = now();
        $user->save();
        $message = array('message' => '更新しました。');
    
        return response()->json(
            $message,
            200, [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function logout()
    {
        $token = request()->bearerToken();
        $user = \App\User::where('token', $token)->first();
        if ($token && $user) {
            $user->token = null;
            $user->save();
            return [];
        } else {
            abort(401);
        }
    }

    public function store()
    {
        $email = request()->get('email');
        $password = request()->get('password');
        $user = \App\User::where('email', $email)->first();
        if (isset($user)) {
            abort(400);
        } else {
            $user = new User;
            $user->email = $email;
            $user->email_verified_at = now();
            $user->password =  Hash::make($password);
            $user->name = substr($email, 0, strcspn($email,'@'));
            $token = str_random(64);
            $user->token = $token;
            $user->save();
            $this->createdefaultConfig($user->id);
            $isSocial = false;
            $user['isSocial'] = $isSocial;
            return [
                'user' => $user,
            ];
        }
    }

    private function createdefaultConfig($id)
    {
        $this->createKoza($id , '財布');
        $this->createKokaiConfig($id);

        $i = 0;
        $this->createCategory($id , '0', '給与所得', $i++, ['月給','賞与','その他']);
        $this->createCategory($id , '0', '事業所得', $i++, ['事業所得']);
        $this->createCategory($id , '0', 'その他', $i++, ['その他']);
        $this->createCategory($id , '1', '食費', $i++, ['食料品','外食','その他']);
        $this->createCategory($id , '1', '雑費', $i++, ['消耗品','雑貨','家具','家電','その他']);
        $this->createCategory($id , '1', '交通費', $i++, ['電車','タクシー','バス','飛行機','その他']);
        $this->createCategory($id , '1', '交際費', $i++, ['付き合い','プレゼント','ご祝儀・香典','その他']);
        $this->createCategory($id , '1', '娯楽費', $i++, ['旅行','お小遣い','イベント','音楽','書籍','その他']);
        $this->createCategory($id , '1', '教育費', $i++, ['習い事','新聞','学費','書籍','仕送り','その他']);
        $this->createCategory($id , '1', '美容・衣服', $i++, ['洋服','アクセサリー・小物','美容院','エステ','クリーニング','その他']);
        $this->createCategory($id , '1', '医療・保険', $i++, ['病院','薬','保険','介護','その他']);
        $this->createCategory($id , '1', '通信費', $i++, ['電話','インターネット','郵便・宅急便','その他']);
        $this->createCategory($id , '1', '水道光熱費', $i++, ['水道','電気','ガス','その他']);
        $this->createCategory($id , '1', '住宅費', $i++, ['家賃','ローン','保険','その他']);
        $this->createCategory($id , '1', '車', $i++, ['ガソリン','駐車場','保険','税金','ローン','高速代','その他']);
        $this->createCategory($id , '1', '税金', $i++, ['年金','所得税','消費税','住民税','個人事業税','その他']);
        $this->createCategory($id , '1', 'その他', $i++, ['使途不明金','立替金','その他']);
        $this->createCategory($id , '1', '未分類', $i++, ['未分類']); 
    }

    private function createKoza($id , $koza_name){
        $koza = new Koza;
        $koza->user_id = $id;
        $koza->koza_name = $koza_name;
        $koza->zandaka = 0;
        $koza->is_credit = 0;
        $koza->save();
    }

    private function createKokaiConfig($id){
        $kokaiconfig = new KokaiConfig;
        $kokaiconfig->user_id = $id;
        $kokaiconfig->is_open = '0';
        $kokaiconfig->is_shunyu = '1';
        $kokaiconfig->is_shunyu_category = '1';
        $kokaiconfig->is_shishutu = '1';
        $kokaiconfig->is_shishutu_category = '1';
        $kokaiconfig->is_togetu = '0';
        $kokaiconfig->is_zandaka = '0';     
        $kokaiconfig->save();
    }

    private function createCategory($id , $category_kbn, $category_name, $category_order, $subcategoies){
        $category = new Category;
        $category->user_id = $id;
        $category->category_kbn = $category_kbn;
        $category->category_name = $category_name;
        $category->category_order = $category_order;
        $category->save();
        $i = 0;
        foreach ($subcategoies as $subcategory) {
            $sub = new SubCategory;
            $sub->category_id = $category->id;
            $sub->subcategory_order = $i;
            $sub->subcategory_name = $subcategory;
            $sub->save();
            $i++;
        }
    }

    private function createUser($user)
    {
        $user = User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => '',
        ]);
        return $user;
    }

    private function createToken($user){
        if ($user == null) {
            $user = $this->createUser($yUser);
        }
        if ($user->name == null) {
            $user->name = substr($user->email, 0, strcspn($user->email,'@'));
        }
        $token = str_random(64);
        $user->token = $token;
        $user->save();
        return $token;
    }
}
