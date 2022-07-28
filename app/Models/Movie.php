<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Movie extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['title', 'director', 'description'];

	public function scopeFilter($query, array $filters)
	{
		$query->when(
			$filters['value'] ?? false,
			fn ($query, $search) => $query
				->where(
					fn ($query) => $query
						->where('title', 'like', '%' . $search . '%')
				)
		);
	}

	public function quotes(): HasMany
	{
		return $this->hasMany(Quote::class);
	}

	public function genres(): BelongsToMany
	{
		return $this->belongsToMany(Genre::class, 'movie_genres');
	}
}
