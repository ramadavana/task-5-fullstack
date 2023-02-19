<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;


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

Route::middleware('auth:api')->group(function () {
    Route::post('/get-token', function () {
        $http = new GuzzleHttp\Client;

        $response = $http->post(url('/oauth/token'), [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => '4',
                'client_secret' => 'p8y1YZ79XnrJ1h4WTlA6q6QgYLCSOleNwkOxqr3G',
                'scope' => '',
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('api/v1')->group(function () {
    Route::post('/posts', [PostController::class, 'create'])->middleware('auth.api');
    Route::get('/posts', [PostController::class, 'list'])->middleware('auth.api');
    Route::get('/posts/{id}', [PostController::class, 'show'])->middleware('auth.api');
    Route::put('/posts/{id}', [PostController::class, 'update'])->middleware('auth.api');
    Route::delete('/posts/{id}', [PostController::class, 'delete'])->middleware('auth.api');
});