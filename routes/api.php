<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\userController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users', [userController::class, 'register']);
Route::post("/users/login", [userController::class, 'login']);
Route::middleware("apiAuth")->group(function () {
    Route::get('/users/current', [userController::class, 'getUserCurrent']);
    Route::patch('/users/current', [userController::class, 'updateUserCurrent']);
    Route::delete('/users/logout', [userController::class, 'logout']);
    Route::post("/contacts", [ContactController::class, "create"]);
    Route::get("/contacts/{id}", [ContactController::class, "getContact"])->where('id', '[0-9]+');
    Route::put("/contacts/{id}", [ContactController::class, "update"])->where('id', '[0-9]+');
    Route::delete("/contacts/{id}", [ContactController::class, "delete"])->where('id', '[0-9]+');
});
