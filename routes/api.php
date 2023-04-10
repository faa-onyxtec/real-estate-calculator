<?php

use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\LoginRegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Registeration and verify through email routes
Route::post('register-post', [LoginRegisterController::class, 'register']);
Route::get('/user/verify/{token}', [LoginRegisterController::class, 'verifyUser'])->name('user/verify');
// Login route
Route::post('post-login', [LoginRegisterController::class, 'login'])->name('peerLogin.post');

// Forget and Reset password route
Route::post('forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');
