<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_users_can_get_their_account_info_if_authenticated()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson('/api/user');

		$response
			->assertStatus(200)
			->assertJson([
				'id'       => $user->id,
				'username' => $user->username,
				'email'    => $user->email,
				'picture'  => $user->picture,
			]);
	}

	/* @test */
	public function test_users_can_update_their_account()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->postJson('/api/login', [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson('/api/user', [
			'username'         => 'new-username',
			'picture'          => UploadedFile::fake()->image('picture.jpg'),
			'password'         => 'new-password',
			'confirm_password' => 'new-password',
		]);

		$response
			->assertStatus(200);
	}
}
