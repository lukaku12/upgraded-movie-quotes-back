<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_dont_save_notification_if_user_who_liked_post_is_same_as_user_who_wrote_post()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('notifications.store'), [
			'user_id'  => $user->id,
			'quote_id' => $quote->id,
			'message'  => 'Post Body',
			'read_at'  => null,
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_can_save_notification()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$other_user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $user->id]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('notifications.store'), [
			'user_id'  => $other_user->id,
			'quote_id' => $quote->id,
			'message'  => 'Reacted to your quote',
			'read_at'  => null,
		]);

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_can_update_read_at_for_notifications()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $user->id]);
		Notification::create([
			'user_id'  => $user->id,
			'quote_id' => $quote->id,
			'message'  => 'Reacted to your quote',
			'read_at'  => null,
		]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->postJson(route('notifications.update'));

		$response->assertStatus(200);
	}

	/* @test */
	public function test_user_can_get_all_notifications()
	{
		$user = User::factory()->create(['password' => bcrypt('password')]);
		$quote = Quote::factory()->create(['user_id' => $user->id]);
		Notification::create([
			'user_id'  => $user->id,
			'quote_id' => $quote->id,
			'message'  => 'Reacted to your quote',
			'read_at'  => null,
		]);

		$this->postJson(route('login'), [
			'email'      => $user->email,
			'password'   => 'password',
		])->assertStatus(200);

		$response = $this->getJson(route('notifications.get'));

		$response->assertStatus(200);
	}
}
