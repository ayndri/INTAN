<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginCover extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-cover', ['pageConfigs' => $pageConfigs]);
  }

  public function login(Request $request)
  {

    $request->validate([
      'username_or_email' => 'required|string',
      'password' => 'required|string',
    ]);


    $loginType = filter_var($request->input('username_or_email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    $credentials = [
      $loginType => $request->input('username_or_email'),
      'password' => $request->input('password'),
    ];

    try {

      if (Auth::attempt($credentials)) {
        return redirect()->intended('dashboard')->with('success', 'Login berhasil!');
      } else {

        return back()->withErrors([
          'loginError' => 'Username/email atau password salah.',
        ])->withInput();
      }
    } catch (\Exception $e) {

      Log::error('Login failed: ' . $e->getMessage());
      return back()->withErrors([
        'loginError' => 'Terjadi kesalahan saat mencoba login. Silakan coba lagi.',
      ])->withInput();
    }
  }

  public function logout(Request $request)
  {
    try {

      Auth::logout();

      // Regenerate session token to prevent session fixation attacks
      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return redirect('/')->with('status', 'Logout successful');
    } catch (\Exception $e) {

      Log::error('Logout failed: ' . $e->getMessage());
      return redirect()->back()->withErrors(['error' => 'Logout failed. Please try again.']);
    }
  }
}
