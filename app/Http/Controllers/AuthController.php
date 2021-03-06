<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function getLogin()
    {
        return view('login');
    }

    public function postLogin(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials    = $request->only('email', 'password');
        $user_check     = User::where("email",$credentials["email"])->first();

        if (!$user_check) {
            return redirect('login')->with(['status' => 'Email yang anda masukan salah.']);
        } else {
            $user = Auth::attempt($credentials);
            if ($user) {
                $user = Auth::user();
                return redirect('/admin/dashboard');
            } else {
                return redirect('login')->with('status', 'Email atau Password yang anda masukan salah.');
            }
        }
        
    }

    public function getRegister()
    {
        return view('register');
    }

    public function postRegister(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->count();
        if ($user > 0) {
            return redirect('/register')->with('status', 'Email sudah terdaftar. Silahkan daftar dengan email yang berbeda.');
        }

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
        } catch (Exception $e) {
            return redirect('/register')->with('status', 'Daftar akun gagal. Silahkan coba kembali.');
        }

        return redirect('/login')->with('message', 'Pembuatan akun berhasil. Silahkan login dengan akun anda.');
    }

    public function logout()
        {
            // Session::flush();
            Auth::logout();
            return redirect('/login');
        }

}
