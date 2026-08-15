<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HackClubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('hackclub')->redirect();
    }

    public function callback()
    {
        try {
            $hackclubUser = Socialite::driver('hackclub')->user();

            // Prefer stable provider ID, then fall back to email for older accounts.
            $user = null;

            if (!empty($hackclubUser->id)) {
                $user = User::where('hackclub_id', (string) $hackclubUser->id)->first();
            }

            if (!$user && !empty($hackclubUser->email)) {
                $user = User::where('email', $hackclubUser->email)->first();
            }

            if (!$user) {
                $user = new User([
                    'email' => $hackclubUser->email,
                    'password' => bcrypt(uniqid()),
                ]);
            }

            $user->name = $hackclubUser->name ?: $user->name;
            $user->slack_id = $hackclubUser->slack_id;
            $user->hackclub_id = $hackclubUser->id ? (string) $hackclubUser->id : $user->hackclub_id;
            $user->hackclub_access_token = $hackclubUser->token ?: $user->hackclub_access_token;
            $user->hackclub_refresh_token = $hackclubUser->refreshToken ?: $user->hackclub_refresh_token;
            $user->hackclub_token_expires_at = isset($hackclubUser->expiresIn)
                ? now()->addSeconds((int) $hackclubUser->expiresIn)
                : $user->hackclub_token_expires_at;
            $user->save();

            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hack Club login failed: ' . $e->getMessage());
        }
    }
}   