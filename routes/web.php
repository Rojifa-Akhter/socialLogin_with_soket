<?php

use App\Http\Controllers\PushNotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pushNotification', function () {
    return view('notificationPush.index');

});

// Start Push Notification==========================================================
// Route::view('pushNotification', 'notificationPush.index');
Route::post('save-push-notification-sub', [PushNotificationController::class, 'saveSubscription']);
Route::post('send-push-notification', [PushNotificationController::class, 'sendNotification']);
