<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CustomerAuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);
        } catch (ValidationException $e) {
            // Return a generic message for email errors to prevent account enumeration
            $errors = $e->errors();
            if (isset($errors['email'])) {
                $errors['email'] = ['This email address cannot be used for registration.'];
            }
            throw ValidationException::withMessages($errors);
        }

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'role'     => 'user',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        try {
            Mail::to($user->email)->queue(new WelcomeEmail($user));
        } catch (\Throwable $e) {
            Log::warning('welcome_email_failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }

        return response()->json(['user' => $user], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        $request->session()->regenerate();

        return response()->json(['user' => Auth::user()]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out.']);
    }

    public function me()
    {
        return response()->json(['user' => auth()->check() ? auth()->user() : null]);
    }
}
