<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Tampilkan form pengaturan akun admin.
     */
    public function index()
    {
        return view('admin.settings');
    }

    /**
     * Simpan perubahan data akun admin (nama, email, password opsional).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = auth()->user();
        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
