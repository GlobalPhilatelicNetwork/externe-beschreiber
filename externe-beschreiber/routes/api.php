<?php
use App\Http\Controllers\Api\ConsignmentController;
use App\Http\Controllers\Api\LookupController;
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

    // Lookup CRUD
    Route::get('/categories', [LookupController::class, 'indexCategories']);
    Route::post('/categories', [LookupController::class, 'storeCategory']);
    Route::put('/categories/{category}', [LookupController::class, 'updateCategory']);
    Route::delete('/categories/{category}', [LookupController::class, 'destroyCategory']);

    Route::get('/catalog-types', [LookupController::class, 'indexCatalogTypes']);
    Route::post('/catalog-types', [LookupController::class, 'storeCatalogType']);
    Route::put('/catalog-types/{catalogType}', [LookupController::class, 'updateCatalogType']);
    Route::delete('/catalog-types/{catalogType}', [LookupController::class, 'destroyCatalogType']);

    Route::get('/grouping-categories', [LookupController::class, 'indexGroupingCategories']);
    Route::post('/grouping-categories', [LookupController::class, 'storeGroupingCategory']);
    Route::put('/grouping-categories/{groupingCategory}', [LookupController::class, 'updateGroupingCategory']);
    Route::delete('/grouping-categories/{groupingCategory}', [LookupController::class, 'destroyGroupingCategory']);

    Route::get('/destinations', [LookupController::class, 'indexDestinations']);
    Route::post('/destinations', [LookupController::class, 'storeDestination']);
    Route::put('/destinations/{destination}', [LookupController::class, 'updateDestination']);
    Route::delete('/destinations/{destination}', [LookupController::class, 'destroyDestination']);

    Route::get('/conditions', [LookupController::class, 'indexConditions']);
    Route::get('/pack-types', [LookupController::class, 'indexPackTypes']);
    Route::get('/lot-types', [LookupController::class, 'indexLotTypes']);
});
