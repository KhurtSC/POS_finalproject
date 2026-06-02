<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRoute(Auth::user()->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->with('error', 'The email or password is incorrect.')
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        ActivityLogger::log(
            event: 'login',
            description: Auth::user()->name . ' logged in.',
            subject: Auth::user(),
        );

        return redirect()->intended(route($this->dashboardRoute(Auth::user()->role)));
    }

    public function logout(Request $request)
    {
        ActivityLogger::log(
            event: 'logout',
            description: Auth::user()->name . ' logged out.',
            subject: Auth::user(),
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been signed out.');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role'     => ['required', 'in:admin,cashier'],
        ]);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLogger::log(
            event: 'register',
            description: $user->name . ' registered a new account.',
            subject: $user,
            context: ['role' => $user->role],
        );

        return redirect()->route($this->dashboardRoute($user->role))
            ->with('success', 'Account created successfully.');
    }

    private function dashboardRoute(?string $role): string
    {
        return $role === 'cashier' ? 'cashier.dashboard' : 'admin.dashboard';
    }
}