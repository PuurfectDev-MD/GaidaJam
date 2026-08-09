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
            
            // Find or create user
            $user = User::firstOrCreate(
                ['email' => $hackclubUser->email],
                [
                    'name' => $hackclubUser->name,
                    'password' => bcrypt(uniqid()), // Random password
                    'slack_id' => $hackclubUser->slack_id,
                ]
            );

            Auth::login($user, true);
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Hack Club login failed: ' . $e->getMessage());
        }
    }
}   