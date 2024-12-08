<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix' => 'auth'], function () {
    Route::post('loginSocial', [AuthController::class, 'socialLogin']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('payment', [PaymentController::class, 'Payment']);

});

Route::middleware('auth:api')->group(function () {
    Route::post('/sendMessage', [MessageController::class, 'sendMessage']);
    Route::get('/getMessages', [MessageController::class, 'getMessages']);

    // Group Messaging Routes
    Route::post('/createGroup', [MessageController::class, 'createGroup']);
    Route::post('/joinGroup/{groupId}', [MessageController::class, 'joinGroup']);
    Route::post('/leaveGroup/{groupId}', [MessageController::class, 'leaveGroup']);
    Route::post('/sendGroupMessage/{groupId}', [MessageController::class, 'sendGroupMessage']);
    Route::post('/getGroupMessages/{groupId}', [MessageController::class, 'getGroupMessages']);

    Route::get('/plans', [PlanController::class, 'getPlans']);
    Route::post('/plan', [PlanController::class, 'createPlan']);
    Route::post('/checkout/{plan_id}', [PaymentController::class, 'checkout']);
    Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [PaymentController::class, 'cancel']);
});

Route::get('/checkout/success', [PaymentController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [PaymentController::class, 'cancel'])->name('checkout.cancel');
