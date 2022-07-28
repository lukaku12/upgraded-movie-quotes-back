<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['title'];

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

	public function movie(): BelongsTo
	{
		return $this->belongsTo(Movie::class, 'movie_id');
	}

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function likes(): HasMany
	{
		return $this->hasMany(Like::class);
	}

	public function notifications(): HasMany
	{
		return $this->hasMany(Notification::class);
	}
}
