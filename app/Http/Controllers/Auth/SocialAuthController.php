<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect to provider's OAuth consent screen.
     */
    public function redirect(string $provider)
    {
        $allowed = ['google', 'facebook', 'github'];
        abort_unless(in_array($provider, $allowed, true), 404);

        $redirectUrl = config("services.{$provider}.redirect") ?: url("/oauth/{$provider}/callback");
        return Socialite::driver($provider)
            ->redirectUrl($redirectUrl)
            ->redirect();
    }

    /**
     * Handle provider callback.
     */
    public function callback(string $provider): RedirectResponse
    {
        $allowed = ['google', 'facebook', 'github'];
        abort_unless(in_array($provider, $allowed, true), 404);

        try {
            $redirectUrl = config("services.{$provider}.redirect") ?: url("/oauth/{$provider}/callback");
            $socialUser = Socialite::driver($provider)
                ->redirectUrl($redirectUrl)
                ->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Login ' . ucfirst($provider) . ' gagal, coba lagi.');
        }

        // Try to find by email first (simplest linking strategy)
        $user = User::where('email', $socialUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $socialUser->getName() ?: ($socialUser->getNickname() ?: 'Pengguna'),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'user',
            ]);
            // Mark email as verified if provider supplies a valid email
            $user->email_verified_at = now();
            $user->save();
        }

        Auth::login($user, true);

        return redirect()->route('home')->with('login_success', 'Selamat datang, ' . $user->name . '!');
    }

    /**
     * Debug endpoint: show the exact redirect URL used for the provider.
     */
    public function debug(string $provider)
    {
        $allowed = ['google', 'facebook', 'github'];
        abort_unless(in_array($provider, $allowed, true), 404);

        $configRedirect = config("services.{$provider}.redirect");
        $dynamicRedirect = url("/oauth/{$provider}/callback");

        return response()->json([
            'provider' => $provider,
            'config_redirect' => $configRedirect,
            'dynamic_redirect' => $dynamicRedirect,
            'note' => 'Copy the value you intend to use into Google/Facebook/GitHub Authorized Redirect URI exactly.'
        ]);
    }
}
