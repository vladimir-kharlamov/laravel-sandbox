<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix(‘sanctum’)->namespace(‘API’)->group(function() {
    Route::post(‘register’, ‘AuthController@register’);
    Route::post(‘token’, ‘AuthController@token’);
});*/

/* Test command with job database queued */
Route::post('/tracking/', [\App\Http\Controllers\TrackingController::class, 'track']);

/**
 * Test jwt-auth library: authorize via access token on api
 * See documentation https://jwt-auth.readthedocs.io/en/develop/laravel-installation/
 */

Route::middleware('auth')->get('/user',
    function (Request $request) {
        return $request->user();
    });

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});
