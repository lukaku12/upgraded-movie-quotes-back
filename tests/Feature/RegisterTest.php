<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_cant_register_if_provided_credentials_are_not_valid()
	{
		$this->withExceptionHandling();

		$response = $this->postJson('/api/register/create', [
			'username'         => '',
			'email'            => '',
			'password'         => '',
			'confirm_password' => '',
		]);

		$response
			->assertStatus(422)
			->assertJson(['errors' => true]);
	}

	/* @test */
	public function test_user_can_register_if_provided_credentials_are_valid()
	{
		$this->withExceptionHandling();

		$response = $this->postJson('/api/register/create', [
			'username'         => 'luka',
			'email'            => 'lukakurdadze2@gmail.com',
			'password'         => 'password',
			'confirm_password' => 'password',
		]);

		$response->assertStatus(200);
	}
}
