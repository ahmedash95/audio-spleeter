<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
	public function redirectToProvider()
	{
		return Socialite::with('Facebook')->redirect();
	}

	public function handleProviderCallback()
	{
		$facebookUser = Socialite::driver('Facebook')->user();

		$user = User::where(['email' => $facebookUser->getEmail()])->first();
		if (!$user) {
			$user = User::create(
				[
					'name' => $facebookUser->getName(),
					'email' => $facebookUser->getEmail(),
					'email_verified_at' => now(),
					'password' => Str::random(16),
					'remember_token' => Str::random(10),
				]
			);
		}
		auth()->login($user);

		return redirect('/');
	}
}
