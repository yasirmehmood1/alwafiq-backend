<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Services\GoogleAuthService;
use Illuminate\Support\Facades\Auth;

class GoogleAuthController extends Controller
{

    protected $googleAuthService;
    public function __construct(GoogleAuthService  $googleAuthService)
    {
        $this->googleAuthService = $googleAuthService;
    }

    public function redirectToGoogle()
    {
        return redirect(Socialite::driver('google')->stateless()->redirect()->getTargetUrl());
    }
    
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = $this->googleAuthService->handleGoogleUser($googleUser);
        Auth::login($user);
        $token= $user->createToken('API Token')->plainTextToken;
        return redirect()->away('http://localhost:5173?token=' .$token);
    }
}
