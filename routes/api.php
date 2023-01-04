<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Example\ExampleController;
use App\Http\Controllers\Example\EsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']); //注册
Route::post('/login', [AuthController::class, 'login']); //登陆


// 示例
Route::namespace('Example')->prefix('example')->group(function (){
    Route::get('doc', [ExampleController::class, 'doc']);
    Route::get('ok', [ExampleController::class, 'ok']);
    Route::get('err', [ExampleController::class, 'err']);
    Route::get('test', [ExampleController::class, 'test']);
    Route::get('test2', [ExampleController::class, 'test2']);
    Route::get('test3', [ExampleController::class, 'test3']);
});

// es
Route::namespace('Example')->prefix('es')->group(function () {
    Route::get('info', [EsController::class, 'info']);
    Route::get('exists_index', [EsController::class, 'existsIndex']);
    Route::get('create_index', [EsController::class, 'createIndex']);
    Route::get('del_index', [EsController::class, 'delIndex']);
    Route::get('exists_doc', [EsController::class, 'existsDoc']);
    Route::get('add_doc', [EsController::class, 'addDoc']);
    Route::get('get_doc', [EsController::class, 'getDoc']);
    Route::get('update_doc', [EsController::class, 'updateDoc']);
    Route::get('del_doc', [EsController::class, 'deleteDoc']);
    Route::get('search_doc', [EsController::class, 'searchDoc']);
});
