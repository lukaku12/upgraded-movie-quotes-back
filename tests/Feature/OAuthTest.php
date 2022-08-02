<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OAuthTest extends TestCase
{
	use RefreshDatabase;

	/* @test */
	public function test_user_can_be_redirected_to_oauth_authentication_page()
	{
		$response = $this->get('/api/auth/redirect', );

		$response->assertStatus(302);
	}
}
