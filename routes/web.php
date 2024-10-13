<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Todo\TodoController;
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //TODO作成
    Route::post('/add_todo', [TodoController::class, 'store']);
    //TODO表示
    Route::get('/show_todo/{id}', [TodoController::class, 'show']);
    //TODO更新
    Route::patch('/update_todo/{id}', [TodoController::class, 'update']);
});

require __DIR__.'/auth.php';
