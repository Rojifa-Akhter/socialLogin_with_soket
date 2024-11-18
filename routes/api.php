<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['prefix' => 'auth'], function () {  
    Route::post('login', [AuthController::class, 'socialLogin']);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('/sendMessage', [MessageController::class, 'sendMessage']);
    Route::get('/getMessages', [MessageController::class, 'getMessages']);
    Route::post('/markAsRead/{messageId}', [MessageController::class, 'markAsRead']);
    
    // Group Messaging Routes
    Route::post('/createGroup', [MessageController::class, 'createGroup']);
    Route::post('/joinGroup/{groupId}', [MessageController::class, 'joinGroup']);
    Route::post('/leaveGroup/{groupId}', [MessageController::class, 'leaveGroup']);
    Route::post('/sendGroupMessage/{groupId}', [MessageController::class, 'sendGroupMessage']);
    Route::post('/markGroupMessageAsRead/{messageId}', [MessageController::class, 'markGroupMessageAsRead']);
});
