<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    public function handleGoogleUser(SocialiteUser $googleUser)
    {
        if (!str_ends_with($googleUser->getEmail(), '@gmail.com')) {
            return response()->json(['error' => 'Only Gmail accounts are allowed'], 403);
        }
        $user = User::where('email', $googleUser->getEmail())->first();
        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
            ]);
        } else {
            $user = User::create([
                'google_id' => $googleUser->getId(),
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => bcrypt(str()->random(16))
            ]);
        }

        return $user;
    }
}
