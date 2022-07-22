<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MovieGenre extends Model
{
	use HasFactory;

	protected $guarded = ['id'];

	public function movies(): BelongsToMany
	{
		return $this->belongsToMany(Movie::class);
	}

	public function genres(): BelongsToMany
	{
		return $this->belongsToMany(Genre::class);
	}
}
