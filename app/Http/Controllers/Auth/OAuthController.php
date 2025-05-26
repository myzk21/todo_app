<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\GoogleUser;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        if(session()->has('invalidRefreshToken')) {
            session()->forget('invalidRefreshToken');//リフレッシュトークン関係のセッションを削除
        }
        return Socialite::driver('google')
        ->scopes(['https://www.googleapis.com/auth/calendar.events'])//Googleカレンダーのイベントにアクセスできる権限をリクエスト
        ->with(['access_type' => 'offline',  'prompt' => 'consent'])
        ->redirect();
    }
    public function authenticateWithGoogle()//redirectToGoogleの次に実行される
    {
        try {
            $social_user = Socialite::driver('google')->user();//Googleから返されたユーザー情報を取得
            $google_user = GoogleUser::where('google_id', $social_user->id)->first(); //GoogleユーザーIDに基づいて既存のユーザーを取得

            if (!$google_user) {//Googleユーザーがまだない場合にその情報を保存
                $google_user = new GoogleUser;
                $google_user->google_id = $social_user->id;
                $google_user->user_id = Auth::id();
            }

            //アクセストークンとリフレッシュトークンを保存
            // $google_user->access_token = encrypt($social_user->token);
            $google_user->access_token = $social_user->token;
            if (!empty($social_user->refreshToken)) {
                $google_user->refresh_token = $social_user->refreshToken;
            } else {
                $google_user->refresh_token = $google_user->refresh_token ?? null;
            }
            $google_user->expires = Carbon::now()->addSeconds($social_user->expiresIn);
            $google_user->save();
            return redirect('/');
        } catch(\Exception $error) {
            return redirect()->back()->withErrors('googleAuthError', 'googleアカウントの接続に失敗しました');
        }
    }
}
