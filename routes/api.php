<?php

use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\DocumentController;

Route::post('/session', [SessionController::class, 'createSession']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/session/{sessionId}/validate-otp', [SessionController::class, 'validateOTP']);
    Route::post('/session/{sessionId}/resend-otp', [SessionController::class, 'resendOTP']);
    Route::get('/session', [SessionController::class, 'index']);
    Route::get('/session/{sessionId}', [SessionController::class, 'show']);
    Route::put('/session/{sessionId}', [SessionController::class, 'update']);
    Route::delete('/session/{sessionId}', [SessionController::class, 'destroy']);

    Route::post('/request', [RequestController::class, 'createRequest']);
    Route::put('/request/{id}', [RequestController::class, 'updateRequest']);
    Route::get('/request/{id}', [RequestController::class, 'showRequest']);
    Route::get('/requests', [RequestController::class, 'listRequests']);
    Route::delete('/request/{id}', [RequestController::class, 'deleteRequest']);

    Route::get('/request/session/{sessionId}', [RequestController::class, 'getRequestBySessionId']);
    Route::get('/request/number/{requestNumber}', [RequestController::class, 'getRequestByRequestNumber']);

    Route::post('/document', [DocumentController::class, 'uploadOrUpdateDocument']);
    Route::get('/document/{documentId}/verify-checksum', [DocumentController::class, 'verifyChecksum']);
    Route::delete('/document/{documentId}', [DocumentController::class, 'deleteDocument']);
});
