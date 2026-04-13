<?php
use App\Http\Controllers\Api\ConsignmentController;
use App\Http\Controllers\Api\LotController;
use App\Http\Controllers\Api\UserController;
use App\Http\Middleware\ApiKeyMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware(ApiKeyMiddleware::class)->prefix('v1')->group(function () {
    Route::get('/consignments', [ConsignmentController::class, 'index']);
    Route::get('/consignments/{consignment}', [ConsignmentController::class, 'show']);
    Route::post('/consignments', [ConsignmentController::class, 'store']);
    Route::put('/consignments/{consignment}', [ConsignmentController::class, 'update']);
    Route::get('/consignments/{consignment}/lots', [LotController::class, 'index']);
    Route::get('/lots', [LotController::class, 'byConsignorNumber']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{user}', [UserController::class, 'update']);
});
