<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Audio extends Model
{
	protected $guarded = [];

	public function user(){
    	return $this->belongsTo(User::class);
	}

	public function tasks(){
    	return $this->hasMany(Task::class);
	}

	public function wipe(){
		array_map(function($file){
			@unlink($file);
		},[
			public_path($this->source),
			public_path($this->accompaniment),
			public_path($this->vocals),
		]);
		// delete output directory
		$outPutDirPath = tap(collect(explode('/',$this->vocals)))->pop()->implode('/');
		@rmdir(public_path($outPutDirPath));

		$this->delete();
	}

	public function getSourceNameWithoutExtension() : string
	{
		$fileName = Arr::last(explode(DIRECTORY_SEPARATOR,$this->source));
		return explode('.',$fileName)[0];
	}
}
