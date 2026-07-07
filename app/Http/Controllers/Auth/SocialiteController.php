<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            
            $user = User::updateOrCreate([
                'provider_id' => $socialUser->id,
                'provider' => $provider,
            ], [
                'name' => $socialUser->name ?? $socialUser->nickname,
                'email' => $socialUser->email,
            ]);

            Auth::login($user);

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed using ' . $provider);
        }
    }
}
