<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	protected $guarded = [];

	public function audio()
	{
		return $this->hasOne(Audio::class);
	}
}
