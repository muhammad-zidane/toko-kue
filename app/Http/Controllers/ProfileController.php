<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->load('orders');
        $orderCount    = $user->orders->count();
        $activeOrders  = $user->orders->whereIn('status', ['pending', 'processing'])->count();
        $totalSpent    = $user->orders->where('status', 'completed')->sum('total_price');

        return view('profile.index', compact('user', 'orderCount', 'activeOrders', 'totalSpent'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Require current password confirmation when the email is being changed
        if ($request->input('email') !== $request->user()->email) {
            $request->validate(
                ['confirm_password' => ['required', 'string', 'current_password']],
                ['confirm_password.required'        => 'Konfirmasi password wajib diisi saat mengganti email.',
                 'confirm_password.current_password' => 'Password yang kamu masukkan tidak sesuai.']
            );
        }

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.index')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required'        => 'Password wajib diisi untuk menghapus akun.',
            'password.current_password' => 'Password yang kamu masukkan tidak sesuai.',
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
