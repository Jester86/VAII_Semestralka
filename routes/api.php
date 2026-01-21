<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ChatController;

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

// Public API for fetching new posts (polling)
Route::get('/topics/{topic}/posts', [PostController::class, 'index']);
Route::get('/topics/{topic}/posts/sync', [PostController::class, 'sync']);
Route::get('/topics/{topic}/posts/count', [PostController::class, 'count']);

// Global Chat API
Route::get('/chat/messages', [ChatController::class, 'index']);
Route::get('/chat/poll', [ChatController::class, 'poll']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chat/messages', [ChatController::class, 'store']);
    Route::delete('/chat/messages/{chatMessage}', [ChatController::class, 'destroy']);
});
