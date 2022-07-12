<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

Route::post('/register/create', [RegisterController::class, 'store'])->name('register.api');
Route::post('/login', [AuthController::class, 'login'])->name('login.api');

Route::group(['middleware' => ['web']], function () {
	Route::get('/auth/redirect', function () {
		return Socialite::driver('google')->redirect();
	});
	Route::get('/google-callback', function () {
		$googleUser = Socialite::driver('google')->user();

		$user = User::updateOrCreate([
			'google_id' => $googleUser->id,
		], [
			'username'                 => $googleUser->name,
			'email'                    => $googleUser->email,
			'google_token'             => $googleUser->token,
			'google_refresh_token'     => $googleUser->refreshToken,
		]);

		Auth::login($user);

		return redirect('/dashboard');
	});
});

//Route::middleware(['auth:api'])->group(function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout.api');

	Route::get('/user', [UserController::class, 'index'])->name('user.api');

	Route::get('/movies', [MovieController::class, 'index'])->name('movies.api');
	Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.api');

	Route::get('/quotes', [QuoteController::class, 'index'])->name('quotes.api');
	Route::post('/comment/add', [CommentController::class, 'index'])->name('add-comment.api');
	Route::post('/movies/{slug}/quote/add', [QuoteController::class, 'addQuote'])->name('add-quote.api');
	Route::get('/movies/{slug}/quote/{id}', [QuoteController::class, 'show'])->name('quote.api');
	Route::post('/movies/{slug}/quote/{id}', [QuoteController::class, 'update'])->name('quote-update.api');
	Route::post('/quotes/create', [QuoteController::class, 'store'])->name('quote-create.api');
	Route::delete('/movies/{slug}/quote/{id}', [QuoteController::class, 'destroy'])->name('quote-remove.api');
//});
