<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function test_user_cant_verify_email_if_token_is_invalid()
	{
		$user = User::factory()->create([
			'email_verified_at' => null,
		]);

		$response = $this
			->get(
				route('verification.verify', ['id' => $user->id, 'signature' => $user->email_verification_token])
			);

		$this->assertFalse($user->hasVerifiedEmail());
		$response->assertStatus(401);
	}

	/** @test */
	public function test_a_user_can_verify_his_email_address()
	{
		$user = User::factory()->create(['email_verified_at' => null]);

		$verificationUrl = URL::temporarySignedRoute(
			'verification.verify',
			now()->addMinutes(60),
			['id' => $user->id, 'hash' => sha1($user->email)]
		);

		$this->assertSame(null, $user->email_verified_at);

		$this->actingAs($user)->get($verificationUrl);

		$this->assertNotNull(!$user->email_verified_at);
	}
}
