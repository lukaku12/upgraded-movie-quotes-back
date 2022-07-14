<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory, HasTranslations;

	protected $guarded = ['id'];

	public $translatable = ['title'];

	public function movie()
	{
		return $this->belongsTo(Movie::class, 'movie_id');
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function comments()
	{
		return $this->hasMany(Comment::class);
	}

	public function likes()
	{
		return $this->hasMany(Like::class);
	}
}
