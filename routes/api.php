<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
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

Route::post('/register/create', [RegisterController::class, 'store'])->name('register.api');
Route::post('/login', [AuthController::class, 'login'])->name('login.api');

Route::middleware(['auth:api'])->group(function () {
	Route::post('/logout', [AuthController::class, 'logout'])->name('logout.api');
	Route::get('/user', [UserController::class, 'index'])->name('user.api');
	Route::get('/movies', [MovieController::class, 'index'])->name('movies.api');
	Route::get('/movies/{slug}', [MovieController::class, 'show'])->name('movies.api');
	Route::get('/movies/{slug}/quote/{id}', [QuoteController::class, 'show'])->name('quote.api');
	Route::patch('/movies/{slug}/quote/{id}', [QuoteController::class, 'update'])->name('quote-update.api');
	Route::post('/quotes/create', [QuoteController::class, 'store'])->name('quotes-create.api');
});
