<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition()
	{
		$fakerKa = \Faker\Factory::create('ka_GE');
		$title = $this->faker->sentence();
		$slug = strtolower(str_replace('.', '', str_replace(' ', '-', $title)));
		return [
			'title'     => ['en' => $this->faker->sentence(), 'ka' => $fakerKa->realText(30)],
			'slug'      => $slug,
			'user_id'   => User::factory(),
		];
	}
}
