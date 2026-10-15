<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KeepNotesController;
use Laravel\Fortify\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

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

// Public: sign up and sign in (Laravel Fortify, passwords are hashed)
Route::post('register', [RegisteredUserController::class, 'store']);
Route::post('login', [AuthenticatedSessionController::class, 'store']);

// Signed-in users only
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('get_notes', [KeepNotesController::class, 'getAllNotes']);
    Route::post('add_note', [KeepNotesController::class, 'addNote']);
    Route::post('edit_note', [KeepNotesController::class, 'editNote']);
    Route::post('edit_action', [KeepNotesController::class, 'editAction']);
    Route::get('getDepartment', [KeepNotesController::class, 'getDepartment']);

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
});
