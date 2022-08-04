<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SearchController;
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

Route::middleware(['guest'])->group(function () {
	Route::post('/register/create', [AuthController::class, 'register']);
	Route::post('/login', [AuthController::class, 'login']);

	Route::post('/forget-password', [PasswordResetController::class, 'submitForgetPasswordForm']);
	Route::post('/reset-password', [PasswordResetController::class, 'submitResetPasswordForm']);

	Route::group(['middleware' => ['web']], function () {
		Route::get('/auth/redirect', [OAuthController::class, 'redirect']);
		Route::get('/google-callback', [OAuthController::class, 'callback']);
	});
});
	Route::get('email/verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(['auth:api'])->group(function () {
	Route::post('/logout', [AuthController::class, 'logout']);

	Route::get('/user', [UserController::class, 'index']);
	Route::post('/user', [UserController::class, 'update']);

	Route::controller(MovieController::class)->group(function () {
		Route::get('/movies', 'index');
		Route::get('/movies/{slug}','show');
		Route::get('/movies/{slug}/edit','editMovie');
		Route::post('/movies/{slug}/edit', 'updateMovie');
		Route::post('/movies/{slug}/remove', 'destroy');
		Route::post('/movies/add', 'store');
	});

	Route::controller(QuoteController::class)->group(function () {
		Route::get('/quotes', 'index');
		Route::get('/movies/{slug}/quote/{id}', 'show');
		Route::post('/movies/{slug}/quote/{id}', 'update');
		Route::post('/quotes/create', 'store');
		Route::post('/movies/{slug}/quote/{id}/remove', 'destroy');
	});

	Route::controller(NotificationController::class)->group(function () {
		Route::post('/notify-user', 'index');
		Route::get('/notifications', 'getUserNotifications');
		Route::post('/notifications/read-all', 'updateNotifications');
	});

	Route::get('/genres', [GenreController::class, 'index']);

	Route::post('/search', [SearchController::class, 'search']);

	Route::post('/comment/add', [CommentController::class, 'index']);
	Route::post('/like/add', [LikeController::class, 'index']);
	Route::post('/like/remove', [LikeController::class, 'destroy']);

});
