<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		Genre::factory()->create(['name' => 'Drama']);
		Genre::factory()->create(['name' => 'Comedy']);
		Genre::factory()->create(['name' => 'Action']);
		Genre::factory()->create(['name' => 'Horror']);
		Genre::factory()->create(['name' => 'Thriller']);
		Genre::factory()->create(['name' => 'Sci-Fi']);
		Genre::factory()->create(['name' => 'Mystery']);
		Genre::factory()->create(['name' => 'Fantasy']);
		Genre::factory()->create(['name' => 'Romance']);
		Genre::factory()->create(['name' => 'History']);
		Genre::factory()->create(['name' => 'War']);
		Genre::factory()->create(['name' => 'Western']);
		Genre::factory()->create(['name' => 'Animation']);
		Genre::factory()->create(['name' => 'Family']);
		Genre::factory()->create(['name' => 'Musical']);
		Genre::factory()->create(['name' => 'Documentary']);
	}
}
