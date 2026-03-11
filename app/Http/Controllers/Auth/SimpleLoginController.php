<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class SimpleLoginController extends Controller
{
    public function show()
    {
        return view('auth.simple-login'); // ✅ your blade file name
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string'],   // username OR email
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (!Auth::attempt([$field => $data['login'], 'password' => $data['password']], $remember)) {
            throw ValidationException::withMessages([
                'login' => 'Invalid username/email or password.',
            ]);
        }

        $request->session()->regenerate();

        // ✅ redirect by role
        $role = strtolower(Auth::user()->role ?? '');

        if ($role === 'student') {
            return redirect()->route('student.register');
        }

        return redirect()->route('dashboard'); // staff/admin
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
