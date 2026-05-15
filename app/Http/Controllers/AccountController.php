<?php

namespace App\Http\Controllers;

use App\Rules\StrongPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function showChangePassword(): View
    {
        return view('account.change-password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password'      => ['required', 'string'],
            'password'              => ['required', 'confirmed', new StrongPassword($request->user()->email)],
            'password_confirmation' => ['required', 'string'],
        ], [
            'current_password.required'      => 'Password lama wajib diisi.',
            'password.required'              => 'Password baru wajib diisi.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
            'password_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ]);

        if (! Hash::check($request->current_password, $request->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
        }

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // Keluarkan semua sesi lain yang aktif
        Auth::logoutOtherDevices($request->password);

        return back()->with('status', 'password-updated');
    }
}
