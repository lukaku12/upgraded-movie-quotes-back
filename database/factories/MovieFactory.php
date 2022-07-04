<?php

namespace Database\Factories;

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
		$title = $this->faker->sentence();
		$slug = strtolower(str_replace('.', ' ', str_replace(' ', '-', $title)));
		return [
			'title' => ['en' => $title, 'ka' => 'ფილმის სახელი'],
			'slug'  => $slug,
		];
	}
}
