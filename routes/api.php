<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
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

Route::post('/register/create', [RegisterController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['web']], function () {
	Route::get('/auth/redirect', [OAuthController::class, 'redirect']);
	Route::get('/google-callback', [OAuthController::class, 'callback']);
});

Route::middleware(['auth:api'])->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);

	Route::get('/user', [UserController::class, 'index']);

	Route::get('/movies', [MovieController::class, 'index']);
	Route::get('/movies/{slug}', [MovieController::class, 'show']);

	Route::get('/quotes', [QuoteController::class, 'index']);
	Route::post('/comment/add', [CommentController::class, 'index']);
	Route::post('/like/add', [LikeController::class, 'index']);
	Route::post('/like/remove', [LikeController::class, 'destroy']);
	Route::post('/movies/{slug}/quote/add', [QuoteController::class, 'addQuote']);
	Route::get('/movies/{slug}/quote/{id}', [QuoteController::class, 'show']);
	Route::post('/movies/{slug}/quote/{id}', [QuoteController::class, 'update']);
	Route::post('/quotes/create', [QuoteController::class, 'store']);
	Route::delete('/movies/{slug}/quote/{id}', [QuoteController::class, 'destroy']);

	Route::post('/notify-user', [NotificationController::class, 'index']);
	Route::get('/notifications', [NotificationController::class, 'getUserNotifications']);
	Route::post('/notifications/read-all', [NotificationController::class, 'updateNotifications']);
});
