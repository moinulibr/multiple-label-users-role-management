<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    protected string $provider = 'google';

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver($this->provider)->redirect();
    }

    /**
     * Handle Google OAuth callback.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver($this->provider)->user();
        } catch (Exception $e) {
            return redirect()
                ->route('login')
                ->with('error', 'Authentication failed. Please try again.');
        }

        // Find or create user
        $user = $this->findOrCreateUser($googleUser);

        Auth::login($user);

        return redirect()
            ->intended('/dashboard')
            ->with('status', 'logged-in');
    }

    /**
     * Find existing user or create a new one.
     */
    private function findOrCreateUser($googleUser)
    {
        return User::updateOrCreate(
            [
                'provider_id' => $googleUser->getId(),
                'provider'    => $this->provider,
            ],
            [
                'name'  => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
            ]
        );
    }
}
