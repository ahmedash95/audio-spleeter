<?php

namespace App\Http\Controllers;

use App\Audio;
use App\Events\AudioUploaded;
use App\Jobs\AudioSplit;
use Illuminate\Http\Request;

class AudioController extends Controller
{
    public function index(){
    	return $this->files()->get();
	}
	public function upload(Request $request){
		$this->validate($request,[
			'file' => 'required|max:15000|mimes:mp3,mpga,wav',
		]);

		$path = 'storage/'.$request->file('file')->store('source');

		$audio = $this->files()->create([
			'name' => $request->file('file')->getClientOriginalName(),
			'source' => $path,
			'status' => 'in Queue',
		]);

		broadcast(new AudioUploaded($audio));
		AudioSplit::dispatch($audio);

	}

	public function delete($id){
    	$this->files()->findOrFail($id)->wipe();
	}

	private function files(){
    	return auth()->user()->files();
	}

}
