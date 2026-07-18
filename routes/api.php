<?php
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\EventController;

Route::post('/events', [EventController::class, 'store']);

Route::get('/accounts/{account_id}/balance', [AccountController::class, 'balance']);

Route::get('/accounts/{account_id}/events', [AccountController::class, 'events']);
?>