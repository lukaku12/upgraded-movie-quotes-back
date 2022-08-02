<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
	use RefreshDatabase;

	public function test_show_error_massage_if_user_does_not_enter_valid_email()
	{
		$response = $this->postJson('/api/forget-password', ['email' => 'test@test.com']);

		$response->assertStatus(422);
	}

	public function test_send_email_to_user_if_email_is_valid()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['email' => 'lukakurdadze2@gmail.com']);

		$response = $this->postJson('/api/forget-password', ['email' => $user->email]);

		$response->assertStatus(200);
	}

	public function test_show_error_massage_if_users_entered_passwords_do_not_match()
	{
		$this->withExceptionHandling();

		$response = $this->postJson('/api/reset-password', ['password' => 'test12345', 'password_confirmation' => 'test']);

		$response->assertStatus(422)->assertJson(['errors' => true]);
	}

	public function test_throw_validation_error_if_token_is_invalid()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$token = Str::random(64);

		DB::table('password_resets')->insert([
			'email'      => $user->email,
			'token'      => $token,
			'created_at' => Carbon::now(),
		]);

		$response = $this->postJson('/api/reset-password', [
			'email'                 => $user->email,
			'password'              => 'password',
			'password_confirmation' => 'password',
			'token'                 => 'THIS IS INVALID TOKEN',
		]);
		$response->assertStatus(400);
	}

	public function test_update_password_if_all_credentials_are_valid()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$token = Str::random(64);

		DB::table('password_resets')->insert([
			'email'      => $user->email,
			'token'      => $token,
			'created_at' => Carbon::now(),
		]);

		$response = $this->postJson('/api/reset-password', [
			'email'                 => $user->email,
			'password'              => 'password',
			'password_confirmation' => 'password',
			'token'                 => $token,
		]);
		$response->assertStatus(200);
	}
}
