<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GListController;
use App\Http\Controllers\GameController;
use Inertia\Inertia;


// 添加处理 OPTIONS 请求的路由
Route::options('/{any}', function (Request $request) {
    $origin = $request->header('ORIGIN', '*');
    return response('', 200)
        ->header('Access-Control-Allow-Origin', $origin)
        ->header('Access-Control-Allow-Credentials', 'true')
        ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
        ->header('Access-Control-Allow-Headers', 'Origin, Access-Control-Request-Headers, SERVER_NAME, Access-Control-Allow-Headers, cache-control, token, X-Requested-With, Content-Type, Accept, Connection, User-Agent, Cookie, X-XSRF-TOKEN');
})->where('any', '.*');

// 首页
Route::get('/', function () {
    return Inertia::render('Index');
})->name('index');

// 游戏详情页
Route::get('/game/{title}', [GameController::class, 'show'])
    ->name('game.detail');

// 测试页面
Route::get('/bac', function () {
    return Inertia::render('Welcome');
})->name('home');

// 仪表盘
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// API 路由
Route::prefix('api')
    ->group(function () {
        Route::apiResource('lists', GListController::class);
    });

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
