<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = auth()->user()->addresses()->latest()->get();
        return view('account.addresses', compact('addresses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20|regex:/^[0-9\+\-\s]+$/',
            'street'         => 'required|string',
            'rt_rw'          => 'nullable|string|max:20',
            'kelurahan'      => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'nullable|string|max:10',
            'is_default'     => 'nullable|boolean',
        ]);

        $data['user_id'] = auth()->id();

        if (!empty($data['is_default'])) {
            Address::where('user_id', auth()->id())->update(['is_default' => false]);
        }

        // First address is always default
        if (auth()->user()->addresses()->count() === 0) {
            $data['is_default'] = true;
        }

        Address::create($data);

        return back()->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeOwner($address);

        $data = $request->validate([
            'label'          => 'required|string|max:50',
            'recipient_name' => 'required|string|max:255',
            'phone'          => 'required|string|max:20|regex:/^[0-9\+\-\s]+$/',
            'street'         => 'required|string',
            'rt_rw'          => 'nullable|string|max:20',
            'kelurahan'      => 'nullable|string|max:100',
            'kecamatan'      => 'nullable|string|max:100',
            'city'           => 'required|string|max:100',
            'postal_code'    => 'nullable|string|max:10',
        ]);

        $address->update($data);

        return back()->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeOwner($address);
        $address->delete();

        // Set new default if deleted was default
        if ($address->is_default) {
            auth()->user()->addresses()->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Alamat berhasil dihapus.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeOwner($address);

        Address::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Alamat utama diperbarui.');
    }

    private function authorizeOwner(Address $address): void
    {
        if ($address->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
