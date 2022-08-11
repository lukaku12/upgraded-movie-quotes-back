<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UserTest extends TestCase
{
	use RefreshDatabase;

	protected function setUp(): void
	{
		parent::setUp();

		$user = User::factory()->create(['password' => bcrypt('password')]);

		$this->actingAs($user);
	}

	/* @test */
	public function test_users_can_get_their_account_info_if_authenticated()
	{
		$response = $this->getJson(route('user.get'));

		$response
			->assertStatus(200)
			->assertJson([
				'id'       => auth()->id(),
				'username' => auth()->user()->username,
				'email'    => auth()->user()->email,
				'picture'  => auth()->user()->picture,
			]);
	}

	/* @test */
	public function test_users_can_update_their_account()
	{
		$response = $this->postJson(route('user.update'), [
			'username'         => 'new-username',
			'picture'          => UploadedFile::fake()->image('picture.jpg'),
			'password'         => 'new-password',
			'confirm_password' => 'new-password',
		]);

		$response->assertStatus(200);
	}

	/** @test */
	public function test_a_post_has_many_movies()
	{
		$user = User::factory()->create();
		$movie = Movie::factory()->create(['user_id' => $user->id]);

		$this->assertTrue($user->movies->contains($movie));
	}

	/** @test */
	public function test_a_post_has_many_quotes()
	{
		$user = User::factory()->create();
		$quote = Quote::factory()->create(['user_id' => $user->id]);

		$this->assertTrue($user->quotes->contains($quote));
	}

	/** @test */
	public function test_a_post_has_many_notifications()
	{
		$user = User::factory()->create();
		$quote = Quote::factory()->create();
		$notifications = Notification::create([
			'user_id'  => $user->id,
			'quote_id' => $quote->id,
			'type'     => 'test',
			'message'  => 'test',
		]);

		$this->assertTrue($user->notifications->contains($notifications));
	}
}
