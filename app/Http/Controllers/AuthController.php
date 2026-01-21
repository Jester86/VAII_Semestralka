<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    // Login Methods
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required'
        ]);

        $login = $request->input('login');
        $password = $request->input('password');

        // Attempt login by email
        if (Auth::attempt(['email' => $login, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Attempt login by name
        if (Auth::attempt(['name' => $login, 'password' => $password])) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }

        // Failed login
        return back()->withErrors([
            'login' => 'Invalid login credentials.'
        ])->withInput();
    }


    // Registration Methods
    public function showRegisterForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'user',          // default role
            'reputation' => 0,         // default reputation
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect('/dashboard');
    }
}
