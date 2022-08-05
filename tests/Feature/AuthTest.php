<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_cant_login_if_provided_credentials_are_empty()
	{
		User::factory()->create(['password' => bcrypt('password')]);

		$response = $this->postJson(route('login'), [
			'email'      => '',
			'password'   => '',
		]);

		$response
			->assertStatus(422)
			->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_cant_login_if_provided_credentials_are_not_valid()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$response = $this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'not-valid-password',
		]);

		$response->assertStatus(401);
	}

	public function test_user_cant_login_if_user_doesnt_have_verified_email()
	{
		$user = User::factory()->create(['email_verified_at' => null, 'password' => bcrypt('password')]);

		$response = $this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$response->assertStatus(401);
	}

	public function test_user_can_login_if_credentials_are_right_and_has_verified_email()
	{
		$user = User::factory()->create(['email_verified_at' => now(), 'password' => bcrypt('password')]);

		$response = $this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$response
			->assertStatus(200)
			->assertJson([
				'access_token' => true,
				'token_type'   => 'bearer',
				'expires_in'   => 3600,
			]);
	}

	public function test_user_can_logout()
	{
		$this->withExceptionHandling();

		$user = User::factory()->create(['email_verified_at' => now(), 'password' => bcrypt('password')]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		]);

		$response = $this->postJson('/api/logout');

		$response->assertStatus(200);
	}
}
