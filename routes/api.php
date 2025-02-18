<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\ResourceController;
use App\Http\Controllers\Api\PermissionUserController;
use App\Http\Controllers\Api\UserConfirmedController;
use App\Http\Controllers\Api\UserContactController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Auth and Register Routes
 */
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/resendCode', [RegisterController::class, 'resendCode']);
Route::post('/auth', [AuthController::class, 'auth']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
Route::put('/users/cell-confirmed/{identify}', [UserConfirmedController::class, 'updateCellConfirmed']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/resources', [ResourceController::class, 'index']);
    
    Route::get('/users/can/{permission}', [PermissionUserController::class, 'userHasPermission']);
    Route::get('/users/{identify}/permissions', [PermissionUserController::class, 'permissonsUser']);
    Route::post('/users/permissions', [PermissionUserController::class, 'addPermissonsUser']);
    Route::delete('/users/permissions', [PermissionUserController::class, 'removePermissonsUser'])->middleware('can:del_permissions_user');
    
    Route::apiResource('/users', UserController::class);
    Route::get('/users/address/{identify}', [UserContactController::class, 'show']);
    Route::put('/users/address/{identify}', [UserContactController::class, 'updateAddress']);
});

Route::get('/', function () {
    return response()->json(['message' => 'micro-auth success!']);
});
