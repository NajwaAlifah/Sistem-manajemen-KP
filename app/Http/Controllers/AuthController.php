<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Kp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Hash, RateLimiter};

class AuthController extends Controller
{
    // Login memakai nomor induk: NPM (mahasiswa) atau NIP / ID (dosen & admin)
    public function login(Request $request)
    {
        $data = $request->validate(['login' => 'required|string|max:30', 'password' => 'required'],
            ['login.required' => 'NPM / NIP wajib diisi.', 'password.required' => 'Password wajib diisi.']);
        $number = trim($data['login']);
        $key = 'login:' . $number . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'Terlalu banyak percobaan. Coba lagi dalam ' . RateLimiter::availableIn($key) . ' detik.']);
        }

        $user = User::where('nim', $number)->orWhere('nip', $number)->first();
        if (!$user || !$user->is_active || !Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->withInput($request->only('login'))
                ->withErrors(['login' => 'NPM / NIP atau password salah, atau akun tidak aktif.']);
        }

        RateLimiter::clear($key);
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        Kp::audit('login', $user->name);
        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Kp::audit('logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah keluar dari sistem.');
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100', 'phone' => 'nullable|string|max:20']);
        $request->user()->update($data);
        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate(['current_password' => 'required|current_password', 'password' => 'required|min:6|confirmed']);
        $request->user()->update(['password' => $request->password]); // di-hash oleh cast 'hashed'
        return back()->with('success', 'Password berhasil diubah.');
    }
}
