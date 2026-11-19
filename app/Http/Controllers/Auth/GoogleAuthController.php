<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
    {
        if (! $request->filled('code')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in was interrupted. Please try again.',
            ]);
        }

        try {
            /** @var \Laravel\Socialite\Two\User $googleUser */
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google sign-in failed. Please try again.',
            ]);
        }

        $googleId = $googleUser->getId();

        if (blank($googleId)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google did not return an account ID.',
            ]);
        }

        $user = User::withTrashed()->where('google_id', $googleId)->first();

        if ($user === null) {
            $email = $googleUser->getEmail();

            if (blank($email) || ($googleUser->getRaw()['email_verified'] ?? false) !== true) {
                return redirect()->route('login')->withErrors([
                    'email' => 'Please use a Google account with a verified email address.',
                ]);
            }

            if (User::withTrashed()->where('email', $email)->exists()) {
                return redirect()->route('login')->withErrors([
                    'email' => 'An account with this email already exists. Use your existing sign-in method.',
                ]);
            }

            $user = new User();
            $user->name = Str::limit($googleUser->getName() ?: $email, 255, '');
            $user->email = $email;
            $user->google_id = $googleId;
            $user->password = Str::random(64);
            $user->role = 'customer';
            $user->status = 'active';

            try {
                $user->save();
            } catch (UniqueConstraintViolationException) {
                return redirect()->route('login')->withErrors([
                    'email' => 'We could not create your account. Try signing in again.',
                ]);
            }
        }

        if ($user->trashed() || $user->status !== 'active') {
            return redirect()->route('login')->withErrors([
                'email' => 'This account is unavailable. Please contact support.',
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }
}