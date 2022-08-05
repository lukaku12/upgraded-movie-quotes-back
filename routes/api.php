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
	Route::post('/register/create', [AuthController::class, 'register'])->name('register');
	Route::post('/login', [AuthController::class, 'login'])->name('login');

	Route::post('/forget-password', [PasswordResetController::class, 'submitForgetPasswordForm'])->name('password.forget');
	Route::post('/reset-password', [PasswordResetController::class, 'submitResetPasswordForm'])->name('password.reset');

	Route::group(['middleware' => ['web']], function () {
		Route::get('/auth/redirect', [OAuthController::class, 'redirect'])->name('google.auth-redirect');
		Route::get('/google-callback', [OAuthController::class, 'callback'])->name('google.callback');
	});
});
	Route::get('email/verify/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');

Route::middleware(['auth:api'])->group(function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

	Route::get('/user', [UserController::class, 'index'])->name('user.get');
	Route::post('/user', [UserController::class, 'update'])->name('user.update');

	Route::controller(MovieController::class)->group(function () {
		Route::get('/movies', 'index')->name('movies.get');
		Route::get('/movies/{movie:slug}', 'show')->name('movies.show');
		Route::get('/movies/{movie:slug}/edit', 'edit')->name('movies.edit');
		Route::post('/movies/{movie:slug}/edit', 'update')->name('movies.update');
		Route::post('/movies/{movie:slug}/remove', 'destroy')->name('movies.destroy');
		Route::post('/movies/add', 'store')->name('movies.store');
	});

	Route::controller(QuoteController::class)->group(function () {
		Route::get('/quotes', 'index')->name('quotes.get');
		Route::get('/movies/{movie:slug}/quote/{quote:id}', 'show')->name('quotes.show');
		Route::post('/movies/{movie:slug}/quote/{quote:id}', 'update')->name('quotes.update');
		Route::post('/quotes/create', 'store')->name('quotes.store');
		Route::post('/movies/{movie:slug}/quote/{quote:id}/remove', 'destroy')->name('quotes.destroy');
	});

	Route::controller(NotificationController::class)->group(function () {
		Route::post('/notify-user', 'index')->name('notifications.get');
		Route::get('/notifications', 'getUserNotifications')->name('notifications.get-user-notifications');
		Route::post('/notifications/read-all', 'updateNotifications')->name('notifications.update-notifications');
	});

	Route::get('/genres', [GenreController::class, 'index'])->name('genres.get');

	Route::post('/search', [SearchController::class, 'search'])->name('search');

	Route::post('/comment/add', [CommentController::class, 'index'])->name('comment.store');
	Route::post('/like/add', [LikeController::class, 'index'])->name('like.store');
	Route::post('/like/remove', [LikeController::class, 'destroy'])->name('like.destroy');
});
