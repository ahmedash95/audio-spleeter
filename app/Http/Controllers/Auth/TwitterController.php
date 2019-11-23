<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class TwitterController extends Controller
{
	public function redirectToProvider()
	{
		return Socialite::with('Twitter')->redirect();
	}

	public function handleProviderCallback()
	{
		$twitterUser = Socialite::driver('Twitter')->user();

		$user = User::where(['email' => $twitterUser->getEmail()])->first();
		if (!$user) {
			$user = User::create(
				[
					'name' => $twitterUser->getName(),
					'email' => $twitterUser->getEmail(),
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
