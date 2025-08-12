<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\FacilityRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Application submission endpoints
Route::post('v1/join', [JoinController::class, 'store'])->withoutMiddleware(['web', 'api']);
Route::post('v1/facility-request', [FacilityRequestController::class, 'store']);
