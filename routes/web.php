<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Todo\TodoController;
use App\Http\Controllers\Todo\TimerController;
use App\Http\Controllers\Pdca\PdcaController;
use App\Http\Controllers\Pdca\CheckActionController;
use App\Http\Controllers\Pdca\GoalController;
use App\Http\Controllers\Auth\OAuthController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('list');
// })->name('home');

// Route::get('/', [TodoController::class, 'index'])->name('home');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('home');

    //最新のcsrfトークンを取得
    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //google認証確認
    Route::get('/check-auth', [OAuthController::class, 'getGoogleUser']);

    //TODO作成
    Route::post('/add_todo', [TodoController::class, 'store']);
    //TODO表示
    Route::get('/show_todo/{id}', [TodoController::class, 'show']);
    //TODO更新
    Route::patch('/update_todo/{id}', [TodoController::class, 'update']);
    //TODO削除
    Route::delete('/delete_todo/{id}', [TodoController::class, 'destroy']);
    //TODOのステータス更新
    Route::patch('/changeTodoStatus/{id}', [TodoController::class, 'changeTodoStatus']);
    //PDCA
    Route::get('/pdca-cycle', [PdcaController::class, 'index'])->name('pdca');
    //PDCA目標作成(月間週間どちらも作成する場合)
    Route::post('/create-pdca', [PdcaController::class, 'storeFirstGoal'])->name('pdca.create-first-goal');
    //PDCA目標作成(月間か週間かどちらか)
    Route::post('/create-goal', [GoalController::class, 'store'])->name('pdca.create-goal');
    //PDCA check & action作成
    Route::post('/create-check-action', [CheckActionController::class, 'store'])->name('pdca.create-check-action');
    //PDCA check & action更新
    Route::patch('/create-check-action/{check_id}/{action_id}', [CheckActionController::class, 'update'])->name('pdca.update-check-action');
    //OAuth認証するためのURLにリダイレクトする
    Route::get('/auth/google/redirect', [OAuthController::class, 'redirectToGoogle'])->name('google.redirect');
    // oauthで飛んできたコードを使ってユーザを認証している
    Route::get('/auth/google/callback', [OAuthController::class, 'authenticateWithGoogle']);
    //タイマーの記録
    Route::post('/store_timer_data', [TimerController::class, 'storeTimerData']);
    Route::patch('/fetch_timer_data', [TimerController::class, 'fetchTimerData']);
});

require __DIR__.'/auth.php';
